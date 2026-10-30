# Project Retrospective

*A living document updated after each milestone. Lessons feed forward into future planning.*

## Milestone: v2.0 — Docker Deployment

**Shipped:** 2026-10-30
**Phases:** 4 | **Plans:** 8 | **Sessions:** not measured

### What Was Built
- PHP 8.5 FPM image and PostgreSQL 18 Compose `publish`/`dev` profiles with persistent volumes.
- Shared-root Nginx routing, a static routing checker, and operator-applied post-install installer restriction instructions.
- First-deployment, installer, volume, backup/restore, upgrade, and secrets documentation.
- PHP 8.5 static syntax evidence and an explicit container-test handoff.

### What Worked
- Static source checks allowed deployment integration work while respecting the no-container/no-test milestone boundary.
- Keeping the `/install/` restriction operator-applied preserved first-run access and avoided claiming live enforcement.
- Reusing the Phase 7 routing checker gave the final static-verification task a focused source contract.

### What Was Inefficient
- The first PHP 8.5 lint exposed legacy syntax in bundled Securimage/phpThumb code that changed PHP was not expected to cover; fixing it was necessary to satisfy the all-tracked-files check.
- Existing plan summaries omitted task counts, so the CLI roll-up needed reconciliation against the plan files.

### Patterns Established
- Record exact static commands and outcomes separately from runtime claims.
- Deployment command examples include adjacent observable failure signals.
- Named-volume first-use seeding and the operator-applied installer restriction are explicit deployment concerns.

### Key Lessons
1. Lint all tracked PHP files when the acceptance criterion covers the repository, not only files changed in the current phase.
2. Static routing and syntax evidence do not establish service health, persistence, installer behavior, or backup/restore success.
3. Keep task counts in plan summaries consistent with plan task declarations to improve milestone reporting.

### Cost Observations
- Model mix: not measured
- Sessions: not measured
- Notable: no test suites, image builds, or container workflows were run; user validation remains pending.

---

## Cross-Milestone Trends

### Process Evolution

| Milestone | Sessions | Phases | Key Change |
|-----------|----------|--------|------------|
| v1.0 | not measured | 4 | PHP 8.5/PDO modernization |
| v2.0 | not measured | 4 | Docker deployment with static-only verification |

### Cumulative Quality

| Milestone | Tests | Coverage | Zero-Dep Additions |
|-----------|-------|----------|-------------------|
| v1.0 | Not recorded | Not recorded | Not recorded |
| v2.0 | Application suites not run | N/A | Not recorded |

### Cross-Milestone Lessons

No lessons are yet verified across both archived milestones. Revisit this
section after the next milestone, when comparable evidence is available.
