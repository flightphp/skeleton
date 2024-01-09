# Flight PHP Skeleton App

Use this skeleton application to quickly setup and start working on a new Flight PHP application. This application uses the latest version of Flight PHP v3.

This skeleton application was built for Composer. You also could download a zip of this repo and manually autoload the files by running `require('flight/autoload.php')` in your `app/config/bootstrap.php` file.

## Install the Application

Run this command from the directory in which you want to install your new Flight PHP application. (this will require PHP 7.4 or newer)

```bash
composer create-project flightphp/skeleton cool-project-name
```

Replace `cool-project-name` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory (apache webserver, nginx, etc).

One additional thing you'll need to do is copy the `app/config/config_sample.php` file to `app/config/config.php` and update the values to be correct for your environment.

To run the application in development, you can run these commands 

```bash
cd cool-project-name
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd cool-project-name
docker-compose up -d
# or if a newer version of docker
docker compose up -d
```
After that, open `http://localhost:8080` in your browser.

That's it! Go build something flipping sweet!
