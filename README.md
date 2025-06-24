# japi-example-app
A simple example application to demonstrate refactored JAPI

This isn't really intended to be useful to anybody apart from me, though it may serve as a decent example application for using JAPI.  It exists to fulfill the following tasks:

* Serve as an example JAPI application
* Allow me to find and issues/bugs in JAPI
* Determine a set of common dependencies that would make sense for a real JAPI application (DI, .env handling, etc)
* Find ways in which JAPI can be improved
* Determine how to best use JAPI in an application

As such, it's not going to have unit tests, as it's a test of sorts in and of itself.  

```shell
composer install
cd ./public
php -S localhost:8000
```
