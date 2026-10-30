# Static verification and container-test handoff

This report separates source/config evidence from runtime evidence. Static
verification was performed on the checked-out source; container and application
scenarios below are explicitly **not run** and remain for the operator after
the migration milestone.

## Static verification performed

### PHP 8.5 syntax

PHP version: **8.5.12**. All **230 Git-tracked PHP files** passed `php -l`.
The check also surfaced obsolete curly-brace string offsets and an unreachable
`break` in bundled Securimage/phpThumb code; those syntax blockers were
corrected before the successful run.

```sh
php -r 'if (PHP_MAJOR_VERSION !== 8 || PHP_MINOR_VERSION !== 5) { fwrite(STDERR, "Expected PHP 8.5.x; found " . PHP_VERSION . PHP_EOL); exit(1); } echo "PHP " . PHP_VERSION . PHP_EOL;' &&
git ls-files -z -- '*.php' | xargs -0 -n 1 php -l
```

Fails when: the interpreter is not PHP 8.5.x, any tracked PHP file reports a
parse error, or the command exits nonzero.

### Nginx routing source check

The Phase 7 standard-library checker passed. It checks shared document roots,
development bind mounts, FastCGI routing, CMS rewrite mappings, missing PHP
scripts, and dotfile handling. This is a static source/config check, not a live
Nginx request.

```sh
python3 docker/nginx/check-routing.py
```

Fails when: the checker exits nonzero or emits a `FAIL` for a routing,
document-root, FastCGI, rewrite, missing-script, or dotfile contract.

### Static deployment-source review

- `Dockerfile` uses `php:8.5-fpm-trixie`, installs `pdo_pgsql`, `xsl`, `gd`,
  and `mbstring`, uses `/var/www/html`, and runs PHP-FPM as `www-data` on
  port 9000.
- `docker-compose.yml` defines PostgreSQL 18 with a `pg_isready` healthcheck
  and a `pgdata` volume. The `publish` app waits for database health; Nginx
  publishes `${HTTP_PORT:-8080}` while PostgreSQL has no host-published port.
  The `dev` profile bind-mounts the source tree. Compose declares the named
  application volumes and seeds them from image content only on first use.
- `docker/nginx.Dockerfile` uses `nginx:stable-alpine`, copies the application
  into `/var/www/html`, and installs the project Nginx configuration.
- `docker/nginx/default.conf` serves from `/var/www/html`, tries real files
  and directories before the CMS fallback, checks that PHP scripts exist
  before forwarding to `app:9000`, and applies the `.html`, `/gallery/`, and
  `/media` rewrites. Dotfiles are denied except for `/.well-known/`. The
  default configuration does not restrict `/install/`.
- Compose supplies database credentials through environment variables; this
  source configuration does not use a Docker secret store.

This is a **source/config only** review. No Compose runtime validation, Nginx
runtime, image build, container start, or application test suite was performed.
Static checks do not prove service health, HTTP behavior, persistence, or
backup/restore behavior.

## Post-milestone container scenarios (not run)

Run these scenarios after the migration milestone in an isolated deployment.
Use [DEPLOYMENT.md](../DEPLOYMENT.md) for the operator procedures. Record actual
outcomes; none of the expected results below should be read as already verified.

1. **Clean `publish` deployment and database readiness**
   - Expected: with a fresh deployment and valid environment settings,
     PostgreSQL becomes healthy, the application starts after the database,
     and Nginx serves the installer on the configured port.
   - Fails when: the database remains unhealthy, a service exits or cannot
     connect, or the configured HTTP endpoint cannot be reached.
2. **Six-step installer**
   - Expected: `/install/` loads; step 2 detects the Compose-provided database
     settings; step 3 creates the schema; the remaining platform and
     superuser steps complete and provide the admin link.
   - Fails when: environment settings are not detected, schema creation
     errors, any step cannot advance, or the completed account cannot access
     the admin interface.
3. **Static and CMS routing**
   - Expected: static assets load; `.html`, `/gallery/`, and `/media` requests
     reach their corresponding CMS routes; an existing PHP script is handled
     by PHP-FPM; a missing PHP script returns 404; dotfiles are denied except
     for `/.well-known/`.
   - Fails when: any route reaches the wrong handler, an existing PHP file
     fails to execute, a missing script is forwarded, a dotfile is exposed,
     or an expected static file cannot be served.
4. **Persistence after service recreation**
   - Expected: PostgreSQL data, uploaded files, logs, temporary data, generated
     configuration, and any changed CSS/images/XSLT remain attached to their
     named volumes after services are recreated.
   - Fails when: data disappears, a volume is replaced unexpectedly, or
     generated configuration or content becomes unavailable.
5. **Backup and restore**
   - Expected: following the deployment guide restores PostgreSQL data,
     uploads, and generated configuration to an isolated target; verify the
     restored site and data before directing users to it.
   - Fails when: a backup is empty or errors, an import or extraction fails,
     or restored data/configuration is missing or inconsistent.
6. **Application upgrade and PostgreSQL major upgrade**
   - Expected: an application upgrade preserves the existing named volumes.
     A PostgreSQL major-version change follows a separate, deliberate data
     upgrade procedure with a verified backup.
   - Fails when: an application upgrade loses persistent data, or a major
     version change is attempted by changing only the image tag.
7. **Secrets handling**
   - Expected: the local `.env` uses a replaced placeholder, remains ignored
     by Git, is access-restricted on the Compose host, and is not exposed in
     logs; `.env.example` remains trackable.
   - Fails when: a real credential is committed or logged, the example file is
     ignored, or the application cannot connect with the configured
     credentials.
8. **Post-install `/install/` restriction**
   - Expected: `/install/` remains available for setup until the operator
     applies the restriction in
     [docker/nginx/INSTALLER.md](nginx/INSTALLER.md) and activates the updated
     Nginx configuration; afterward it returns 404.
   - Fails when: the restriction is presumed automatic, the updated Nginx
     configuration is not active, or `/install/` remains accessible after
     the operator change.

## Unresolved probe results and flagged assumption

Static evidence does not resolve the supplied spec-less probes:

| Requirement | Category | Status | Verification | Resolution | Reason |
|---|---|---|---|---|---|
| DOC-01 | unclassified | unresolved | null | null | null |
| VER-01 | unclassified | unresolved | null | null | null |

**Flagged assumption:** do not describe the `/install/` restriction as
automatic or imply that container tests have run. The operator must apply and
verify the restriction, and the scenarios above remain unrun.
