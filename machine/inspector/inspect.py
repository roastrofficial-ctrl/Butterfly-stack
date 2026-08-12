#!/usr/bin/env python3
import json, os
from pathlib import Path
from urllib.request import Request, urlopen

manifest = json.loads(Path(os.getenv("BUTTERFLY_MANIFEST", "/machine/manifest.yml")).read_text())

def online(name, spec):
    url = spec.get("health")
    if not url:
        return "HOST"
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
hosts = [spec for spec in manifest["systems"].values() if spec["status"] == "host_dependency"]
counterfactual = [spec for spec in manifest["systems"].values() if spec["status"] == "counterfactual"]
print(f"\nHOST DEPENDENCIES              {len(hosts)}")
print(f"COUNTERFACTUAL SYSTEMS         {len(counterfactual)}")
print("\nFIND ME                        READY")
