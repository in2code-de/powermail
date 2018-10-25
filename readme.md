# TYPO3 Extension powermail

Powermail is a well-known, editor-friendly, powerful
and easy to use mailform extension for TYPO3 with a lots of features
(spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)

## 1. Installation

Please look at the manual for a detailed documentation at [official extension documentation of TYPO3](https://docs.typo3.org/typo3cms/extensions/powermail)

Quick guide:
- Just install this extension - e.g. `composer require in2code/powermail` or download it or install it with the classic way (Extension Manager)
- Clear caches
- Add a new form (with one or more pages and with some fields to a page or a folder)
- Add a new pagecontent with type "powermail" and choose the former saved form
- That's it

## 2. Administration corner

### 2.1. Versions and support

| Powermail   | TYPO3      | PHP       | Support/Development                     |
| ----------- | ---------- | ----------|---------------------------------------- |
| 7.x         | 8.7 - 9.x  | 7.0 - 7.x | Features, Bugfixes, Security Updates    |
| 6.x         | 8.7 - 9.x  | 7.0 - 7.x | Support dropped    |
| 5.x         | 8.7 - 9.x  | 7.0 - 7.x | Support dropped                         |
| 4.x         | 7.6 - 8.7  | 5.5 - 7.2 | Bugfixes, Security Updates              |
| 3.x         | 7.6 - 8.7  | 5.5 - 7.2 | Security Updates                        |
| 2.18 - 2.25 | 6.2 - 7.6  | 5.5 - 7.0 | Security Updates                        |
| 2.2 - 2.17  | 6.2 - 7.6  | 5.3 - 7.0 | Support dropped                         |

### 2.2. Changelog

Please look into the [official extension documentation in changelog chapter](https://docs.typo3.org/typo3cms/extensions/powermail/Changelog/Index.html)

### 2.3. Suggested Extensions for powermail

- **email2powermail** Automatically convert emails to a link to a powermail form [Link](https://github.com/einpraegsam/email2powermail)
- **powermailrecaptcha** Google recaptcha [Link](https://github.com/einpraegsam/powermailrecaptcha)
- **invisiblerecaptcha** Google invisible recaptcha [Link](https://github.com/einpraegsam/invisiblerecaptcha)
- **powermailextended** Is just an example extension how to extend powermail with new fields or use signals [Link](https://github.com/einpraegsam/powermailextended)
- **powermail_fastexport** Extend powermail for faster export to .xlsx / .csv files. This is useful if you have many records to be exported. [Link](https://github.com/bithost-gmbh/powermail_fastexport)

### 2.4. Conflicts

* At the moment powermail does not support TYPO3 workspaces (See [in2publish](https://github.com/in2code-de/in2publish_core) as an alternative to workspaces)
* The extensions compatibility6 and compatibility7 could conflict with powermail

### 2.5. Future plans

There are some ideas for future developments (like removing jQuery dependency, etc...) but there is no final roadmap.
Nevertheless it's planned to release a version for **TYPO3 10** (TYPO3 9 is of course already supported).

### 2.6. Product Owner

The product owner and author of the extension is Alex Kellner from [in2code](https://www.in2code.de). Beside that every
in2code colleague is allowed to support further development if she/he wants. In addition there are a lot of other
contributors that helped to improve the extension with their *Pull Requests* - thank you for that!

### 2.7. Release Management

Powermail uses **semantic versioning** which basicly means for you, that
- **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes.
- **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes.
- **major updates** (e.g. 1.0.0 => 2.0.0) normally includes basic refactoring, new features and also breaking changes.

We try to mark breaking changes in the [changelog](https://docs.typo3.org/typo3cms/extensions/powermail/Changelog/Index.html)
with a leading **!!!** and try to explain what to do on an upgrade (e.g. VieHelper name changed from vh:foo to vh:bar in templates).

In addition powermail is using [Git Flow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) as Git workflow.
That means that there is one branch which contains new and untested code: **develop**.
The branch **master** only contains tested code which will also be tagged from time to time.

Based on `release early, release often` we try to release new versions as often as possible into TER and to github/packagist.

### 2.8. Composer and Packagist

This extension is, of course available on [packagist](https://packagist.org/packages/in2code/powermail).
You can install it via composer with `composer require in2code/powermail`

And of course you don't need to run your TYPO3-environment in composer mode. Powermail works also in classic mode.

### 2.9. Automatic Testing

#### Behaviour tests

There is a huge testparcours that have to be passed before every release. For example there is an
[automatic test](https://github.com/einpraegsam/powermail/blob/develop/Tests/Behavior/Features/Pi1/Validation/Input/JsPhpValidation.feature)
where the browser tries to submit 18 different strings and numbers to a field that accepts only phone numbers to test
serverside validation. After that the same process is done for clientside valiation.
There are also some smaller tests like "Is it possible to submit a form on a page where two different forms are stored?".

See [readme.md](https://github.com/einpraegsam/powermail/tree/develop/Tests/Behavior) for some more information about behat and selenium tests on powermail.

#### Unit tests

At the moment powermail offers 543 (and growing) unit tests that have to be passed before every release. See more information
about unit tests or code coverage in powermail in the [readme.md](https://github.com/einpraegsam/powermail/tree/develop/Tests/Unit)

### 2.10. Code quality

Beside respecting PSR-2 and TYPO3 coding guidelines, it's very important for the project to leave a file cleaner as before.
Especially because it's a really large extension with a lot of functionality and a history of 10 years (!) and of course some
technical debts, that have to be fixed step by step (e.g. moving logic completely to Domain folder, ...).
Look at [Sonarqube](https://ter-sonarqube.marketing-factory.de/dashboard?id=powermail) for some interesting details on that.

### 2.11. Contribution

**Pull requests** are welcome in general! Nevertheless please don't forget to add a description to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

- Bugfixes: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue. We're going
to accept only bugfixes if I can reproduce the issue.
- Features: Not every feature is relevant for the bulk of powermail users. In addition: We don't want to make powermail
even more complicated in usability for an edge case feature. Please discuss a new feature before.


### 2.12. Development

Compile and minify (uglify) JavaScript, compress CSS:

```
$ cd Resources/Private
$ npm install
$ ./node_modules/.bin/gulp
```


## 3. Screenshots

### 3.1. Example form with bootstrap classes:

![Example form](https://box.everhelper.me/attachment/445407/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-KmKJsSfGKDz6bnVO/screen.png "Example Form")


### 3.2. Backend module mail list:

![Backend Module](https://box.everhelper.me/attachment/445409/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-HFuHtr8E9DoGfJE6/screen.png "Backend Module")


## 4. What's new

### 4.1. What's new in powermail 7.0

- A disclaimer functionality was added to sendermail and optinmail. So now sender can delete all their data from the server (really delete) by clicking a link in the mail.

### 4.2. What's the difference between version 5 and 4

- A large refactoring
  - For TYPO3 9.x
  - And for PHP 7.0 - 7.2
- Migration for powermail 2.x was removed - see my [post](https://gist.github.com/einpraegsam/a02bb69c29aa747de4ffb613704bbd7a) how to upgrade from 2.x to 5.x

### 4.3. Whats the difference between version 4 and 3

- A small refactoring of the mail related service classes
- Add a possibility to create columns in a form for e.g. bootstrap
- TYPO3 7.6 is still supported in this major version

### 4.4. Whats the difference between version 3 and 2

- General update for TYPO3 7.6 and 8.x
- Table name correction from plural to singular (..mails => ..mail, ..fields => ..field)
- Add bootstrap
- Remove old form converter (converted 1.x to 2.x forms)
- Enable table garbage collector scheduler tasks per default
- Add DataProcessors to change mail object before it's persisted or used in mails
- Own spamshield methods could be registered via TypoScript now
- Refactoring of upload function
- Add signals to ValidationDataAttributeViewHelper, PrefillFieldViewHelper and PrefillMultiFieldViewHelper
- Some code cleanup
