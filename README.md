# Webform API

> A Dockerized contact-form and ticket-management API developed during my internship at HMM Deutschland GmbH.

![PHP](https://img.shields.io/badge/PHP-8.5%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![S3](https://img.shields.io/badge/Storage-S3%20compatible-569A31?style=for-the-badge&logo=amazons3&logoColor=white)

## About

The project provides a REST API for managing contact-form submissions. Users can register and authenticate, create and manage forms, reply to submissions, manage subjects, and upload or download attachments.

The current release is **v2**, built with Laravel 12. It replaces the original plain-PHP implementation with a structured API, JWT authentication, role-based access control, MySQL persistence, and S3-compatible file storage.

## Features

- User registration and login with JWT authentication
- Role-based access for users and administrators
- Contact-form submission and reply management
- Subject management
- File uploads, downloads, and deletion
- MySQL database with Docker health checks
- S3-compatible attachment storage through Zenko CloudServer
- OpenAPI specification in [`v2-laravel/openApi.yaml`](v2-laravel/openApi.yaml)

## Project structure

```text
.
├── v1-php/                 # Legacy plain-PHP implementation
└── v2-laravel/             # Current Laravel API
    ├── nginx/              # Nginx configuration
    ├── php/                # PHP-FPM Docker image
    ├── src/                # Laravel application
    ├── docker-compose.yaml # App, Nginx, MySQL, and S3 services
    └── openApi.yaml        # API reference
```

## Requirements

- Docker Engine 24.0 or newer
- Docker Compose v2
- Git 2.30 or newer

PHP, Composer, MySQL, and the S3-compatible service run in Docker for the v2 setup.

## Quick start

Clone the repository and start the v2 stack:

```bash
git clone https://github.com/Time8009/praktikum-hmm.git
cd praktikum-hmm/v2-laravel

cp src/.env.example src/.env
docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

The API is available at [http://localhost](http://localhost). A health response is available at [http://localhost/](http://localhost/):

```json
{
  "status": "API operational",
  "version": "v2"
}
```

To stop the services:

```bash
docker compose down
```

To remove the database volume as well:

```bash
docker compose down -v
```

> The second command permanently removes the local MySQL data volume.

## Configuration

The Docker Compose file provides these development services:

| Service | Address | Purpose |
| --- | --- | --- |
| Nginx | `http://localhost` | Public API entry point |
| MySQL | `localhost:3306` | Application database |
| S3-compatible storage | `http://localhost:9000` | File attachments |

The default Compose credentials are intended for local development only. Set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`, and the AWS/S3 variables in `v2-laravel/.env` or your shell environment before using the project outside a local environment.

## API overview

Unauthenticated endpoints:

- `POST /register`
- `POST /login`

Authenticated resources:

- `/formular` — contact-form submissions
- `/answer` — replies
- `/subject` — form subjects
- `/user` — user profiles
- `/file` — attachments

Administrator-only operations include subject management, role changes, and access to all users’ submissions. See [`v2-laravel/openApi.yaml`](v2-laravel/openApi.yaml) for request and response schemas.

## Development

Useful commands from `v2-laravel/`:

```bash
docker compose logs -f app
docker compose exec app php artisan route:list
docker compose exec app php artisan test
```

## License

This project is maintained for educational and internship purposes.
