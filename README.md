# TKT Transport System

TKT Transport System is a Laravel project fixed as an admin dashboard and API backend for transport management operations.

It is intended for:

- Web admin panel usage by staff and admins through the browser
- Internal management workflows such as reports, search, and operational data management
- Future Android app synchronization through API endpoints

## Project Role

This Laravel project is the central backend for:

- Admin system
- Mobile app API
- Reports and dashboard

In short:

- Laravel project = admin dashboard + API backend
- Web admin panel is included
- API backend for future Android sync is included

## Tech Stack

- Laravel 13
- PHP 8.4
- PostgreSQL 17

## Local Setup

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Copy environment file:

```bash
copy .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```

5. Run migrations:

```bash
php artisan migrate
```

6. Start the local server:

```bash
php artisan serve
```

## Default Local Environment

The example environment is prepared for local PostgreSQL development with these defaults:

- Database: `tkt_transport_system`
- Host: `127.0.0.1`
- Port: `5432`
- Username: `postgres`

Update the password in `.env` for your local machine before sharing or deploying.

## Scope

Included in this project:

- Admin dashboard
- Internal web admin panel
- API endpoints for mobile app integration
- Reporting, searching, and management features

Out of scope for now:

- Public marketing website
- Company intro website
- Home page
- About us page
- Gallery page
- Routes page for public visitors
- Public landing page

These can be built later as a separate phase if needed.

## Android App Future Sync

The Android app is currently planned as offline-first.

In a later phase:

- Local Android data will sync to the Laravel backend
- Laravel will act as the future central server
- Laravel will also serve as the API server for the Android app

## Initial Project Structure

- `app/Http/Controllers/Admin` for admin-facing modules
- `app/Http/Controllers/Operations` for transport operation workflows
- `app/Http/Controllers/Api` for API endpoints
- `app/Services` for business logic services
- `app/Repositories` for data access abstractions

## Current Status

- Git repository initialized
- PostgreSQL connection configured
- Initial Laravel migrations completed
- First commit pushed to GitHub

## Repository

[GitHub Repository](https://github.com/n3k00/tkt_transport_system)
