#!/usr/bin/env python3
"""Networkless HarmonicDB Host: it only collects from and returns to its Porter."""
import json
import os
import time
from pathlib import Path

from harmonic.hdbe import HDBEService, _json
from harmonic.api import HarmonicDBError, ENGINE_PROTOCOL
from porter.protocol import atomic_write, package, validate

ipc=Path(os.getenv("PORTER_IPC","/porter"));inbox=ipc/"inbox";outgoing=ipc/"outgoing";collected=ipc/"collected"
for folder in (inbox,outgoing,collected):folder.mkdir(parents=True,exist_ok=True)
service=HDBEService({"find_me":"/data/find-me.hws","find_me_private":"/data/find-me-private-symbol.hws"})
(ipc/"host.ready").write_text("HarmonicDB waits to COLLECT; it exposes no network listener.\n")
while True:
    for path in sorted(inbox.glob("PKG-*.json")):
        claimed=collected/(path.stem+".collecting")
        try:path.rename(claimed)
        except FileNotFoundError:continue
        request=validate(json.loads(claimed.read_text()))
        if request["kind"]!="hdbe.call" or not request.get("reply_to"):
            claimed.rename(collected/(path.stem+".refused"));continue
        started=time.perf_counter()
        try:
            call=request["payload"];result=service.dispatch(call["operation"],call.get("parameters",{}))
            envelope={"ok":True,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","collection_wait_ms":call.get("deposited_at_ms") and round(time.time()*1000-call["deposited_at_ms"],2),"host_processing_ms":round((time.perf_counter()-started)*1000,3),"result":_json(result)}
        except HarmonicDBError as exc:envelope={"ok":False,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","error":exc.as_dict()}
        except Exception as exc:envelope={"ok":False,"protocol":ENGINE_PROTOCOL,"transport":"PORTER/1","error":{"code":"HostFailure","message":str(exc)}}
        atomic_write(outgoing,package("harmonicdb","find-me","porter.return",{"envelope":envelope},in_reply_to=request["package"]))
        claimed.rename(collected/(path.stem+".json"))
    time.sleep(.05)
