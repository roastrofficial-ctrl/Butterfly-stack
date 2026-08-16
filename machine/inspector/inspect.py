#!/usr/bin/env python3
import json, os
from pathlib import Path
from urllib.request import Request, urlopen

manifest = json.loads(Path(os.getenv("BUTTERFLY_MANIFEST", "/machine/manifest.yml")).read_text())

def online(name, spec):
    url = spec.get("health")
    if not url:
        return "ISOLATED — NO DIRECT ADDRESS" if spec.get("isolation") == "no_direct_address" else "HOST"
    try:
        request = Request(url, method="POST", data=b"{}", headers={"Content-Type": "application/json"}) if name == "database" else Request(url)
        with urlopen(request, timeout=30) as response:
            return "ONLINE" if response.status < 400 else "OFFLINE"
    except Exception:
        return "OFFLINE"

print("BUTTERFLY/" + manifest["machine"]["generation"])
print()
for role, spec in manifest["systems"].items():
    if spec["status"] == "counterfactual":
        print(f"{role.upper():18} {spec['implementation'].upper():28} {online(role, spec)}")
    elif spec["status"] == "offline":
        print(f"{role.upper():18} {spec['implementation'].upper():28} OFFLINE (NOT REQUIRED)")
hosts = [spec for spec in manifest["systems"].values() if spec["status"] == "host_dependency"]
counterfactual = [spec for spec in manifest["systems"].values() if spec["status"] == "counterfactual"]
print(f"\nHOST DEPENDENCIES              {len(hosts)}")
print(f"COUNTERFACTUAL SYSTEMS         {len(counterfactual)}")
print("\nFIND ME                        READY")

work_root = Path("/evidence/work/location")
ticket_root = Path("/evidence/find-me/tickets")
lodgement_root = Path("/evidence/find-me/lodgements/lodged")
works = []
for path in work_root.glob("LW-*.json") if work_root.exists() else []:
    try: works.append(json.loads(path.read_text()))
    except Exception: pass
works.sort(key=lambda value: value.get("created_at_ms", 0), reverse=True)
print(f"\nPORTER LODGEMENTS             {len(list(lodgement_root.glob('LG-*.json'))) if lodgement_root.exists() else 0}")
print(f"COLLECTION TICKETS            {len(list(ticket_root.glob('CT-*.json'))) if ticket_root.exists() else 0}")
if works:
    work = works[0]
    print(f"LATEST JOURNEY                {work.get('journey')} · {work.get('stage')}")
    rounds = work.get("rounds", [])
    print(f"HOST ROUNDS                   {len(rounds)}")
    if rounds:
        last = rounds[-1]
        observation = (last.get("observations") or [last])[0]
        print(f"LAST ROUND                    {observation.get('state', observation.get('observed_state'))} · {last.get('round')} · initiated by {last.get('initiated_by')}")
        if "observation_latency_ms" in observation:
            print(f"CARRIAGE LATENCY              {observation.get('carriage_latency_ms', 'unknown')} ms")
            print(f"OBSERVATION LATENCY           {observation['observation_latency_ms']} ms")
