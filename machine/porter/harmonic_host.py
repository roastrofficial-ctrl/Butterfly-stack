#!/usr/bin/env python3
"""Networkless HarmonicDB Host: it only collects from and returns to its Porter."""
import json
import os
import time
from pathlib import Path

from harmonic.hdbe import HDBEService, _json
from harmonic.api import HarmonicDBError, ENGINE_PROTOCOL
from porter.protocol import package, validate
from porter.custody import collect_package, recover_collections
from porter.lodgement import atomic_json
from porter.tickets import lodge

ipc=Path(os.getenv("PORTER_IPC","/porter"));inbox=ipc/"inbox";outgoing=ipc/"outgoing";collected=ipc/"collected";application=Path("/data/porter-application")
for folder in (inbox,outgoing,collected,application):folder.mkdir(parents=True,exist_ok=True)
service=HDBEService({"find_me":"/data/find-me.hws","find_me_private":"/data/find-me-private-symbol.hws"})
def record(event_type,package_id,details=None):
    with (ipc/"host-events.jsonl").open("a") as stream:stream.write(json.dumps({"event":event_type,"at_ms":int(time.time()*1000),"host":"harmonicdb","package":package_id,**({"details":details} if details else {})},separators=(",",":"))+"\n");stream.flush();os.fsync(stream.fileno())
(ipc/"host.ready").write_text("HarmonicDB waits to COLLECT; it exposes no network listener.\n")
def experimental_crash(point,collection):
    if os.getenv("PORTER_EXPERIMENT_CRASH_POINT")!=point:return
    marker=application/(collection+"."+point+".crashed")
    if marker.exists():return
    atomic_json(marker,{"collection":collection,"experimental_crash":point})
    raise SystemExit(f"Generation VI interruption at {point}")
while True:
    recover_collections(ipc)
    candidates={path.stem for path in inbox.glob("PKG-*.json")}
    for path in (ipc/"collections"/"facts").glob("CL-*.json"):
        fact=json.loads(path.read_text())
        if fact.get("collector")=="harmonicdb":candidates.add(fact["package"]["package"])
    for package_id in sorted(candidates):
        fact=collect_package(ipc,package_id,"harmonicdb")
        request=validate(fact["package"]);commit=application/(fact["collection"]+".json")
        if commit.exists():continue
        record("RECIPIENT_COLLECTED",request["package"],{"collection":fact["collection"]})
        crash=ipc/"generation5.collection-crash"
        if os.getenv("PORTER_EXPERIMENT_CRASH_AFTER_COLLECTION_ONCE")=="1" and not crash.exists():
            crash.write_text(fact["collection"]+"\n")
            raise SystemExit("Generation V interruption after Collection threshold, before HDBE processing")
        experimental_crash("after_read",fact["collection"])
        if request["kind"]!="hdbe.call" or not request.get("reply_to"):
            atomic_json(commit,{"collection":fact["collection"],"application_state":"DECLINED_KIND"});continue
        result_path=application/(fact["collection"]+".result.json")
        ambiguous=application/(fact["collection"]+".ambiguous.json")
        effect_crash=application/(fact["collection"]+".after_effect.crashed")
        if effect_crash.exists() and not result_path.exists():
            if not ambiguous.exists():atomic_json(ambiguous,{"collection":fact["collection"],"application_state":"EFFECT_WITHOUT_RECORDED_RESULT","meaning":"HDBE recovery required; PORTER cannot decide whether to retry"})
            continue
        if result_path.exists():envelope=json.loads(result_path.read_text())["envelope"]
        else:
            started=time.perf_counter()
            try:
                call=request["payload"];result=service.dispatch(call["operation"],call.get("parameters",{}))
                envelope={"ok":True,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","collection_wait_ms":call.get("deposited_at_ms") and round(time.time()*1000-call["deposited_at_ms"],2),"host_processing_ms":round((time.perf_counter()-started)*1000,3),"result":_json(result)}
            except HarmonicDBError as exc:envelope={"ok":False,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","error":exc.as_dict()}
            except Exception as exc:envelope={"ok":False,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","error":{"code":"HostFailure","message":str(exc)}}
            experimental_crash("after_effect",fact["collection"])
            atomic_json(result_path,{"collection":fact["collection"],"application_state":"HDBE_RESULT_RECORDED","envelope":envelope})
        experimental_crash("after_application_record",fact["collection"])
        draft_path=application/(fact["collection"]+".return-draft.json")
        if draft_path.exists():returned=json.loads(draft_path.read_text())
        else:
            returned=package("harmonicdb","find-me","porter.return",{"envelope":envelope},in_reply_to=request["package"]);atomic_json(draft_path,returned)
        experimental_crash("after_return_draft",fact["collection"])
        mapping=ipc/"tickets"/"by-package"/returned["package"]
        if mapping.exists():return_ticket=json.loads((ipc/"tickets"/(mapping.read_text().strip()+".json")).read_text())
        else:return_ticket=lodge(ipc,returned)
        experimental_crash("after_return_lodgement",fact["collection"])
        atomic_json(commit,{"collection":fact["collection"],"application_state":"RETURN_LODGEMENT_RECORDED","return":returned["package"],"return_lodgement":return_ticket["lodgement"]});record("RETURN_LODGED",returned["package"],{"in_reply_to":request["package"],"collection":fact["collection"]})
    time.sleep(.05)
