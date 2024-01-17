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

Frontend: https://powermail-<TYPO3-version>.ddev.site/ \
Backend: https://powermail-<TYPO3-version>.ddev.site/typo3


Username: admin \
Password: password

### "Plain" docker environment

#### Prequisites

- Docker installed https://docs.docker.com/get-docker/
- Dinghy Proxy installed https://github.com/codekitchen/dinghy-http-proxy

#### Project setup

- open a console in the project root
- run `make install-project`

Frontend https://local-<TYPO3-version>.powermail.de/ \
Backend: https://local-<TYPO3-version>.powermail.de/typo3

Username: admin \
Password: password

For all available `make` commands just run `make`

## Behaviour tests

More information on running behaviour tests is available here: [Behaviour tests](../../Tests/Behavior/readme.md)

## Frontend Development

There are some javascript libraries and (s)css files necessary for EXT:powermail to work properly in frontend context.

The sources files are located in `Resources/Private/Build`.

There is a small build pipeline to build the assets. The artifacts are committed into the repository.

* have nvm installed (https://github.com/nvm-sh/nvm#install--update-script)
* `nvm i` will install the correct npm version
* `nvm use` will change to needed npm version
* `npm i` will install the node modules (if not yet installed)
* `npm run build` will build the necessary files
* `npm run watch` will watch the files and rebuild them on changes

