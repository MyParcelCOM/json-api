# MyParcel.com Transformers
Shared library for transforming models into jsonapi responses.

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

#### Composer key
Composer needs to be able to install private packages. In order to do that it requires an access token
for GitHub.

- Login to GitHub.
- Go to `Settings` > `Personal access tokens` > `Generate new token`.
- Check the box next to `repo` and fill in the `description`.
- Click `generate`.
- Copy the generated token and paste it in your `.env` file where it says `<api-token>`.

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
