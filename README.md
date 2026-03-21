# TKT Transport System

TKT Transport System is a Laravel-based transport management application for handling operational workflows such as route planning, vehicle management, trip scheduling, staff coordination, ticketing, and reporting.

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
