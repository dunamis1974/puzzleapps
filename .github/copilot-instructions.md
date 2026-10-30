<!-- GSD:project-start source:PROJECT.md -->

## Project

**PuzzleApps CMS Modernization**

This project modernizes the existing PHP/XSLT content-management system so it runs on PHP 8.5 and uses PDO instead of its legacy database abstraction layers. It is intended to preserve the CMS's public-site, administration, and installation workflows while supporting MySQL, SQLite, and PostgreSQL.

**Core Value:** Keep the existing CMS usable on a supported PHP runtime while removing obsolete database dependencies without changing its essential workflows.

### Constraints

- **Runtime compatibility**: PHP 8.5 or later — PHP 5 is no longer an acceptable target.
- **Database layer**: PDO only — remove ADOdb and PEAR::DB paths.
- **Database compatibility**: MySQL, SQLite, and PostgreSQL — other backend paths are not part of this migration.
- **Validation timing**: Do not run test suites during the migration milestone — the user will run them after completion. PHP 8.5 syntax and static checks may run during implementation.

<!-- GSD:project-end -->

<!-- GSD:stack-start source:research/STACK.md -->

## Technology Stack

## Recommendation

### Runtime and drivers

| Technology | Version / requirement | Purpose | Rationale |
|---|---|---|---|
| PHP | 8.5 or later | Application runtime | Matches PROJECT.md. The official support table lists PHP 8.5 active support through 2027-12-31 and security support through 2029-12-31. |
| PDO | Bundled/installed PHP extension | Common connection and statement API | First-party, stable API; drivers are separate extensions and must be present in deployment. |
| PDO_MYSQL | Built for/loaded into PHP | MySQL access | Uses mysqlnd by default; DSN supports host, port, dbname, and connection charset. |
| PDO_SQLITE | Built for/loaded into PHP | SQLite access | Enabled by default in PHP builds unless disabled; PHP 8.5 requires SQLite library 3.7.17 or newer. |
| PDO_PGSQL | Built for/loaded into PHP | PostgreSQL access | Requires libpq; PHP 8.4+ documents libpq 10.0 or newer. |

## Connection and PDO policy

## Safe replacement of ADOdb / PEAR::DB

- `core/config/conf.d/database.conf.php` selects ADOdb, and `core/modules/database/database.module.php` builds legacy URL-style DSNs. It has a partial `pdodb` branch, but this is not the active default.
- `core/modules/database/drivers/pdodb.driver.php` is a prototype: its constructor is named `DATABASE()` rather than `__construct()`, query methods execute interpolated SQL, it does not configure connection options, and `GetOne()` assumes a particular fetch shape. PHP 8 no longer treats same-name class methods as constructors, so this is not a ready PHP 8.5 adapter.
- Application callers use ADOdb-style `GetAll`/`GetOne` and SQL assembled with `escape_sql()`. The MySQL, PostgreSQL, and SQLite helper files call legacy `mysql_escape_string()`, `pg_escape_string()`, and `sqlite_escape_string()`. The installer separately checks removed extension functions and creates schema through `ADONewConnection()` and ADOdb XML schema support.

## Do not use

- **ADOdb, PEAR::DB, raw `mysql_*` / `pg_*` / old SQLite APIs, mysqli, or a second DBAL.** They conflict with the PDO-only project constraint and preserve divergent code paths.
- **Hand-built SQL escaping (`escape_sql`, `PDO::quote`) as the primary write API.** Prepared statements are the standard for values; escaping is easy to mismatch with driver and encoding.
- **A universal PDO configuration that assumes all attributes work on every driver.** In particular, `ATTR_EMULATE_PREPARES` is documented for MySQL/OCI/Firebird, not as a portable all-driver setting.
- **Unbounded SQL portability assumptions.** PDO does not translate vendor SQL, identifier syntax, auto-increment behavior, or schema definitions.
- **Committed credentials, DSN values built directly from installer POST data, or production display of SQL/debug details.** Validate driver selection and configuration, and keep secrets outside source control and user-facing errors.

## Sources

- [Supported PHP Versions](https://www.php.net/supported-versions.php) — PHP 8.5 release and support dates.
- [PDO overview](https://www.php.net/manual/en/book.pdo.php), [PDO constructor](https://www.php.net/manual/en/pdo.construct.php), [PDO error handling](https://www.php.net/manual/en/pdo.error-handling.php), [PDO attributes](https://www.php.net/manual/en/pdo.setattribute.php).
- [Prepared statements](https://www.php.net/manual/en/pdo.prepare.php), [execute parameters](https://www.php.net/manual/en/pdostatement.execute.php), [transactions](https://www.php.net/manual/en/pdo.transactions.php), [last insert ID](https://www.php.net/manual/en/pdo.lastinsertid.php).
- [PDO_MYSQL installation and DSN](https://www.php.net/manual/en/ref.pdo-mysql.php), [MySQL DSN](https://www.php.net/manual/en/ref.pdo-mysql.connection.php), [MySQL character sets](https://www.php.net/manual/en/mysqlinfo.concepts.charset.php).
- [PDO_SQLITE requirements](https://www.php.net/manual/en/ref.pdo-sqlite.php), [SQLite DSN](https://www.php.net/manual/en/ref.pdo-sqlite.connection.php).
- [PDO_PGSQL requirements](https://www.php.net/manual/en/ref.pdo-pgsql.php), [PostgreSQL DSN](https://www.php.net/manual/en/ref.pdo-pgsql.connection.php).
- [PHP 8.0 incompatible changes](https://www.php.net/manual/en/migration80.incompatible.php) — same-name class methods are no longer constructors.

<!-- GSD:stack-end -->

<!-- GSD:conventions-start source:CONVENTIONS.md -->

## Conventions

Conventions not yet established. Will populate as patterns emerge during development.
<!-- GSD:conventions-end -->

<!-- GSD:architecture-start source:ARCHITECTURE.md -->

## Architecture

Architecture not yet mapped. Follow existing patterns found in the codebase.
<!-- GSD:architecture-end -->

<!-- GSD:skills-start source:skills/ -->

## Project Skills

No project skills found. Add skills to any of: `.github/skills/`, `.agents/skills/`, `.cursor/skills/`, `.github/skills/`, or `.codex/skills/` with a `SKILL.md` index file.
<!-- GSD:skills-end -->

<!-- GSD:workflow-start source:GSD defaults -->

## GSD Workflow Enforcement

Before using Edit, Write, or other file-changing tools, start work through a GSD command so planning artifacts and execution context stay in sync.

Use these entry points:
- `/gsd-fast` for a trivial task inline, with no subagents and no PLAN.md
- `/gsd-quick` for small fixes, doc updates, and ad-hoc tasks
- `/gsd-debug` for investigation and bug fixing
- `/gsd-execute-phase` for planned phase work

Do not make direct repo edits outside a GSD workflow unless the user explicitly asks to bypass it.
<!-- GSD:workflow-end -->

<!-- GSD:profile-start -->

## Developer Profile

> Profile not yet configured. Run `/gsd-profile-user` to generate your developer profile.
> This section is managed by `generate-claude-profile` -- do not edit manually.
<!-- GSD:profile-end -->
