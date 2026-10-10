# Production deployment

## Existing Supabase database

The app can deploy against the existing Supabase PostgreSQL database; a schema dump in this repository is not required. The app expects its application tables in the PostgreSQL `laravel` schema. Keep using that database and schema when configuring the OCI VM. The SQL shared for this deployment discussion is context only; it is not an import script.

Do not run `migrate:fresh` or seed production. Check `php artisan migrate:status` and apply only reviewed migrations that are genuinely pending for the existing database. Back up Supabase before any schema change.

The controller query optimization migration adds PostgreSQL indexes concurrently to the existing `laravel` tables. After deploying the code, review the pending migration and apply it with `php artisan migrate --force`; concurrent index builds avoid blocking normal reads and writes while the indexes are created.

## Server checklist

- Use an Ubuntu 22.04/24.04 VM (OCI Ampere is ARM64) with a supported PHP version (8.2+) and Nginx/PHP-FPM.
- Install Composer dependencies for production with `composer install --no-dev --prefer-dist --optimize-autoloader`.
- Install and enable the PHP extensions reported by `composer check-platform-reqs`; the PostgreSQL connection needs `pdo_pgsql`.
- Point Nginx's document root to this project's `public/` directory. Never expose the project root, `.env`, `vendor/`, or storage directories to the web.
- Use HTTPS and set the correct public `APP_URL`.
- Configure `APP_ENV=production`, `APP_DEBUG=false`, and a unique `APP_KEY`. Keep `.env` and all credentials out of Git, images, and web-accessible paths.
- Ensure the PHP-FPM user can write to `storage/` and `bootstrap/cache/`.
- The app stores menu and ingredient images on Supabase S3-compatible storage; configure those credentials and test upload/read/delete before launch. Do not depend on a VM's local filesystem for user uploads.
- Configure a real mail transport and `REPORT_EMAIL_TO` if scheduled report emails are required.
- Set up automated, off-server backups for the database and object storage; perform a restore test.

The frontend is served from the checked-in `public/css/` and `public/js/` assets. This deployment does not need Node.js, npm, Vite, Docker, or Metabase.

## Optional: host Metabase on the same OCI VM

Docker is **not required**. Metabase can run as a Java JAR under `systemd`; Docker is an alternative packaging method, not a separate hosting provider. Docker Desktop on your Windows PC is not needed to run Docker or the JAR on an OCI Linux VM.

The layout can be:

- Laravel/PHP-FPM behind Nginx at `https://app.your-domain.example`.
- Metabase listening only on `127.0.0.1:3000`, reverse-proxied by Nginx at `https://bi.your-domain.example`.
- Supabase PostgreSQL as the Laravel data source. In Metabase, connect it with a dedicated read-only database role where practical.
- A **separate, persistent PostgreSQL application database for Metabase**, which stores Metabase users, questions, dashboards, and settings. Do not rely on Metabase's built-in H2 database for production. Keep Metabase's app database separate from Laravel's `laravel` schema; it can be a separately managed PostgreSQL database or a carefully isolated schema with its own role and privileges.

The supplied `C:\Metabase` folder is an existing Metabase instance, not just an unconfigured JAR. I inspected it: `metabase.jar` reports **Metabase v0.59.4**; `metabase.db.mv.db` in the folder root is its H2 application database, currently about 10 MB; `metabase.db.trace.db` is an H2 trace/log file; and `plugins/` contains downloaded database driver plugins plus a separate sample database. Your saved Metabase setup lives in the application database, not in the JAR. Preserve that database to retain your dashboards, questions, connections, and settings.

The current JAR documentation recommends Java 25 and says ARM is supported (though Metabase tests x86 and ARM). The local JAR started far enough to report its version using Java 26; do not assume that makes Java 26 the supported OCI runtime. Check the v0.59.4 runtime requirements and use a supported Java version on the VM. Pin the JAR to v0.59.4 during migration; do not migrate its database and upgrade Metabase in the same step.

Before transferring the current H2 data, stop the local Metabase process cleanly, then make a separate backup copy of both `metabase.db.mv.db` and the JAR. Do not copy a live H2 database file while Metabase is writing to it. Copy the JAR and, if you rely on any of the downloaded database drivers, the `plugins/` folder to OCI. The `sample-database.db.mv.db` under `plugins/` is not your Metabase application database.

For a production install, migrate the H2 application database to a persistent PostgreSQL application database using Metabase's `load-from-h2` procedure, following the official [H2 migration guide](https://www.metabase.com/docs/latest/installation-and-operation/migrating-from-h2). Use the same Metabase v0.59.4 JAR for the migration and first production start. The target database must be empty and dedicated to Metabase; do not point Metabase's application database at Laravel's `laravel` schema. Keep the original H2 file and backup until you have verified all saved dashboards, questions, and connections in production.

For a JAR install, use the official [Metabase JAR instructions](https://www.metabase.com/docs/latest/installation-and-operation/running-the-metabase-jar-file) and [systemd service guide](https://www.metabase.com/docs/latest/installation-and-operation/running-metabase-as-service). Run it as an unprivileged `metabase` Linux user, configure a protected environment file (mode `600`), and use a systemd service so it restarts after VM reboot. Set `MB_JETTY_HOST=127.0.0.1` and `MB_JETTY_PORT=3000`; configure the `MB_DB_*` variables for its separate PostgreSQL application database. Put Nginx and HTTPS in front of Metabase and do not expose port 3000 directly to the internet.

Alternatively, install Docker Engine on the OCI Linux VM and run the official `metabase/metabase` image with a pinned version, a persistent application database, and protected environment variables. Docker Desktop on Windows is only needed if you want to run Docker locally; it is not a prerequisite for an OCI deployment.

After Metabase is online over HTTPS, set `METABASE_SITE_URL=https://bi.your-domain.example` in Laravel and set `METABASE_SECRET_KEY` to the **same** guest-embedding secret configured in Metabase. Keep that secret server-side. Test the embedded dashboard from Laravel. Without these two Laravel settings, the app intentionally hides the Metabase embed while its native dashboard metrics continue to work.

## Environment settings

Use the current `.env` as the source for the existing Supabase connection and integration settings, but do not copy local development settings blindly. Keep secrets out of Git and deployment images. The current local file is set to `APP_ENV=local` and `APP_DEBUG=true`; override those on OCI. At minimum configure:

```dotenv
APP_NAME=TheaRai
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
APP_KEY=base64:generated-with-artisan

DB_CONNECTION=pgsql
DB_URL=postgresql://<user>:<password>@<supabase-pooler-host>:5432/postgres
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

The current `.env` already has a Supabase session-pooler URL on port 5432 for the `postgres` database; its username/password are embedded in that URL. Keep the URL private and transfer it to OCI's protected environment configuration. Separate `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` values are not needed when `DB_URL` is used. The checked-in PostgreSQL config uses the `laravel` schema/search path. Set `DB_SSLMODE=require` on OCI so the connection requires TLS, then verify the database role can connect and access the `laravel` schema.

The current local `.env` also has `APP_URL=http://127.0.0.1:8000`, `SESSION_DRIVER=file`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`, `FILESYSTEM_DISK=local`, and `MAIL_MAILER=smtp`. Set `APP_URL` to the real HTTPS domain on OCI. A single OCI VM can use file sessions if its storage remains persistent; for replaceable/multiple app instances, use a shared session backend. SMTP values are present locally, but successful delivery must be tested from the deployed server.

### Optional integrations

- **Metabase:** both settings are currently present in the local `.env`. For an OCI deployment without Metabase, leave `METABASE_SECRET_KEY` and `METABASE_SITE_URL` unset/empty on the server. The dashboard and analytics totals remain available, while embedded charts are omitted. To enable embeds later, set both values and configure the matching Metabase guest-embed secret and public HTTPS URL.
- **Xendit QR payments:** a key is currently present in the local `.env`. Use a production Xendit secret on OCI, never a development/test key. If QR checkout is not needed at launch, leave `XENDIT_SECRET_KEY` empty; checkout returns a service-unavailable message while other app features remain available.
- **Supabase object storage:** configure `SUPABASE_URL`, `SUPABASE_S3_ACCESS_KEY`, `SUPABASE_S3_SECRET_KEY`, `SUPABASE_S3_REGION`, `SUPABASE_S3_BUCKET`, and `SUPABASE_S3_ENDPOINT`.
- **Mail:** configure `MAIL_MAILER` and its corresponding SMTP/provider variables; the example's `log` mailer does not deliver email.

## Deployment procedure

Run commands from the application root, with production environment variables available to PHP/Artisan:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan about
php artisan migrate:status
```

Back up Supabase before any schema change. Check migration status. If there are no intended pending migrations, do not run migrations. If a reviewed migration is genuinely pending for this existing database, apply it explicitly:

```sh
php artisan migrate --force
```

Then cache Laravel's production configuration and routes:

```sh
php artisan optimize
```

Do **not** run `php artisan db:seed` on production. The application health endpoint is `/up`; verify it over HTTPS after configuring Nginx and PHP-FPM.

Set up Laravel's scheduler in the `www-data` user's crontab (one entry only):

```cron
* * * * * cd /var/www/thearai && php artisan schedule:run >> /dev/null 2>&1
```

The scheduled cleanup and daily/monthly report jobs will then run. Verify the mail provider and report recipient before enabling report schedules.

## Pre-launch verification

1. Confirm the OCI VM can connect over TLS to the existing Supabase pooler and that its database role has the required privileges on the `laravel` schema.
2. Confirm `php artisan migrate:status` shows the expected migrations and no unexpected pending changes.
3. Run `php artisan test` in CI or a non-production environment. Never point the test runner at the live database.
4. Run `php artisan about` and confirm production mode, debug disabled, database connectivity, and storage configuration.
5. Test login, role access, core POS/inventory/menu flows, image upload, and payment flows only with appropriate test credentials.
6. Check `/up`, HTTPS redirects, application logs, scheduler execution, backups, and restore procedures.
7. Confirm `.env` is not tracked or web-accessible and remove any temporary deployment credentials.
