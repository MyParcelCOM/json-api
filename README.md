# MyParcel.com Exceptions
Shared library for exceptions and exception handling.

## TODO
- [x] Setup Laravel repo.
- [ ] Add initial exceptions and handler.
- [ ] Add tests.

## Content
- [Installation](#installation)
- [Setup](#setup)
- [Commands](#commands)

### Installation
The library uses Docker to run php and composer. To install Docker, follow the steps below for your preferred OS.

#### Mac
Install Docker for Mac from [https://docs.docker.com/docker-for-mac/install/](https://docs.docker.com/docker-for-mac/install/).

#### Windows
Install Docker for Windows from [https://docs.docker.com/docker-for-windows/install/](https://docs.docker.com/docker-for-windows/install/).

#### Linux
Install Docker by running the following command:
```bash
curl -sSL https://get.docker.com/ | sh
```

Then install Docker Compose by following the instructions [here](https://github.com/docker/compose/releases).

Finally assign yourself to the Docker group:
```bash
sudo usermod -aG docker $(whoami)
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
