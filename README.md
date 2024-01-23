# MyParcel.com JSON API
[![GitHub Actions](https://img.shields.io/github/actions/workflow/status/MyParcelCOM/json-api/ci.yml)](https://github.com/MyParcelCOM/json-api/actions)
[![Packagist](https://img.shields.io/packagist/v/MyParcelCOM/json-api.svg)](https://packagist.org/packages/myparcelcom/json-api)

Shared library with JSON API related exceptions, interfaces, traits and utility classes.

## Installation
The library uses Docker to run php and composer. To install Docker, follow the steps in the [documentation](https://docs.myparcel.com/development.html#docker).

### Setup
To set up the project, run:
```bash
./mp.sh setup
```

## Commands
The following commands are available for development:

`./mp.sh composer <args>` - Run composer inside the container.

`./mp.sh php <args>` - Run any command on the php container.

`./mp.sh test <args>` - Run the PHPUnit tests.

A few composer scripts have been defined, you can call these using the following commands:

`./mp.sh composer check-style` - Check if the code is PSR-2 compliant.

`./mp.sh composer fix-style` - Automatically fix non-PSR-2 code (not all errors can be automatically fixed).

## License
All software by MyParcel.com is licensed under the [MyParcel.com general terms and conditions](https://www.myparcel.com/legal). 
