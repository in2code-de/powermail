# TYPO3 Extension powermail - Documentation for contributors

If you want to contribute to the TYPO3 extension powermail, you are very welcome.

To make it easier to contribute, we provide a ddev enviroment, with a complete TYPO3 setup and ready to
use.

## Prerequisites

- Docker installed https://docs.docker.com/get-docker/
- ddev installed https://ddev.readthedocs.io/en/stable/

## Project setup

- open a console in the project root
- run `ddev start`
- run `ddev initialize`

Now you will be able to work with the website

Frontend: https://powermail-<TYPO3-version>.ddev.site/ \
Backend: https://powermail-<TYPO3-version>.ddev.site/typo3

Username: admin \
Password: password

## PHP tests

There are several test types preconfigured in EXT:powermail. These are

- phplint
- php-cs-fixer
- phpstan
- php unit test

All can be triggered locally via `composer`.

```bash
ddev exec composer run test:php:phpstan
```

### PHPstan: Update baseline

As the time of this writing (while introducing phpstan in Sept. 2024), there are slightly over 1000 issues in the
phpstan base. (Hopefully) They will be reduced, in future development. If you fixed one or more of them, it will be
reported in the github pipeline or locally. If done so, they must be removed from the baseline with the following
command.

```bash
ddev exec composer run test:php:phpstan:generate-baseline
```



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

