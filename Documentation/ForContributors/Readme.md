# TYPO3 Extension powermail - Documentation for contributors

If you want to contribute to the TYPO3 extension powermail, you are very welcome.

To make it easier to contribute, there are two docker based installations, with a complete TYPO3 setup and ready to
use.

## Development environments

### DDEV-based environment
#### Prerequisites

- Docker installed https://docs.docker.com/get-docker/
- ddev installed https://ddev.readthedocs.io/en/stable/

#### Project setup

- open a console in the project root
- run `ddev start`
- run `ddev initialize`

Now you will be able to work with the website

Frontend: https://powermail.ddev.site/ \
Backend: https://powermail.ddev.site/typo3


Username: admin \
Password: password

### "Plain" docker environment

#### Prequisites

- Docker installed https://docs.docker.com/get-docker/
- Dinghy Proxy installed https://github.com/codekitchen/dinghy-http-proxy

#### Project setup

- open a console in the project root
- run `make install-project`

Frontend https://local.powermail.de/ \
Backend: https://local.powermail.de/typo3

Username: admin \
Password: password

For all available `make` commands just run `make`

## Behaviour tests

More information on running behaviour tests is available here: [Behaviour tests](../../Tests/Behavior/readme.md)
