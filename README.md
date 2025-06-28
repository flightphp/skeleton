# Flight PHP Skeleton App

Use this skeleton application to quickly setup and start working on a new Flight PHP application. This application uses the latest version of Flight PHP v3.

This skeleton is designed to be AI-friendly out of the box, with predefined instructions files for popular AI coding assistants such as GitHub Copilot, Cursor, and Windsurf. This helps streamline development and ensures your project is ready for modern, AI-assisted workflows.

This skeleton application was built for Composer. You also could download a zip of this repo, downloading a zip of the [flightphp/core](https://github.com/flightphp/core) repo, and manually autoload the files by running `require('flight/autoload.php')` in your `app/config/# FlightPHP Skeleton Project Instructions

This document provides guidelines and best practices for structuring and developing a project using the FlightPHP framework.

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

The robust version adds an `app/` directory where everything has a basic structure. This is how this skeleton is configured by default.meant

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
