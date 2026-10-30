# PuzzleApps CMS Modernization

## What This Is

This project modernizes the existing PHP/XSLT content-management system so it runs on PHP 8.5 and uses PDO instead of its legacy database abstraction layers. It is intended to preserve the CMS's public-site, administration, and installation workflows while supporting MySQL, SQLite, and PostgreSQL.

## Current State

**Shipped:** v2.0 Docker Deployment (2026-10-30).

The project now has a PHP 8.5 FPM image, PostgreSQL 18 Compose `publish` and
`dev` profiles, durable named volumes, Nginx CMS routing, an operator
deployment guide, and a static verification handoff. PHP 8.5.12 syntax lint
passed for all 230 tracked PHP files and the Nginx routing source checker
passed. No application test suite, image build, container, or live Nginx
workflow was run; runtime compatibility and user-facing behavior remain
unverified.

<details>
<summary>Shipped milestone scope: v2.0 Docker Deployment</summary>

**Goal:** Containerize PuzzleApps so it can be deployed quickly with docker compose on PostgreSQL.

**Target features:**
- Dockerfile for PHP 8.5 (php-fpm with pdo_pgsql) plus an nginx web container
- docker-compose.yml with app, nginx and PostgreSQL services and persistent volumes
- Environment-driven configuration and a documented installer path against the Postgres service
- .dockerignore and deployment docs

</details>

## Core Value

Keep the existing CMS usable on a supported PHP runtime while removing obsolete database dependencies without changing its essential workflows.

## Requirements

### Validated

- ✓ The repository contains an existing PHP content-management system with public, admin, and installer entry points — PHP 8.5 syntax passes for all tracked PHP files; runtime behavior remains unverified.
- ✓ The project uses XSLT as its templating engine — documented in the repository README.
- ✓ PHP 8.5 FPM image, PostgreSQL 18 Compose profiles, Nginx routing sources, operator deployment docs, and static verification handoff — v2.0 (runtime behavior remains unverified).
- ✓ ADOdb and PEAR::DB paths were removed from the application — v1.0.
- ✓ PHP 8.5 syntax and static source checks ran during implementation; container tests were deferred — v2.0.

### Active

- [ ] Verify the public CMS, admin interface, and installer run on PHP 8.5 or later using the post-milestone test handoff.
- [ ] Verify CMS database behavior with PDO on MySQL, SQLite, and PostgreSQL.
- [ ] Verify existing essential CMS workflows and persisted data behavior are preserved.
- [ ] Run the deferred application and container scenarios in `docker/TEST-HANDOFF.md`; resolve any findings.

### Out of Scope

- MSSQL and other database backends — the migration is limited to MySQL, SQLite, and PostgreSQL.
- PHP 5 support or PHP 5 test runs — the target is PHP 8.5+, and the user will run tests after the milestone.

## Context

- v2.0 shipped the PHP 8.5/PostgreSQL 18 Docker deployment path and operator documentation.
- PHP 8.5 syntax and static Nginx routing checks passed; runtime behavior and container scenarios remain unverified.
- `docker/TEST-HANDOFF.md` records the deferred user-run scenarios and their expected outcomes.
- The codebase is brownfield and does not yet have a GSD codebase map. The project is initialized with that limitation because mapping was explicitly skipped.
- The user intends to perform test runs after the migration milestone is complete.

## Constraints

- **Runtime compatibility**: PHP 8.5 or later — PHP 5 is no longer an acceptable target.
- **Database layer**: PDO only — remove ADOdb and PEAR::DB paths.
- **Database compatibility**: MySQL, SQLite, and PostgreSQL — other backend paths are not part of this migration.
- **Validation timing**: No test suites or containers were run during v2.0; the user-run runtime validation remains pending. PHP 8.5 syntax and static source checks were allowed and passed.

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Set PHP 8.5 as the minimum runtime | Target a supported PHP 8 release | ✓ Syntax-verified; runtime pending |
| Replace ADOdb and PEAR::DB with PDO | Simplify and modernize database access | ✓ Legacy paths removed; backend runtime pending |
| Support MySQL, SQLite, and PostgreSQL; defer other backends | User-selected migration scope | — Runtime validation pending |
| Defer test suites until after the milestone; allow PHP 8.5 syntax/static checks | User will perform test runs after migration completion | ✓ Followed; user-run verification remains |
| Structure the migration as horizontal layers | Establish runtime, database, and schema foundations before workflow integration | ✓ Completed across v1.0 and v2.0 |
| Skip the initial codebase map | User chose to proceed without mapping first | — Accepted; map remains unavailable |

## Next Milestone Goals

- Run the deferred application and container scenarios and resolve any resulting issues.
- Validate the supported PDO database backends and essential CMS workflows at runtime.
- Define scope through `/gsd-new-milestone`; do not infer runtime success from static checks.

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-10-30 after v2.0 milestone*
