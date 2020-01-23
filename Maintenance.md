# Maintenance Instructions
This document provides instructions on maintaining this library and its documentation. End-users can ignore this file: these instructions are only useful for the author and contributors.

These instructions have been written from the perspective of a Windows machine, but backslashes have been replaced with forward slashes to minimize edits needed to get these working on a Linux machine, since forward slashes work on Windows machines, as well.

## Unit Testing
Unit testing is ran with [PHPUnit](https://phpunit.de) and should be ran on all relevant files under the `tests` directory. The `bootstrap.php` file should be included, as it ensures class autoloading and instantiates needed variables and objects. An example of the command line to be ran is below. This will vary based on operating system and how you have PHPUnit installed:

    C:\projects\smartapihelper\tests> php C:/php/phpunit.phar --bootstrap bootstrap.php .

Notice in this example the working folder is the `tests` folder, so using the period to indicate the target means we're running PHPUnit on the folder itself, along with all sub-directories.

## Documentation Generation
[Doxygen](http://doxygen.nl) is used to create the documentation for this library. Though Doxygen does have a wizard that provides a GUI, using the command line to handle this is highly recommended.

Below is an example of the command line to run to generate the documentation for this library:

    C:\projects\smartapihelper> doxygen doxygen/doxygen.config

Notice in the example our working directory is the root folder for this library. You must run Doxygen from this folder, else the relative paths used in the config file will not point to the correct resources. Note we also included the `doxygen.config` file as the config file to use. This config file contains all other information that is needed to generate the documentation.

Some important notes below:

- The `doxygen_filter.php` file is a filter that must be used when generating the documentation. The config file already includes this instruction and you can see it under the config file's `INPUT_FILTER` setting.
- The input files are all listed in the config file under the `INPUT` setting. If a new folder is added to the source code, it should be added to this list to ensure documentation is generated for it.
- The README.md file in the root directory is included in the generated documentation and appears on the home page.

## Versioning
This library uses [semantic versioning](https://semver.org). The version of this library is something we include in the user agent string when submitting the HTTP request to the SmartAPI endpoint. Each time this library is updated, the current version number should be updated in the `src/VERSION.config` file. This file should contain only the version number with no trailing spaces or line breaks. Our library will read from this file when populating the version number into the user agent string.