# Backend Laravel API Readme

This README provides instructions on how to use and set up the Dockerized Laravel app. The app runs in a Docker container, allowing for easy deployment and portability across different environments.

## Prerequisites

Before you begin, ensure that you have the following prerequisites installed on your system:

- Docker: The Docker Engine must be installed and running on your machine. You can download Docker from the official website: [https://www.docker.com/get-started](https://www.docker.com/get-started)

## Getting Started

To start the Dockerized Laravel app, follow these steps:

1. Clone the repository: `git clone https://github.com/dev-harbiola/backend-laravel-api`
2. Navigate to the project directory: `cd backend-laravel-api`

### Building the Docker Image

To build the Docker image for the Laravel app, execute the following command in the project directory:

```shell
docker build -t backend-laravel-api .
```

This command will build the Docker image based on the provided `Dockerfile`. The image will be tagged as `backend-laravel-api`.

### Starting the App

To start the app, execute the following command in the project directory:

```shell
docker-compose up -d
```

This command will start the Docker containers defined in the `docker-compose.yml` file.

The Docker Compose file defines three services:

1. `server` - Laravel app service: This service runs the Laravel app using the `backend-laravel-api` Docker image. It exposes port 8000, allowing access to the app.
2. `db` - PostgreSQL database service: This service runs the PostgreSQL database using the `postgres:15-alpine` Docker image.
3. `client` - React News app client: This service runs the React News app client using the `react-news-app_client` Docker image.

### Accessing the App

- Laravel App: The Laravel app will be accessible at `http://localhost:8000`.
- PostgreSQL Database: The PostgreSQL database is running as a service and can be accessed using the appropriate configuration in your Laravel app.
- React News App Client: The React News app client will be accessible according to its configuration.

Note: Make sure the port 8000 is not already in use on your system.

### Stopping the App

To stop the running containers, execute the following command in the project directory:

```shell
docker-compose down
```

This will stop and remove the containers.

## Dockerfile

The provided `Dockerfile` is responsible for building the Docker image for the Laravel app. Here's a brief overview of its contents:

1. Base Image: The base image used is `php:8.2-fpm`.
2. Working Directory: The working directory is set to `/var/www/html`.
3. Dependencies: The Docker image is updated, and necessary dependencies such as `libzip-dev` and `unzip` are installed.
4. Application Files: The entire application directory is copied into the Docker image.
5. Composer: Composer is installed in the Docker image.
6. Application Dependencies: The application dependencies are installed using `composer install`.
7. Permissions: Permissions are set for the `storage` and `bootstrap/cache` directories.
8. Exposed Port: Port 8000 is exposed to allow access to the Laravel app.
9. Start PHP-FPM: The PHP-FPM process is started as the container's entry point.

Feel free to modify the `Dockerfile` according to your specific Laravel app requirements.

## Troubleshooting

If you encounter any issues or errors while running the Dockerized Laravel app, here are a few suggestions:

1. Ensure that Docker is installed and running correctly on your system.
2. Verify that the required ports (8000) are not being used by other