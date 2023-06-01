# Backend Laravel API Readme

This README provides instructions on how to use and set up the Dockerized Laravel app, which allows for easy deployment and portability across different environments using Docker containers.

## Prerequisites

Before you begin, make sure you have the following prerequisites installed on your system:

- Docker: Install and run the Docker Engine on your machine. You can download Docker from the official website: [https://www.docker.com/get-started](https://www.docker.com/get-started)

## Getting Started

To start the Dockerized Laravel app, follow these steps:

1. Clone the repository: 
```
git clone https://github.com/dev-harbiola/backend-laravel-api
```
2. Navigate to the project directory: 
```
cd backend-laravel-api
```

### Building the Docker Image

To build the Docker image for the Laravel app, run the following command in the project directory:
```
docker build -t backend-laravel-api .
```
This command builds the Docker image based on the provided `Dockerfile` and tags it as `backend-laravel-api`.

### Starting the App

To start the app, run the following command in the project directory:
```
docker-compose up -d
```
This command starts the Docker containers defined in the `docker-compose.yml` file.

The Docker Compose file defines three services:

1. `server` - Laravel app service: Runs the Laravel app using the `backend-laravel-api` Docker image. It exposes port 8000 for accessing the app.
2. `db` - PostgreSQL database service: Runs the PostgreSQL database using the `postgres:15-alpine` Docker image.
3. `client` - React News app client: Runs the React News app client using the `react-news-app_client` Docker image.

Alternatively, you can run the following command to automatically configure and run the application:
```
docker-compose up --build
```

### Accessing the App

- Laravel App: Access the Laravel app at `http://localhost:8000`.
- PostgreSQL Database: The PostgreSQL database runs as a service and can be accessed using the appropriate configuration in your Laravel app.
- React News App Client: The React News app client is accessible based on its specific configuration.

Note: Ensure that port 8000 is not already in use on your system.

### Stopping the App

To stop the running containers, run the following command in the project directory:
```
docker-compose down
```
This command stops and removes the containers.

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

If you encounter any issues or errors while running the Dockerized Laravel app, consider the following troubleshooting steps:

1. Ensure that Docker is installed and running correctly on your system.
2. Verify that the required ports (8000) are not being used by other services or applications on your system.
3. Double-check the syntax and configuration of your Dockerfile and docker-compose.yml files for any errors or misconfigurations.
4. Make sure you have the correct dependencies and environment variables set up for your Laravel app and PostgreSQL database.
