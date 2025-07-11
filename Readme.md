# TYPO3 Extension powermail

Powermail is a well-known, editor-friendly, powerful and easy to use mailform extension for TYPO3 with a lots of
features (spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)

## 1. Documentation overview

* [Introduction](/Documentation/Index.md)
* [Development Model](/Documentation/DevelopmentModel.md)
* [Documentation for editors](/Documentation/ForEditors/Index.md)
* [Documentation for administrators](/Documentation/ForAdministrators/Index.md)
* [Documentation for developers](/Documentation/ForDevelopers/Index.md)
* [Documentation for developers](/Documentation/ForDevelopers/Index.md)
* [FAQ](/Documentation/FAQ/Index.md) (with a lot of stuff and best practice)
* [Changelog](/Documentation/Changelog/Index.md)
* [Support](/Documentation/Support/Index.md)
* [Additional links](/Documentation/Links/Index.md)

## 2. Installation

Quick guide:
- Just install this extension - e.g. `composer require in2code/powermail`
- Add a static typoscript template to your root template
- Add a new form (with one or more pages and with some fields to a page or a folder)
- Add a new pagecontent (plugin) with type "powermail" and choose the former saved form
- That's all, you can view the result in the frontend

## 3. Administration corner

### 3.1. Versions and Support

| Powermail   | TYPO3     | PHP       | Support/Development                           |
|-------------|-----------|-----------|-----------------------------------------------|
| 13.x        | 13.x      | 8.3 - 8.x | Features, Bugfixes, Security Updates          |
| 12.x        | 12.x      | 8.1 - 8.2 | Bugfixes, Security Updates                    |
| 11.x        | 12.x      | 8.1 - 8.2 | Support dropped                               |
| 10.x        | 11.x      | 7.4 - 8.1 | Support dropped (paid backports are possible) |
| 9.x         | 11.x      | 7.4       | Support dropped                               |
| 8.x         | 10.x      | 7.2 - 7.4 | Support dropped (paid backports are possible) |
| 7.x         | 8.7 - 9.x | 7.0 - 7.4 | Support dropped                               |
| 6.x         | 8.7 - 9.x | 7.0 - 7.x | Support dropped                               |

Do you need free support? There is a kind TYPO3 community that could help you.
You can ask questions at https://stackoverflow.com and tag your question with `TYPO3` and `Powermail`.
In addition there is a slack channel in the TYPO3 slack `ext-powermail`.

### 3.2. Changelog

Please look into the [changelog of powermail](/Documentation/Changelog/Index.md)

### 3.3. Suggested Extensions for powermail

- **email2powermail** Automatically convert emails to a link to a powermail form [Link](https://github.com/einpraegsam/email2powermail)
- **powermailrecaptcha** Google recaptcha [Link](https://github.com/einpraegsam/powermailrecaptcha)
- **invisiblerecaptcha** Google invisible recaptcha [Link](https://github.com/einpraegsam/invisiblerecaptcha)
- **powermailextended** Is just an example extension how to extend powermail with new fields or use signals [Link](https://github.com/einpraegsam/powermailextended)
- **powermail_cond** Add conditions (via AJAX) to powermail forms for fields and pages [Link](https://github.com/einpraegsam/powermail_cond)
- **powermail_fastexport** Extend powermail for faster export to .xlsx / .csv files. This is useful if you have many records to be exported. [Link](https://github.com/bithost-gmbh/powermail_fastexport)

### 3.4. Product Owner

The product owner and author of the extension is Marcus Schwemer from [in2code](https://www.in2code.de). Beside that every
in2code colleague is allowed to support further development if she/he wants. In addition there are a lot of other
contributors that helped to improve the extension with their *Pull Requests* - thank you for that!

### 3.5. Release Management

Powermail uses **semantic versioning** which basically means for you, that
- **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes.
- **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes.
- **major updates** (e.g. 1.0.0 => 2.0.0) normally includes basic refactoring, new features and also breaking changes.

We try to mark breaking changes in the [changelog](/Documentation/Changelog/Index.md)
with a leading **!!!** and try to explain what to do on an upgrade (e.g. VieHelper name changed from vh:foo to vh:bar in templates).

Based on `release early, release often` we try to release new versions as often as possible into TER and to github/packagist.

### 3.6. Automatic Testing

#### Behaviour tests

There is a huge testparcours that have to be passed before every release. For example there is an
[automatic test](/Tests/Behavior/Features/Pi1/Validation/Input/JsPhpValidation.feature)
where the browser tries to submit 18 different strings and numbers to a field that accepts only phone numbers to test
serverside validation. After that the same process is done for clientside valiation.
There are also some smaller tests like "Is it possible to submit a form on a page where two different forms are stored?".

See [readme.md](/Tests/Behavior/readme.md) for some more information about behat and selenium tests on powermail.

#### Unit tests

At the moment powermail offers many unit tests that have to be passed before every release. See more information
about unit tests or code coverage in powermail in the [readme.md](/Tests/Unit/readme.md)

### 3.7. Code quality

Beside respecting PSR-12 and TYPO3 coding guidelines, it's very important for the project to leave a file cleaner as before.
Especially because it's a really large extension with a lot of functionality and a history of over 17 years (!) and of course some
technical debts, that have to be fixed step by step.

Current quality tools are:

- php-cs-fixer
- phpstan (level 8)
- php linter
- TypoScript linter

### 3.8. Contribution

**Pull requests** are welcome in general! Nevertheless please don't forget to add a description to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

- Bugfixes: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue. We're going
to accept only bugfixes if I can reproduce the issue.
- Features: Not every feature is relevant for the bulk of powermail users. In addition: We don't want to make powermail
even more complicated in usability for an edge case feature. Please discuss a new feature before.


### 3.9. Development

Compile and minify (uglify) JavaScript, compress CSS:

```
$ cd Resources/Private
$ npm install
$ ./node_modules/.bin/gulp
```


## 4. Screenshots

### 4.1. Example form with bootstrap classes:

![Example form](Documentation/Images/frontend1.png "Example Form")


### 4.2. Backend module mail list:

![Backend Module](Documentation/Images/backend1.png "Backend Module")


### 4.3. Backend module reporting:

![Backend Module2](Documentation/Images/backend2.png "Backend Module2")

## 5. Additional supplementary extensions

### 5.1 EXT:powermail_frontend

This extension contains the previous plugins pi2 - pi4. Since the public release of v13 this functionality is only
available as a premium extension.

### 5.2 EXT:powermail_cleaner

EXT:powermail_cleaner gives administrators and editors more flexibility in cleaning up saved email records and in
displaying additional data protection texts per form. This is available as a premium extension.

### 5.4 EXT:powermail_powerpack

This extension will collect some additional functionality and will be available as a premium extension.
