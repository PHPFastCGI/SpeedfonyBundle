# Speedfony Bundle

[![Latest Stable Version](https://poser.pugx.org/phpfastcgi/speedfony-bundle/v/stable)](https://packagist.org/packages/phpfastcgi/speedfony-bundle)
[![Build Status](https://travis-ci.org/PHPFastCGI/SpeedfonyBundle.svg?branch=master)](https://travis-ci.org/PHPFastCGI/FastCGIDaemon)
[![Coverage Status](https://coveralls.io/repos/PHPFastCGI/SpeedfonyBundle/badge.svg?branch=master)](https://coveralls.io/r/PHPFastCGI/SpeedfonyBundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PHPFastCGI/SpeedfonyBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PHPFastCGI/SpeedfonyBundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/phpfastcgi/speedfony-bundle/downloads)](https://packagist.org/packages/phpfastcgi/speedfony-bundle)

A symfony2 bundle which allows applications to reduce overheads by exposing symfony's Request-Response structure to a FastCGI daemon.

Visit the [project website](http://phpfastcgi.github.io/).

## Introduction

Using this bundle, symfony2 applications can stay alive between HTTP requests whilst operating behind the protection of a FastCGI enabled web server.

## Current Status

This daemon is currently in early development stages and not considered stable.

Contributions and suggestions are welcome.

## Installing

By turning your Symfony application into a FastCGI application, you can keep the application in memory between request cycles.

To do this, open the terminal in your project directory and use composer to add the Speedfony Bundle to your dependencies.

```sh
composer require "phpfastcgi/speedfony-bundle:^0.8"
```

Next, register the bundle in your AppKernel.php file:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
  public function registerBundles()
  {
    $bundles = array(
      // ...
      new PHPFastCGI\SpeedfonyBundle\PHPFastCGISpeedfonyBundle(),
    );

    // ...
  }
// ...
```

## Running the Daemon

To start the daemon listening on port 5000 use the command below. Production mode is selected here for the purposes of generating accurate benchmarks. We do not recommend that you use this package in production mode as it is not yet stable.

Check the FastCGI documentation for your chosen web server to find out how to configure it to use this daemon as a FastCGI application.

```sh
php app/console speedfony:run --port 5000 --env="prod"
```

If you are using apache, you can configure the FastCGI module to launch and manage the daemon itself. For this to work you must omit the "--port" option from the command and the daemon will instead listen for incoming connections on FCGI_LISTENSOCK_FILENO (STDIN).

## Updates

### v0.8.1
- Bugfix: Upgraded FastCGIDaemon to v0.8.0

### v0.8.0
- Symfony 3.0 component support

### v0.7.1
- Service configuration file fix

### v0.7.0
- Upgraded to use FastCGIDaemon v0.7.0

### v0.6.0
- Upgraded to use FastCGIDaemon v0.6.0

### v0.5.0
- Upgraded to use FastCGIDaemon v0.5.0

### v0.4.0
- Upgraded to use FastCGIDaemon v0.4.0, renamed command to 'speedfony:run'

### v0.3.2
- Bugfix: Composer dependency on FastCGIDaemon was too loose

### v0.3.1
- Bugfix: Added call to terminate method on symfony kernel (so post response listeners now work)

### v0.3.0
- Upgraded to use FastCGIDaemon v0.3.0

### v0.2.0
- Upgraded to use FastCGIDaemon v0.2.0 and Symfony 2.7 with PSR-7 messages

Contributions and suggestions are welcome.
