#!/usr/bin/env python3
"""Locally scheduled Postbox adapter between file IPC and its Porter."""

import json
import os
import time
from pathlib import Path

from porter.protocol import package
from porter.tickets import collect, inspect, lodge

MAILWEB = Path(os.environ.get("MAILWEB_FILE_ROOT", "/mailweb-ipc"))
IPC = Path(os.environ.get("PORTER_IPC", "/porter"))

for name in ("outgoing", "incoming", "work"):
    directory = MAILWEB / name
    directory.mkdir(parents=True, exist_ok=True)
    directory.chmod(0o777)

while True:
    for path in sorted((MAILWEB / "outgoing").glob("*.json")):
        request = json.loads(path.read_text())
        work = MAILWEB / "work" / f"{request['id']}.json"
        if not work.exists():
            value = package(
                "postbox",
                "find-me",
                "mailweb.request",
                {"request": request},
                reply_to="postbox",
                ttl=300,
            )
            ticket = lodge(IPC, value)
            work.write_text(
                json.dumps(
                    {
                        "request_id": request["id"],
                        "ticket": ticket["ticket"],
                        "package": value["package"],
                    }
                )
            )
        path.unlink(missing_ok=True)

    for work in sorted((MAILWEB / "work").glob("*.json")):
        value = json.loads(work.read_text())
        status = inspect(IPC, value["ticket"], record=False)
        if status["state"] in {"EXPIRED_OBSERVED", "ABANDONED", "ABANDONED_WITH_RETURN"}:
            work.unlink()
            continue
        if status["state"] != "RETURN_HELD":
            continue
        result = collect(IPC, value["ticket"])
        response = result["package"]["payload"]["response"]
        target = MAILWEB / "incoming" / f"{value['request_id']}.json"
        temporary = target.with_suffix(".tmp")
        temporary.write_text(json.dumps(response))
        temporary.replace(target)
        work.unlink()
    time.sleep(0.025)
