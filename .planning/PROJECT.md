# PuzzleApps CMS Modernization

## What This Is

This project modernizes the existing PHP/XSLT content-management system so it runs on PHP 8.5 and uses PDO instead of its legacy database abstraction layers. It is intended to preserve the CMS's public-site, administration, and installation workflows while supporting MySQL, SQLite, and PostgreSQL.

## Core Value

Keep the existing CMS usable on a supported PHP runtime while removing obsolete database dependencies without changing its essential workflows.

## Requirements

### Validated

- ✓ The repository contains an existing PHP content-management system with public, admin, and installer entry points — existing code; PHP 8.5 behavior not yet verified.
- ✓ The project uses XSLT as its templating engine — documented in the repository README.

### Active

- [ ] The public CMS, admin interface, and installer run on PHP 8.5 or later.
- [ ] CMS database access uses PDO for MySQL, SQLite, and PostgreSQL.
- [ ] ADOdb and PEAR::DB code and drivers are removed from the application.
- [ ] Existing essential CMS workflows and persisted data behavior are preserved during the migration.
- [ ] PHP 8.5 syntax and static checks run during implementation; test suites are deferred until after the milestone.

### Out of Scope

- MSSQL and other database backends — the migration is limited to MySQL, SQLite, and PostgreSQL.
- PHP 5 support or PHP 5 test runs — the target is PHP 8.5+, and the user will run tests after the milestone.

## Context

- The repository README describes a PHP CMS using XSLT and legacy ADOdb or PEAR::DB database abstractions.
- Existing code includes public, admin, and installer entry points, plus a large set of bundled ADOdb drivers.
- The codebase is brownfield and does not yet have a GSD codebase map. The project is initialized with that limitation because mapping was explicitly skipped.
- The user intends to perform the test runs after the migration milestone is complete.

## Constraints

- **Runtime compatibility**: PHP 8.5 or later — PHP 5 is no longer an acceptable target.
- **Database layer**: PDO only — remove ADOdb and PEAR::DB paths.
- **Database compatibility**: MySQL, SQLite, and PostgreSQL — other backend paths are not part of this migration.
- **Validation timing**: Do not run test suites during the migration milestone — the user will run them after completion. PHP 8.5 syntax and static checks may run during implementation.

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Set PHP 8.5 as the minimum runtime | Target a supported PHP 8 release | — Pending |
| Replace ADOdb and PEAR::DB with PDO | Simplify and modernize database access | — Pending |
| Support MySQL, SQLite, and PostgreSQL; defer other backends | User-selected migration scope | — Pending |
| Defer test suites until after the milestone; allow PHP 8.5 syntax/static checks | User will perform test runs after migration completion | — Pending |
| Skip the initial codebase map | User chose to proceed without mapping first | — Pending |

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
*Last updated: 2026-10-28 after initialization*
