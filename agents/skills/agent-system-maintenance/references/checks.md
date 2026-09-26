# Shared checks

`npm run check` is the common entrypoint for Codex, Claude Code and repository
work. CI calls the same Python runner directly so documentation checks do not
need Node dependencies. The runner never installs dependencies or deploys.

```bash
npm run check -- --plan
npm run check
npm run check -- --full
npm run check -- --base origin/main
```

Locally, the default comparison is the merge base with `origin/main`, including
committed branch changes, staged files, unstaged files and untracked files that
Git does not ignore. Renames inspect both paths. Explicit `--base SHA --head SHA`
is for a clean checkout of that head, as in CI; invalid/missing base history
forces full validation. Fetch before comparing with an outdated remote ref.

| Changes | Checks | Automatic deploy after successful main CI |
| --- | --- | --- |
| Documentation, draft content, research data | Architecture, PHP syntax, skill contracts, selector regressions, canon/E3 and German copy guards | No |
| Agent contracts, skill sources/discovery | Baseline plus all skill suites | No |
| Theme, tooling, dependencies, workflows, unknown paths | All existing runtime, browser, PHP-analysis and build gates | Yes |

The exact allowlist and check catalog live in `scripts/check.py`. Do not add a
lightweight exemption without checking consumers and adding a regression case.
An explicit `--full` broadens validation without making a docs-only diff deployable.
CI manual runs force full validation and do not deploy through the CI workflow.
Main runs do not cancel each other. Inside the existing production lock, an
automatic release checks freshly fetched `main`: a docs/skills successor may
proceed, a newer runtime revision supersedes the older release. Explicit manual
deployments and rollbacks keep their selected ref.

All profiles require Python 3, Git, Bash, PHP and ripgrep. Full checks additionally
need PHP >= 7.4 for the existing provisioning/pricing fixtures, installed Node
tooling (`npm ci`), Chromium (`npx playwright install chromium`)
and Composer dependencies (`composer install`). CI installs these only for full
checks and additionally runs actionlint. Local PHPStan can use an existing phar
via `PHPSTAN_PHAR=/absolute/path/phpstan.phar`; CI uses the locked dependency.

Successful commands print compact status and duration. Failed commands print
their complete output and make the runner fail. Every command's full log is
retained in the printed temporary directory. Passing file/schema checks do not
measure model quality or token savings.
