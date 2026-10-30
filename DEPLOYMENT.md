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
