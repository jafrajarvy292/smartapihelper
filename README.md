[![License MIT](http://img.shields.io/github/license/jafrajarvy292/smartapihelper)](https://opensource.org/licenses/MIT)

[GitHub Page](https://github.com/jafrajarvy292/smartapihelper)  
[Documentation Page](https://sample.asuscomm.com/smartapihelper_documentation/html)

# SmartAPI Helper
This is a PHP library for [MeridianLink's](http://www.meridianlink.com) SmartAPI interface.

This library should be used only after a review of the SmartAPI Integration Guide. The integration guide covers basic knowledge the software developer should have before starting their project. After reviewing the guide, this library can be used to expedite the integration process.

The SmartAPI Integration Guide, which is included in the integration kit, can be obtained by reaching out to MeridianLink directly.

# Requirements
- PHP 7.1.0 or higher
- Extension: cURL
- Extension: OpenSSL
- Extension: DOM

# Installation Instructions
## With Composer
To install using composer:

    composer require jafrajarvy292/smartapihelper

If composer's autoload file is already included in your project, then this library will automatically be loaded.
## Manual Installation
Download the latest release and and extract to its own folder. Afterwards, require the autoloader file.

For example, if you saved this to a folder named `smartapihelper`, then your require statement should look something like the following:

    require "smartapihelper/autoload.php";

# Getting Started
Look through the `samples` folder for examples of using this library.