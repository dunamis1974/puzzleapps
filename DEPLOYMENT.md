# Docker deployment

This guide covers a first deployment using the `publish` Compose profile. The
stack runs PHP-FPM, Nginx, and PostgreSQL on a private Compose network. Only
Nginx publishes a host port; PostgreSQL is not published to the host by
default.

## First deployment

1. Copy the example environment file:

   ```sh
   cp .env.example .env
   ```

   Fails when: the command exits nonzero, for example because `.env.example`
   cannot be read or `.env` cannot be created.

2. Edit `.env` before starting the stack. Replace `POSTGRES_PASSWORD=change-me-before-use`
   with a strong, unique password. `POSTGRES_DB` and `POSTGRES_USER` select the
   initial PostgreSQL database and user. `HTTP_PORT` selects the host port and
   defaults to `8080`. Compose derives the application's `DB_DRIVER`, `DB_HOST`,
   `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` values from the
   `POSTGRES_*` settings; do not put a real password in tracked files.

3. Build and start the published services:

   ```sh
   docker compose --profile publish up --build -d
   ```

   Fails when: Compose exits nonzero or reports a build, configuration,
   interpolation, or service-start error. A successful return is not a
   substitute for the post-milestone runtime checks in
   [docker/TEST-HANDOFF.md](docker/TEST-HANDOFF.md).

4. Open `http://localhost:8080/` (or `http://localhost:<HTTP_PORT>/` when
   `HTTP_PORT` was changed). The installer is under `/install/`.

   Fails when: the browser cannot connect to the configured port or the CMS
   returns an error instead of the installer or site.

## Installer walkthrough

The installer has six numbered steps. Follow the installer UI in order:

1. **Step 1 of 6 — Preflight:** Review the PHP version, PDO driver, required
   extensions, and writable paths. Resolve missing requirements before
   depending on the corresponding feature.
2. **Step 2 of 6 — Database settings:** With the Compose deployment above, the
   installer detects the `DB_*` environment configuration and displays its
   driver/host/port/database. Continue to schema creation. If running outside
   that configured environment, choose an available supported PDO driver and
   enter its connection details.
3. **Step 3 of 6 — Schema:** The installer validates the database connection
   and creates the tables and indexes. Environment-provided settings are used
   directly; otherwise, the installer also writes the PDO database
   configuration.
4. **Step 4 of 6 — Platform details:** Enter the platform information. The
   installer notes that the platform name should not contain spaces and uses
   defaults for empty fields.
5. **Step 5 of 6 — Platform and administrator form:** Submit the platform
   details; the installer creates the platform, saves its configuration, and
   presents the superuser form.
6. **Step 6 of 6 — Superuser:** Submit the superuser details. The installer
   creates the account and provides a link to the admin interface.

After setup, apply the operator action described in
[docker/nginx/INSTALLER.md](docker/nginx/INSTALLER.md). The default Nginx
configuration leaves `/install/` accessible; restriction is not automatic and
live access has not been verified by this documentation.

## Persistent volumes

Compose declares these named volumes. Each is seeded from the image on its
first use, when the volume is created; later image rebuilds do not replace the
existing volume contents.

| Volume | Container path | Purpose |
| --- | --- | --- |
| `pgdata` | `/var/lib/postgresql` | PostgreSQL database files |
| `files` | `/var/www/html/files` | Uploaded files |
| `log` | `/var/www/html/log` | Application logs and runtime data |
| `tmp` | `/var/www/html/tmp` | Temporary runtime data |
| `config_conf` | `/var/www/html/config/conf.d` | Generated platform configuration |
| `core_config_conf` | `/var/www/html/core/config/conf.d` | Generated database configuration |
| `css` | `/var/www/html/css` | CSS content, mounted read-only in Nginx |
| `images` | `/var/www/html/images` | Image content, mounted read-only in Nginx |
| `xslt` | `/var/www/html/xslt` | XSLT content, mounted read-only in Nginx |

Keep the named volumes when recreating services. The `log` and `tmp` volumes
hold runtime data; decide whether logs need separate retention or archival
before cleanup. If administrators edit CSS, images, or XSLT through the CMS,
include those volumes in backups as well.

## Backup and restore

Take coordinated backups before upgrades or other maintenance. Quiesce
application writes while capturing the database and file/configuration data.
Store the resulting files outside the Compose volumes, protect them as
sensitive data, and verify that the backup files are non-empty.

Create a plain SQL database dump:

```sh
docker compose --profile publish exec -T db sh -c 'pg_dump -U "$POSTGRES_USER" "$POSTGRES_DB"' > puzzleapps.sql
```

Fails when: Compose or `pg_dump` exits nonzero, or `puzzleapps.sql` is empty.

Archive uploaded files, generated configuration, and the image-seeded content
volumes:

```sh
docker compose --profile publish exec -T app tar -C /var/www/html -czf - files config/conf.d core/config/conf.d css images xslt > puzzleapps-files-config.tgz
```

Fails when: Compose or `tar` exits nonzero, or `puzzleapps-files-config.tgz` is
empty.

The database dump covers PostgreSQL data; the archive covers uploads,
generated configuration, and mutable image-seeded content. The `log` and `tmp`
volumes are runtime data and are not included in these example backups. Add
them only if your retention policy requires them.

To restore, use an isolated or otherwise empty target deployment with the
matching application and PostgreSQL configuration. Restore the SQL dump into
the target database and extract the archive at `/var/www/html` in the
application container so its volume contents are restored to their original
paths. Stop application writes during restoration, check the database import
and archive extraction exit statuses, then verify the restored site and data
before directing users to it. Do not remove the only copy of existing volumes
until the restored deployment has been checked.

## Upgrades

Back up the database and persistent files/configuration before an application
upgrade. Rebuild and recreate the services with the same Compose project and
named volumes so persistent data remains attached; do not use volume-removal
options as part of routine upgrades. Review release notes and verify the
application after the upgrade.

The Compose database image is PostgreSQL 18. Changing its major version
requires a deliberate PostgreSQL data-upgrade procedure and a verified backup;
changing the image tag alone is not a data migration.

## Secrets

The committed `.env.example` contains placeholders, including
`POSTGRES_PASSWORD=change-me-before-use`. Replace the sample password in the
local `.env` before first deployment. Compose passes the database credentials
to the PostgreSQL and application services as environment variables; this
configuration does not use a Docker secret store.

Keep `.env` and other environment-specific files out of version control and
logs, restrict access to the Compose host and its environment file, and rotate
credentials if they may have been exposed. The existing local `.gitignore`
rules ignore `.env` and `.env.*` while retaining `!.env.example`.
