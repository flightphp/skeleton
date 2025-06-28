# FlightPHP Skeleton Project Instructions

This document provides guidelines and best practices for structuring and developing a project using the FlightPHP framework.

## Instructions for AI Coding Assistants

As you are developing this project, follow these guidelines as close as you can. If you are unsure about something, ask for clarification before proceeding. You should feel 95% confident in the coding decisions that you make, but allow yourself an offramp if you are not sure about something to ask questions.

## Project Structure

Organize your project as follows:

project-root/
│
├── app/                # Application-specific code
│   ├── commands/       # Custom CLI commands for Runway (built using adhocore/cli)
│   ├── config/         # Configuration files (database, app settings, routes)
│                       # Key files in this folder:
│                       #   - bootstrap.php: Bootstraps and connects the files in the config folder.
│                       #   - routes.php: Route definitions.
│                       #   - services.php: Service definitions (where config variables are used and injected).
│   ├── controllers/    # Route controllers (e.g., HomeController.php)
│   ├── logic/          # (For large projects) Business logic classes/services, called from controllers
│   ├── middlewares/    # Custom middleware classes/functions
│   ├── models/         # Data models (if needed, usually using flightphp/active-record)
│   ├── utils/          # Utility/helper functions
│   └── views/          # View templates (if using)
│
├── public/             # Web root (index.php, assets, etc.)
│
├── vendor/             # Composer dependencies
│
├── tests/              # Unit and integration tests
│
├── composer.json       # Composer config
│
└── README.md           # Project overview

## Development Guidelines

- **Controllers:** Place all route-handling logic in `app/controllers/`. Each controller should handle a specific resource or feature. For large projects, move business logic out of controllers and into the `app/logic/` directory as dedicated classes/services, and call them from your controllers. Use appropriate namespaces for organization. By default, all controllers inject the `Engine $app` variable unless this project has its own dependency injection handler.
- **Namespaces:** Use lowercase namespaces for all classes in the `app/` directory. For example, `app/controllers/HomeController.php` should have the namespace `app\controllers`.
- **Middlewares:** Store reusable middleware in `app/middlewares/`. Register them in your bootstrap or route files.
- **Utils:** Place helper functions and utilities in `app/utils/`.
- **Models:** If your app uses data models, keep them in `app/models/`.
- **Views:** Store templates in `app/views/` if using a templating engine.
- **Config:** Use the `app/config/` directory for configuration files. The main config file is `config.php`, which should be created by copying `config_sample.php` and updating as needed. In other main bootstrap files like bootstrap, and services.php, the $config variable is available to use to access configuration values.
- **Public:** Only expose the `public/` directory to the web server. All requests should go through `public/index.php`.
- **Environment:** Do not use .env files; all configuration should be managed in `app/config/config.php`.
- **Routes:** Define routes in `app/config/routes.php`. Use the `$router->map()` method to register routes with all request methods or `$router->get()` for `GET $router->post()` for POST etc. and associate them with controller methods. Best practice for defining the controller is [ MyController::class, 'myMethod' ].

## Getting Started

1. Clone the repository and run `composer install`.
2. Copy `app/config/config_sample.php` to `app/config/config.php` and update configuration values as needed.
3. Set your web server's document root to the `public/` directory.
4. Add new controllers, middlewares, and utilities as needed, following the structure above.
5. Register routes and middlewares in your bootstrap file (usually `public/index.php`).

## CLI Commands

Flight projects can include custom CLI commands to automate tasks such as migrations, seeding, or maintenance. The recommended CLI tool is [flightphp/runway](https://github.com/flightphp/runway), which builds on the [adhocore/cli](https://github.com/adhocore/cli) package (not Symfony Console).

- Place your custom command classes in the `app/commands/` directory.
- Runway will automatically discover and register commands from this directory.
- All CLI commands should be built using the adhocore/cli package (do not use Symfony Console).
- Use CLI commands to manage your application, generate code, or perform routine tasks.

Refer to the Runway documentation for details on creating and using custom commands with adhocore/cli.

## Additional Tips

- Keep controllers focused and small; delegate global or common logic to `app/utils/` when possible. For large projects, move business logic to `app/logic/` and use appropriate namespaces.
- Write tests for class files in the `tests/` directory.
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
  - [flightphp/runway](https://github.com/flightphp/runway) (official CLI tool, built on adhocore/cli)
- **Cookies:**
  - [overclokk/cookie](https://github.com/overclokk/cookie) (cookie management)
- **Debugging:**
  - [tracy/tracy](https://github.com/nette/tracy) (error handler and debugger)
  - [flightphp/tracy-extensions](https://github.com/flightphp/tracy-extensions) (Flight-specific Tracy panels)
- **APM (Performance Monitoring):**
  - [flightphp/apm](https://github.com/flightphp/apm) (application performance monitoring)
- **Encryption:**
  - [defuse/php-encryption](https://github.com/defuse/php-encryption) (encryption/decryption)
- **Job Queue:**
  - [n0nag0n/simple-job-queue](https://github.com/n0nag0n/simple-job-queue) (asynchronous job processing)
- **Templating:**
  - [latte/latte](https://github.com/nette/latte) (recommended templating engine)
  - (Deprecated) flightphp/core View (basic, not recommended for large projects)
- **API Documentation:**betaflightphp
  - [SwaggerUI](https://github.com/zircote/swagger-php) (Swagger/OpenAPI documentation)
  - [FlightPHP OpenAPI Generator](https://daniel-schreiber.de/blog/flightphp-openapi-generator.html) (API-first approach)

Choose the packages that best fit your project's needs. Official FlightPHP packages are recommended for core functionality.

## Security Best Practices

All code implemented in this project must follow secure coding best practices. Insecure code will not be accepted. Always assume user input is hostile and never trust data from users or external sources. The following guidelines and examples are required for all code contributions:

### Cross Site Scripting (XSS)
- Always escape output from users before rendering in views.
- Use Flight's view class or a templating engine like Latte, which auto-escapes variables.

```php
// Example: Escaping user input in views
$name = '<script>alert("XSS")</script>';
Flight::view()->set('name', $name); // Escapes output
Flight::view()->render('template', ['name' => $name]); // Latte auto-escapes
```

### SQL Injection
- Never concatenate user input into SQL queries.
- Always use prepared statements or parameterized queries.

```php
// Secure: Using prepared statements
$statement = Flight::db()->prepare('SELECT * FROM users WHERE username = :username');
$statement->execute([':username' => $username]);
$users = $statement->fetchAll();

// Or with PdoWrapper
$users = Flight::db()->fetchAll('SELECT * FROM users WHERE username = :username', [ 'username' => $username ]);
```

### CORS (Cross-Origin Resource Sharing)
- Set CORS headers using a utility or middleware before Flight::start().
- Only allow trusted origins.

```php
// Example: app/utils/CorsUtil.php
namespace app\utils;
class CorsUtil {
    public function set(array $params): void { /* ...see docs for full example... */ }
    private function allowOrigins(): void { /* ... */ }
}
// In index.php
$CorsUtil = new CorsUtil();
Flight::before('start', [ $CorsUtil, 'set' ]);
```

### Error Handling
- Never display sensitive error details in production.
- Log errors instead and use Flight::halt() for controlled responses.

```php
$environment = ENVIRONMENT;
if ($environment === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '/path/to/error.log');
}
// Controlled error response
Flight::halt(403, 'Access denied');
```

### Input Sanitization
- Always sanitize and validate user input before processing.

```php
$clean_input = filter_var(Flight::request()->data->input, FILTER_SANITIZE_STRING);
$clean_email = filter_var(Flight::request()->data->email, FILTER_SANITIZE_EMAIL);
```

### Password Hashing
- Always hash passwords using PHP's built-in functions. Never store plain text passwords.

```php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
if (password_verify($password, $stored_hash)) { /* Password matches */ }
```

### Rate Limiting
- Use caching or middleware to limit repeated requests and prevent brute force attacks.

```php
Flight::before('start', function() {
    $cache = Flight::cache();
    $ip = Flight::request()->ip;
    $key = "rate_limit_{$ip}";
    $attempts = (int) $cache->retrieve($key);
    if ($attempts >= 10) {
        Flight::halt(429, 'Too many requests');
    }
    $cache->set($key, $attempts + 1, 60);
});
```
