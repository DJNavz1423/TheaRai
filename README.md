# TheaRai Eatery

TheaRai Eatery is a Laravel 12 application for restaurant operations. Its frontend assets are checked in under `public/`; a Vite/Node build is not required to deploy the current app.

See [DEPLOYMENT.md](./DEPLOYMENT.md) for the production checklist and Ubuntu/Oracle Cloud VM deployment runbook.

## Local development

Requirements: PHP 8.2 or newer, Composer, PostgreSQL, and the PHP extensions required by Laravel and the installed Composer packages.

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`, set the local database and any integrations you use, then run `php artisan key:generate`.
3. Run the database migrations only against a disposable development database whose schema is backed up or can be recreated.
4. Start the app with your local PHP web server, pointing its document root at `public/`.

Metabase is optional. Without its URL and embed secret, the dashboard and analytics pages omit the embedded charts and continue to show their native application metrics.
