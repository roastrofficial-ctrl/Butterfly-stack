#!/usr/bin/env python3
"""HDBE application adapter for the application-neutral Host Runtime."""
import json
import os
import sys
import time
from pathlib import Path

from harmonic.hdbe import HDBEService, _json
from harmonic.api import HarmonicDBError, ENGINE_PROTOCOL
from porter.protocol import package, validate
from porter.lodgement import atomic_json
from porter.tickets import lodge


ipc = Path(os.getenv("PORTER_IPC", "/porter"))
application = Path(os.getenv("HARMONIC_APPLICATION_DIR", "/data/porter-application"))
application.mkdir(parents=True, exist_ok=True)
service = HDBEService(
    {
        "find_me": "/data/find-me.hws",
        "find_me_private": "/data/find-me-private-symbol.hws",
    }
)


def experimental_crash(point, collection):
    if os.getenv("PORTER_EXPERIMENT_CRASH_POINT") != point:
        return
    marker = application / (collection + "." + point + ".crashed")
    if marker.exists():
        return
    atomic_json(marker, {"collection": collection, "experimental_crash": point})
    raise SystemExit(f"Host Runtime experiment interruption at {point}")


def handle(fact):
    request = validate(fact["package"])
    commit = application / (fact["collection"] + ".json")
    if commit.exists():
        return
    experimental_crash("before_application_dispatch", fact["collection"])
    if request["kind"] != "hdbe.call" or not request.get("reply_to"):
        atomic_json(
            commit,
            {"collection": fact["collection"], "application_state": "DECLINED_KIND"},
        )
        return
    result_path = application / (fact["collection"] + ".result.json")
    ambiguous = application / (fact["collection"] + ".ambiguous.json")
    effect_crash = application / (fact["collection"] + ".after_effect.crashed")
    if effect_crash.exists() and not result_path.exists():
        if not ambiguous.exists():
            atomic_json(
                ambiguous,
                {
                    "collection": fact["collection"],
                    "application_state": "EFFECT_WITHOUT_RECORDED_RESULT",
                    "meaning": "HDBE recovery required; PORTER and Host Runtime cannot decide whether to retry",
                },
            )
        return
    if result_path.exists():
        envelope = json.loads(result_path.read_text())["envelope"]
    else:
        started = time.perf_counter()
        try:
            call = request["payload"]
            result = service.dispatch(call["operation"], call.get("parameters", {}))
            envelope = {
                "ok": True,
                "protocol": ENGINE_PROTOCOL,
                "transport": "PORTER/1",
                "collection_wait_ms": call.get("deposited_at_ms")
                and round(time.time() * 1000 - call["deposited_at_ms"], 2),
                "host_processing_ms": round((time.perf_counter() - started) * 1000, 3),
                "result": _json(result),
            }
        except HarmonicDBError as exc:
            envelope = {
                "ok": False,
                "protocol": ENGINE_PROTOCOL,
                "transport": "PORTER/1",
                "error": exc.as_dict(),
            }
        except Exception as exc:
            envelope = {
                "ok": False,
                "protocol": ENGINE_PROTOCOL,
                "transport": "PORTER/1",
                "error": {"code": "HostFailure", "message": str(exc)},
            }
        experimental_crash("after_effect", fact["collection"])
        atomic_json(
            result_path,
            {
                "collection": fact["collection"],
                "application_state": "HDBE_RESULT_RECORDED",
                "envelope": envelope,
            },
        )
    experimental_crash("after_application_record", fact["collection"])
    draft_path = application / (fact["collection"] + ".return-draft.json")
    if draft_path.exists():
        returned = json.loads(draft_path.read_text())
    else:
        returned = package(
            "harmonicdb",
            "find-me",
            "porter.return",
            {"envelope": envelope},
            in_reply_to=request["package"],
        )
        atomic_json(draft_path, returned)
    experimental_crash("after_return_draft", fact["collection"])
    mapping = ipc / "tickets" / "by-package" / returned["package"]
    if mapping.exists():
        return_ticket = json.loads(
            (ipc / "tickets" / (mapping.read_text().strip() + ".json")).read_text()
        )
    else:
        return_ticket = lodge(ipc, returned)
    experimental_crash("after_return_lodgement", fact["collection"])
    atomic_json(
        commit,
        {
            "collection": fact["collection"],
            "application_state": "RETURN_LODGEMENT_RECORDED",
            "return": returned["package"],
            "return_lodgement": return_ticket["lodgement"],
        },
    )


for line in sys.stdin:
    dispatch = json.loads(line)
    if dispatch.get("contract") != "PORTER-HOST-ADAPTER/1":
        raise SystemExit("unsupported Host Runtime adapter contract")
    handle(dispatch["collection"])
    sys.stdout.write(
        json.dumps(
            {
                "contract": "PORTER-HOST-ADAPTER/1",
                "dispatch": dispatch["dispatch"],
                "runtime_observation": "ADAPTER_RETURNED_CONTROL",
            },
            separators=(",", ":"),
        )
        + "\n"
    )
    sys.stdout.flush()
