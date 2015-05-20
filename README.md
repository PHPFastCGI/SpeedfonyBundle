# Speedfony Bundle

![GitHub Issues](https://img.shields.io/github/issues/PHPFastCGI/SpeedfonyBundle.svg)
![GitHub Stars](https://img.shields.io/github/stars/PHPFastCGI/SpeedfonyBundle.svg)
![GitHub License](https://img.shields.io/badge/license-GPLv2-blue.svg)
[![Build Status](https://travis-ci.org/PHPFastCGI/SpeedfonyBundle.svg?branch=master)](https://travis-ci.org/PHPFastCGI/FastCGIDaemon)
[![Coverage Status](https://coveralls.io/repos/PHPFastCGI/SpeedfonyBundle/badge.svg?branch=master)](https://coveralls.io/r/PHPFastCGI/SpeedfonyBundle?branch=master)

A symfony2 bundle which allows applications to reduce overheads by exposing symfony's Request-Response structure to a FastCGI daemon.

## Introduction

Using this bundle, symfony2 applications can stay alive between HTTP requests whilst operating behind the protection of a FastCGI enabled web server.

## Running the Daemon

To start the daemon listening on port 5000 use the command below. Production mode is selected here for the purposes of generating accurate benchmarks. We do not recommend that you use this package in production mode as it is not yet stable.

Check the FastCGI documentation for your chosen web server to find out how to configure it to use this daemon as a FastCGI application.

```sh
php app/console speedfony:daemon:run --target="tcp://localhost:5000" --env="prod"
```

If you are using apache, you can configure the FastCGI module to launch and manage the daemon itself. For this to work you must omit the "--target" option from the command and the daemon will instead listen for incoming connections on FCGI_LISTENSOCK_FILENO (STDIN).

## Current Status

This daemon is currently in early development stages and not considered stable. A
stable release is expected by September 2015.

Contributions and suggestions are welcome.
