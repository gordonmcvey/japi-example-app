# JAPI Example App

This is a simple application for my [JAPI refactoring project](https://github.com/gordonmcvey/php-japi), to serve as a kind of skeleton app on which you could build a real application.  

## Running

You can run the application locally or in a Docker container with whatever web server you prefer, or you can run it directly with PHP's built in web server (obviously you should only use that for local testing/development and not in a production environment!)

Running it with PHP's web server is simply a matter of cloning the repo, setting up the appropriate `.env` file, and running the PHP web server from the `/public` subdirectory:

If you want to set up for development with debugging options enabled:
```shell
cp .env.example.dev .env
```

or if you want to set up for production with debugging options disabled:
```shell
cp .env.example.prod .env
```

Then, run the application:

```shell
composer install
cd ./public
php -S localhost:8000
```

### DI Container branches

As the intent with JAPI was to make it as flexible as possible I've provided a number of branches that demonstrate how to use it with several different DI containers.  To use the preferred DI method, simply checkout the branch for the method you want to use before installing dependencies and running the project.

* `main`: No container, dependency injection is managed manually
* `league-container`: Automatic DI managed with the [League DI container](https://container.thephpleague.com/)
* `php-di: Automatic` DI managed with the [PHP-DI container](https://php-di.org/)
* `symfony-di`: Automatic DI managed with the [Symfony DI container](https://symfony.com/doc/current/components/dependency_injection.html) component

## Usage

When the application is running, you can trigger its endpoints with HTTP requests using any suitable method such as invocation from a browser, tools like Postman or Bruno, and so on.

### Endpoints

There are two endpoints available: 

* `/health/ping`: Returns a simple response with when the request was received and when it was dispatched (can only be invoked with `GET`)
* `/health/echo-payload`: Takes whatever JSON is in the request body and echos it back (can only be invoked with `POST` or `PUT`)

### Bruno Collection

The repo includes a collection of [Bruno](https://www.usebruno.com/) requests that include documentation and examples.  You can import them into Bruno and run them from there.  

As the `echo-payload` endpoint requires you to `POST` or `PUT` a JSON document, running it from a browser, from CURL, etc would be tricky, but the `ping` endpoint can be invoked with a simple `GET` request.  

## Rationale

The main goals of this project are mainly to both showcase and test my JAPI refactoring project.  
* Serve as an example JAPI application
* Allow me to find bugs in JAPI that might now show up in its unit/integration tests
* Try out different use cases in JAPI
* Find ways of improving JAPI's usability (if something is difficult to set up in this app then maybe we can modify JAPI in ways to help)
* Determine a set of common dependencies that would make sense for a real JAPI application (DI, .env handling, etc)
* Determine how to best use JAPI in an application

As such, there will be limited (if any) automated testing included in this application, as in a way it serves as a test in its own right.
