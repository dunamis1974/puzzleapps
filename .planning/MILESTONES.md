# Milestones

## v2.0 Docker Deployment (Shipped: 2026-10-30)

**Delivered:** A PHP 8.5 Docker deployment stack with PostgreSQL 18, Nginx routing, operator documentation, and static verification.

**Phases completed:** 5-8 (4 phases, 8 plans, 16 plan tasks)

**Key accomplishments:**
- PHP 8.5 FPM image and restricted Docker build context are defined for the CMS.
- Compose dev and publish profiles now run PostgreSQL 18, health-gated PHP-FPM, and Nginx with persisted CMS data and nginx-only HTTP publishing.
- Shared-root Nginx routing now serves CMS rewrites safely, with an explicit post-install installer restriction procedure.
- Operators have a first-deployment, installer, persistence, backup/restore, upgrade, and secrets guide.
- PHP 8.5 lint passed for all 230 tracked PHP files; the static Nginx routing checker passed and the container-test handoff is documented.

**Stats:**
- 23 tracked files changed; 943 insertions and 51 deletions across `v1.0..HEAD` (PHP: +201/-50).
- 4 phases, 8 plans, 16 plan tasks.
- Timeline: 2026-10-28 to 2026-10-30 (1 elapsed day).

**Git range:** `66c6242` → `8193486`

**Closeout type:** `override_closeout`

**Known verification overrides:** Phases 7 and 8 have no GSD verification reports. Their static checks passed, but runtime/container behavior remains unverified and is deferred to the user. The user elected to proceed without a separate milestone audit; the pre-close artifact audit reported all artifact types clear.

**Task-count note:** The CLI summary roll-up reported 8 because several existing summaries omit task counts; the 16 count above comes from the eight plan files.

**What's next:** Run the deferred scenarios in `docker/TEST-HANDOFF.md` and define the next milestone with `/gsd-new-milestone`.

---

## 1.0 PHP 8.5 and PDO Modernization (Shipped: 2026-10-28)

**Phases completed:** 4 phases, 17 plans, 0 tasks

**Key accomplishments:**
- database configuration selects mysql, sqlite or pgsql and reports unsupported engines or unavailable PDO drivers explicitly.
- consistent API for single-row, multi-row, scalar, mutation and error behavior.
- engine differences isolated behind dialects.

---
