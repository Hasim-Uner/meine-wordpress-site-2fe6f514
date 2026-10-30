#!/usr/bin/env python3
"""Write the deployment proof that the homepage Pruefstand shows.

Runs in deploy.yml after the build, next to .nexus-deploy-sha. Reads the
merged pull request behind the deployed commit and the check runs of that
pull request's head commit, then writes one small JSON file into the theme
build. Stdlib only.

On any error nothing is written and the script still exits 0: the homepage
then shows commit and deploy time from the marker alone, never a guessed
count. A missing proof must not stop a production deploy.
"""

import datetime
import json
import os
import subprocess
import sys
import urllib.parse
import urllib.request

API = os.environ.get("GITHUB_API_URL", "https://api.github.com")
NOT_RUN = {"skipped", "neutral", "stale"}


def get(path):
    request = urllib.request.Request(
        API + path,
        headers={
            "Accept": "application/vnd.github+json",
            "Authorization": "Bearer " + os.environ["GH_TOKEN"],
            "X-GitHub-Api-Version": "2022-11-28",
        },
    )
    with urllib.request.urlopen(request, timeout=15) as response:
        return json.load(response)


def proof_for(repo, sha, now):
    pulls = get(f"/repos/{repo}/commits/{sha}/pulls")
    merged = [p for p in pulls if p.get("merged_at") and (p.get("base") or {}).get("ref") == "main"]
    proof = {"sha": sha, "deployed_at": now}
    if not merged:
        return proof

    pull = merged[0]
    proof["pull_request"] = int(pull["number"])
    head = urllib.parse.quote(pull["head"]["sha"])
    runs = get(f"/repos/{repo}/commits/{head}/check-runs?per_page=100&filter=latest").get("check_runs", [])
    ran = [r for r in runs if r.get("status") == "completed" and r.get("conclusion") not in NOT_RUN]
    if ran:
        proof["checks"] = {
            "passed": sum(1 for r in ran if r.get("conclusion") == "success"),
            "total": len(ran),
        }
    return proof


def main():
    if len(sys.argv) != 2:
        print("usage: stamp-deploy-proof.py OUTPUT_JSON", file=sys.stderr)
        return 2

    target = sys.argv[1]
    try:
        repo = os.environ["GITHUB_REPOSITORY"]
        sha = subprocess.check_output(["git", "rev-parse", "HEAD"], text=True).strip().lower()
        now = datetime.datetime.now(datetime.timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
        proof = proof_for(repo, sha, now)
    except Exception as error:  # noqa: BLE001 - any failure means: no proof file
        print(f"::warning title=Deploy proof skipped::{error}")
        return 0

    temporary = target + ".tmp"
    with open(temporary, "w", encoding="utf-8") as handle:
        json.dump(proof, handle, separators=(",", ":"))
        handle.write("\n")
    os.replace(temporary, target)
    print(f"Deploy proof: {json.dumps(proof)}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
