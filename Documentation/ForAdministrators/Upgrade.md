y# Upgrade Powermail

## Introduction

Maybe you need to upgrade powermail to this version. The following instructions should help you to upgrade as smooth
as possible.
**Note:** Please do not modify any extension files to ensure easy upgrading.

## Upgrade Instructions

You find instructions for each major version. [Upgrade Instructions](/Documentation/Changelog/RUpgradeInstructions.md)

## Any Upgrade

### Description

Do you have **problems after a powermail upgrade**?

### Follow these steps

Please follow these steps before you create a bug entry on GitHub:

* Clean all caches in the Install Tool
* Remove all files and folders in typo3temp/
* Try to use the default TypoScript (comment out your TypoScript)
* Try to use default Templates (and Partials and Layouts)
* Are there any upgrade wizard steps regarding to powermail that you can execute?
* Reload Frontend
* Reload Backend
* Check again
* Still problems? Please report to `GitHub <https://github.com/einpraegsam/powermail/issues>`_


## Bugfix Upgrade

### Example

Upgrade from Powermail 8.0.x to 8.0.y

### Details

No breaking changes in database or HTML-Template-Files.

An upgrade should be very easy. Follow these steps:

* Upgrade powermail via composer or oldschool with the Extension Manager
* Clean all caches in the Install Tool (just removing all files in typo3temp/* may not help you!)
* Reload Frontend
* Reload Backend
* Done


## Minor Upgrade

### Example

Upgrade from Powermail 8.x.a to 8.y.b

### Details

Normally there are no breaking changes. If there is a required change, the update is marked with `!!!` in TER and in
Changelog. Probably there are changes in the Database, but the already stored values keep working.

Follow these steps:

* Upgrade powermail via composer or oldschool with the Extension Manager
* Use original HTML-Files (Templates, Partials, Layouts) for testing
* Clean all caches in the Install Tool (just removing all files in typo3temp/* may not help you!)
* Reload Frontend
* Reload Backend
* Update Backend User Rights for the new powermail version
* Done


## Major Upgrade

### Example 1

Upgrade from Powermail 7.x to 8.x

### Details 1

Many breaking changes in database, Template Files, Scheduler Tasks, etc... are possible.
You have to upgrade your own HTML-Templates (and Partials and Layouts) by your own. In addition the used TypoScript
could have modifications - upgrade your TypoScript config.

Follow these steps:

* Upgrade powermail via composer or oldschool with the Extension Manager
* Are there any upgrade wizard steps regarding to powermail that you can execute? Execute them.
* Clean all caches in the Install Tool (just removing all files in typo3temp/* may not help you!)
* Use original HTML-Files (Templates, Partials, Layouts) for testing if there are still problems
* Use original TypoScript configuration for testing if there are still problems
* Reload Frontend
* Reload Backend
* Update Backend User Rights for the new powermail version
* Done
