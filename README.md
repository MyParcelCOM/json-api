# MyParcel.com Exceptions
Shared library for exceptions and exception handling.

## Content
- [Installation](#installation)
- [Setup](#setup)
- [Commands](#commands)

### Installation
The library uses Docker to run php and composer. To install Docker, follow the steps in the [wiki](https://staging-wiki.myparcel.com/development/docker/).

### Setup
To setup the project, run the following command:
```bash
./mp.sh composer install
```

### Commands
The following commands are available:
- `./mp.sh composer <args>` - Run composer inside the container.
- `./mp.sh php <args>` - Run any command on the ElasticSearch container.
- `./mp.sh test <args>` - Run the PHPUnit tests.

#### Composer commands
A few composer scripts have been defined, you can call these using the following commands:
- `./mp.sh composer check-style` - Check if the code is PSR-2 compliant.
- `./mp.sh composer fix-style` - Automatically fix non-PSR-2 code (not all errors can be automatically fixed).

> **TIP:** You will run many ./mp.sh commands. Alias all the things!
```bash
# ~/.bashrc

alias mp="./mp.sh"
```
