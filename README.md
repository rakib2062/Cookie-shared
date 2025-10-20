# Cookie Sharing Demonstration

This project demonstrates cookie sharing across different domains/subdomains using a Laravel application as a central cookie server and two simple PHP sites.

## Table of Contents

- [Cookie Sharing Demonstration](#cookie-sharing-demonstration)
  - [Table of Contents](#table-of-contents)
  - [Project Overview](#project-overview)
  - [Prerequisites](#prerequisites)
  - [Setup Instructions](#setup-instructions)
    - [Clone the Repository](#clone-the-repository)
    - [Laravel Application (cookie-app-server)](#laravel-application-cookie-app-server)
    - [PHP Sites (siteA and siteB)](#php-sites-sitea-and-siteb)
  - [Running the Applications](#running-the-applications)
  - [Verifying Shared Cookies](#verifying-shared-cookies)
  - [Troubleshooting](#troubleshooting)

## Project Overview

This repository contains three main components:

1.  **`cookie-app-server`**: A Laravel application acting as a central server for setting and managing cookies. It will run on port `8001`.
2.  **`siteA`**: A simple PHP script designed to interact with the `cookie-app-server` and demonstrate cookie sharing. It will run on port `8002`.
3.  **`siteB`**: Another simple PHP script, similar to `siteA`, also demonstrating cookie sharing. It will run on port `8003`.

The goal is to illustrate how cookies can be shared and accessed across these different applications, simulating a cross-domain cookie scenario.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

*   **PHP** (>= 8.2)
*   **Laravel** (>= v12)
*   **Composer**
*   **A web server** capable of serving PHP applications (e.g., Apache, Nginx, or PHP's built-in development server).
*   **Database**: MySQL, PostgreSQL, SQLite, or any database supported by Laravel.

## Setup Instructions

Follow these steps to set up each component of the project.

### Clone the Repository

First, clone the project repository to your local machine:

```bash
git clone <repository_url>
cd coockie-test
```
*Note: Replace `<repository_url>` with the actual URL of this repository.*

### Laravel Application (cookie-app-server)

1.  **Navigate to the Laravel project directory**:
    ```bash
    cd cookie-app-server
    ```

2.  **Install PHP dependencies**:
    ```bash
    composer install
    ```

3.  **Copy the environment file**:
    ```bash
    cp .env.example .env
    ```

4.  **Generate an application key**:
    ```bash
    php artisan key:generate
    ```

5.  **Configure your database**:
    Open the `.env` file and update the database connection details (e.g., `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

6.  **Run database migrations**:
    ```bash
    php artisan migrate
    ```

7.  **Add Tracker Configuration to `.env`**:
    Add the following lines to your `.env` file:
    ```
    TRACKER_ALLOWED_ORIGINS="http://127.0.0.1:8002,http://127.0.0.1:8003"
    TRACKER_COOKIE_DOMAIN="http://127.0.0.1:8001"
    TRACKER_COOKIE_MINUTES=525600
    TRACKER_RATE_LIMIT_PER_MIN=60
    ```


### PHP Sites (siteA and siteB)

The `siteA` and `siteB` directories contain simple PHP scripts. No specific installation steps are required for these, as they are standalone files.

## Running the Applications

You will need to run each application on its designated port.

1.  **Start the Laravel application (cookie-app-server) on port `8001`**:
    Navigate to the `cookie-app-server` directory and run:
    ```bash
    php artisan serve --port=8001
    ```

2.  **Start `siteA` on port `8002`**:
    Navigate to the `siteA` directory and run PHP's built-in development server:
    ```bash
    php -S 127.0.0.1:8002
    ```

3.  **Start `siteB` on port `8003`**:
    Navigate to the `siteB` directory and run PHP's built-in development server:
    ```bash
    php -S 127.0.0.1:8003
    ```

    *Note: Ensure each command is run in a separate terminal window.*

## Verifying Shared Cookies

After all applications are running:

1.  Open your web browser and navigate to `http://127.0.0.1:8002`.
2.  Then, navigate to `http://127.0.0.1:8003`.

You should observe that cookies set by the `cookie-app-server` (which `siteA` and `siteB` interact with) are accessible across both `siteA` and `siteB`, demonstrating the shared cookie functionality. Inspect your browser's developer tools (Application -> Cookies) to see the cookies being set and shared.

## Troubleshooting

*   **Port Conflicts**: If a port is already in use, you will receive an error. Ensure ports `8001`, `8002`, and `8003` are free.
*   **Laravel `.env` configuration**: Double-check your `.env` file for correct database and application settings.
*   **PHP Version**: Ensure your PHP version meets Laravel's requirements.
*   **Composer Issues**: If `composer install` fails, try clearing caches (`composer clear-cache`) and retrying.