# Flight PHP Skeleton App

Use this skeleton application to quickly setup and start working on a new Flight PHP application. This application uses the latest version of Flight PHP v3.

This skeleton is designed to be AI-friendly out of the box, with predefined instructions files for popular AI coding assistants such as GitHub Copilot, Cursor, and Windsurf. This helps streamline development and ensures your project is ready for modern, AI-assisted workflows.

This skeleton application was built for Composer. You also could download a zip of this repo, downloading a zip of the [flightphp/core](https://github.com/flightphp/core) repo, and manually autoload the files by running `require('flight/autoload.php')` in your `app/config/# FlightPHP Skeleton Project Instructions

This document provides guidelines and best practices for structuring and developing a project using the FlightPHP framework.

## Project Structure

Organize your project as follows:

```
project-root/
│
├── app/                # Application-specific code
│   ├── controllers/    # Route controllers (e.g., HomeController.php)
│   ├── middlewares/    # Custom middleware classes/functions
│   ├── models/         # Data models (if needed)
│   ├── utils/          # Utility/helper functions
│   ├── views/          # View templates (if using)
│   └── commands/       # Custom CLI commands for Runway
│
├── public/             # Web root (index.php, assets, etc.)
│
├── config/             # Configuration files (database, app settings, routes)
│
├── vendor/             # Composer dependencies
│
├── tests/              # Unit and integration tests
│
├── composer.json       # Composer config
│
└── README.md           # Project overview
```

## Development Guidelines

- **Controllers:** Place all route-handling logic in `app/controllers/`. Each controller should handle a specific resource or feature.
- **Middlewares:** Store reusable middleware in `app/middlewares/`. Register them in your bootstrap or route files.
- **Utils:** Place helper functions and utilities in `app/utils/`.
- **Models:** If your app uses data models, keep them in `app/models/`.
- **Views:** Store templates in `app/views/` if using a templating engine.
- **Config:** Use the `app/config/` directory for configuration files. The main config file is `config.php`, which should be created by copying `config_sample.php` and updating as needed.
- **Public:** Only expose the `public/` directory to the web server. All requests should go through `public/index.php`.
- **Environment:** Do not use .env files; all configuration should be managed in `app/config/config.php`.

## Getting Started

1. Clone the repository and run `composer install`.
2. Copy `app/config/config_sample.php` to `app/config/config.php` and update configuration values as needed.
3. Set your web server's document root to the `public/` directory.
4. Add new controllers, middlewares, and utilities as needed, following the structure above.
5. Register routes and middlewares in your bootstrap file (usually `public/index.php`).

## CLI Commands

Flight projects can include custom CLI commands to automate tasks such as migrations, seeding, or maintenance. The recommended CLI tool is [flightphp/runway](https://github.com/flightphp/runway).

- Place your custom command classes in the `app/commands/` directory.
- Runway will automatically discover and register commands from this directory.
- Use CLI commands to manage your application, generate code, or perform routine tasks.

Refer to the Runway documentation for details on creating and using custom commands.

## Additional Tips

- Keep controllers focused and small; delegate logic to services or utils when possible.
- Write tests for controllers and utilities in the `tests/` directory.
- Use Composer for dependency management.
- Document your code and update the README as your project evolves.
- **Favor simplicity and minimalism:** Keep your codebase simple and avoid unnecessary abstractions or complexity. Only add dependencies when absolutely necessary, as fewer dependencies mean fewer potential issues and security risks.
- **Follow coding standards:** Use PSR1 coding standards and strict comparisons (`===`). Maintain a high level of code quality and consistency throughout your project.
- **Test thoroughly:** Regularly run and maintain tests for your codebase. Ensure your application works as expected.

For more details, see the README file or visit the FlightPHP documentation.

## Suggested Packages

Flight is highly extensible. Here are some recommended packages and plugins for common needs:

- **ORM / Database:**
  - [flightphp/active-record](https://github.com/flightphp/active-record) (official ORM/Mapper)
  - [flightphp/core](https://github.com/flightphp/core) PdoWrapper (simple PDO wrapper)
  - [byjg/php-migration](https://github.com/byjg/php-migration) (database migrations)
- **Session:**
  - [flightphp/session](https://github.com/flightphp/session) (official session library)
  - [Ghostff/Session](https://github.com/Ghostff/Session) (advanced session manager)
- **Permissions:**
  - [flightphp/permissions](https://github.com/flightphp/permissions) (official permissions library)
- **Caching:**
  - [flightphp/cache](https://github.com/flightphp/cache) (official in-file caching)
- **CLI:**
  - [flightphp/runway](https://github.com/flightphp/runway) (official CLI tool)
- **Cookies:**
  - [overclokk/cookie](https://github.com/overclokk/cookie) (cookie management)
- **Debugging:**
  - [tracy/tracy](https://github.com/nette/tracy) (error handler and debugger)
  - [flightphp/tracy-extensions](https://github.com/flightphp/tracy-extensions) (Flight-specific Tracy panels)
- **APM (Performance Monitoring):**
  - [betaflightphp/apm](https://github.com/betaflightphp/apm) (application performance monitoring)
- **Encryption:**
  - [defuse/php-encryption](https://github.com/defuse/php-encryption) (encryption/decryption)
- **Job Queue:**
  - [n0nag0n/simple-job-queue](https://github.com/n0nag0n/simple-job-queue) (asynchronous job processing)
- **Templating:**
  - [latte/latte](https://github.com/nette/latte) (recommended templating engine)
  - (Deprecated) flightphp/core View (basic, not recommended for large projects)
- **API Documentation:**
  - [SwaggerUI](https://github.com/zircote/swagger-php) (Swagger/OpenAPI documentation)
  - [FlightPHP OpenAPI Generator](https://daniel-schreiber.de/blog/flightphp-openapi-generator.html) (API-first approach)

Choose the packages that best fit your project's needs. Official FlightPHP packages are recommended for core functionality.
bootstrap.php` file.

## Installation

Run this command from the directory in which you want to install your new Flight PHP application. (this will require PHP 7.4 or newer)

```bash
composer create-project flightphp/skeleton cool-project-name
```

Replace `cool-project-name` with the desired directory name for your new application.

After you create the project, make sure you go to the `app/config/config.php` and `app/config/services.php` and uncomment the lines related to the database you want to use before you get started.

> _Tip: This skeleton includes configuration files for AI coding assistants (Copilot, Cursor, Windsurf) to help you get the most out of AI-driven development tools from the start._

### Robust Setup of the Application

This skeleton will come with 2 versions of a starter application. The robust version is a fully structured application meant for projects that you anticipate will be a bigger size. This is setup with object oriented programming in mind so that it is easier to unit test and scale your project with multiple developers (or make it easier on yourself).

The robust version adds an `app/` directory where everything has a basic structure. This is how this skeleton is configured by default.

### Simple Setup of the Application

This is basically a single file application. The only exception to this is the config file which is still in the `app/config/` directory. This is a good starting point for smaller projects or projects that you don't anticipate will grow much.

To use the simple version, you'll need to move the `index-simple.php` file to the `public/` directory and rename it to `index.php`. You can delete any other controllers, views, or config files (except the `config.php` file of course).

With the simple setup, there is two very import security steps to be aware of. 
- **DO NOT SAVE SENSITIVE CREDENTIALS TO THE `index.php` FILE**. 
- **DO NOT COMMIT ANY TYPE OF SENSITIVE CREDENTIALS TO YOUR REPOSITORY**.

This is what the config file is for. If you need to save sensitive credentials, save them to the config file and then reference them in the `index.php` file.

## Running the Application

### No Dependency Setup

To run the application in development, you can run these commands 

```bash
cd cool-project-name
composer start
```

After that, open `http://localhost:8000` in your browser.

__Note: If you run into an error similar to this `Failed to listen on localhost:8000 (reason: Address already in use)` then you'll need to change the port that the application is running on. You can do this by editing the `composer.json` file and changing the port in the `scripts.start` key.__

### Docker Setup

You can [install Docker](https://docs.docker.com/engine/install/) and use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd cool-project-name
docker-compose up -d
# or if a newer version of docker
docker compose up -d
```
After that, open `http://localhost:8000` in your browser.

### Vagrant Setup
You can [install Vagrant](https://vagrantup.com/download) and a provider like [VirtualBox](https://www.virtualbox.org/wiki/Downloads) and use simple run the following command to bring up an environment with PHP/MariaDB already setup based on [n0nag0n/firefly](https://github.com/n0nag0n/firefly)

```bash
cd cool-project-name
vagrant up
```

After that, open `http://localhost:8000` in your browser.

## Project Structure

This skeleton is organized for clarity and maintainability, and is also structured to be easily navigable by AI coding assistants. The following layout is recommended:

```
project-root/
│
├── app/                # Application-specific code
│   ├── controllers/    # Route controllers (e.g., HomeController.php)
│   ├── middlewares/    # Custom middleware classes/functions
│   ├── models/         # Data models (if needed)
│   ├── utils/          # Utility/helper functions
│   ├── views/          # View templates (if using)
│   └── commands/       # Custom CLI commands for Runway
│
├── public/             # Web root (index.php, assets, etc.)
│
├── config/             # Configuration files (database, app settings, routes)
│
├── vendor/             # Composer dependencies
│
├── tests/              # Unit and integration tests
│
├── composer.json       # Composer config
│
└── README.md           # Project overview
```

> _Predefined instructions for AI tools are included in this skeleton, making it easier for AI assistants to understand and help you with this structure._

## Do it!
That's it! Go build something flipping sweet!
