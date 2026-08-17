#!/usr/bin/env python3
import json, os, time
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
carriage_root = Path("/evidence/find-me/carriage")
acceptance_root = Path("/evidence/harmonicdb/acceptances")
collection_root = Path("/evidence/harmonicdb/collections/facts")
application_root = Path("/evidence/harmonicdb-data/porter-application")
round_root = Path("/evidence/find-me/rounds")
works = []
for path in work_root.glob("LW-*.json") if work_root.exists() else []:
    try: works.append(json.loads(path.read_text()))
    except Exception: pass
works.sort(key=lambda value: value.get("created_at_ms", 0), reverse=True)
print(f"\nPORTER LODGEMENTS             {len(list(lodgement_root.glob('LG-*.json'))) if lodgement_root.exists() else 0}")
print(f"COLLECTION TICKETS            {len(list(ticket_root.glob('CT-*.json'))) if ticket_root.exists() else 0}")
knowledge = []
for path in carriage_root.glob("PKG-*.json") if carriage_root.exists() else []:
    try: knowledge.append(json.loads(path.read_text()))
    except Exception: pass
knowledge.sort(key=lambda value: (value.get("attempts") or [{}])[-1].get("began_at_ms", 0), reverse=True)
print(f"REMOTE ACCEPTANCE FACTS       {len(list(acceptance_root.glob('PKG-*.json'))) if acceptance_root.exists() else 0}")
print(f"RECIPIENT COLLECTION FACTS    {len(list(collection_root.glob('CL-*.json'))) if collection_root.exists() else 0}")
if knowledge:
    carriage = knowledge[0]
    remote_fact = (acceptance_root / f"{carriage['package']}.json").exists()
    print(f"LATEST CARRIAGE               {carriage['package']} · {len(carriage.get('attempts', []))} attempt(s)")
    print(f"REMOTE REALITY                {'RECIPIENT PORTER ACCEPTED' if remote_fact else 'NOT OBSERVED HERE'}")
    print(f"LOCAL KNOWLEDGE               {carriage.get('knowledge')}")
    if "acceptance_evidence" in carriage:
        print(f"ACCEPTANCE EVIDENCE           {carriage['acceptance_evidence'].get('acceptance')}")
    collection = None
    for path in collection_root.glob("CL-*.json") if collection_root.exists() else []:
        try:
            candidate = json.loads(path.read_text())
            if candidate.get("package", {}).get("package") == carriage["package"]: collection = candidate; break
        except Exception: pass
    if collection:
        processed = (application_root / f"{collection['collection']}.json").exists()
        print(f"CURRENT CUSTODY               RECIPIENT HOST · {collection['collection']}")
        print(f"APPLICATION REALITY           {'RECORDED SEPARATELY' if processed else 'UNKNOWN TO PORTER'}")
        print("PORTER DISPOSITION            NOT REPRESENTED")
    elif remote_fact:
        legacy_collected = (Path("/evidence/harmonicdb/collected") / f"{carriage['package']}.json").exists()
        print(f"CURRENT CUSTODY               {'LEGACY COLLECTION — INDETERMINATE' if legacy_collected else 'RECIPIENT PORTER'}")
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

# A compact custody/resource view: enough to see pressure without pretending
# the inspector is a monitoring system. Canonical facts and projections remain
# visibly separate.
resource_started = time.perf_counter()
resource_roots = {
    "LG FACTS": lodgement_root,
    "AC FACTS": acceptance_root,
    "CL FACTS": collection_root,
    "TICKETS": ticket_root,
    "INBOX PROJECTION": Path("/evidence/harmonicdb/inbox"),
    "COLLECTED PROJECTION": Path("/evidence/harmonicdb/collected"),
    "CARRIAGE KNOWLEDGE": carriage_root,
    "ROUND JOURNALS": round_root,
}
print("\nPORTER RESOURCE VIEW")
for label, root in resource_roots.items():
    files = [path for path in root.rglob("*") if path.is_file()] if root.exists() else []
    size = sum(path.stat().st_size for path in files)
    print(f"{label:28} {len(files):7} files · {size:10} bytes")
print(f"INSPECTOR RESOURCE SCAN        {(time.perf_counter() - resource_started) * 1000:.1f} ms")
