# TYPO3 Extension powermail

Powermail is a well-known, editor-friendly, powerful
and easy to use mailform extension with a lots of features
(spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)

## Quick installation

Please look at the manual for a big documentation at https://docs.typo3.org/typo3cms/extensions/powermail

Quick guide:
- Just install this extension - e.g. `composer require in2code/powermail` or download it or install it with the classic way (Extension Manager)
- Clear caches
- Add a new form (with one or more pages and with some fields to a page or a folder)
- Add a new pagecontent with type "powermail" and choose the former saved form
- That's it

## Changelog

Please look at https://docs.typo3.org/typo3cms/extensions/powermail/Changelog/Index.html

## Supported version (for latest powermail)

| Software    | Versions   |
| ----------- | ---------- |
| TYPO3       | 7.6 - 8.7  |
| PHP         | 5.5 - 7.1  |

## Need some extension possibilities for powermail?
- Automatically convert emails to a link to a powermail form with **email2powermail** (see https://github.com/einpraegsam/email2powermail)
- Google recaptcha with **powermailrecaptcha** (see https://github.com/einpraegsam/powermailrecaptcha)
- Google invisible recaptcha with **invisiblerecaptcha** (see https://github.com/einpraegsam/invisiblerecaptcha)
- **powermailextended** is just an example extension how to extend powermail with new fields or use signals (see https://github.com/einpraegsam/powermailextended)

## Example form with bootstrap classes:

![Example form](https://box.everhelper.me/attachment/445407/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-KmKJsSfGKDz6bnVO/screen.png "Example Form")


## Backend module mail list:

![Backend Module](https://box.everhelper.me/attachment/445409/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-HFuHtr8E9DoGfJE6/screen.png "Backend Module")


## Whats the difference between version 3 and 2

- General update for TYPO3 7.6 and 8.x
- Table name correction from plural to singular (..mails => ..mail, ..fields => ..field)
  - Updated ext_tables.sql
  - Converter script to convert old tablenames to new tablenames
    - Automaticly on extension installation
    - Start manually from extension manager
- Add bootstrap
  - Frontend
    - Add static template to add bootstrap classes to forms and fields
    - Add constant to load bootstrap.css from powermail folder
    - Update Layouts, Templates, Partials (Pi1 and Pi2)
  - Backend
    - Update modules with new markup
- Backend
  - PluginInformation refactoring
  - Remove old form converter (converted 1.x to 2.x forms)
  - Remove unneeded overview actions
  - Enable table garbage collector scheduler tasks per default
- General
  - Add DataProcessors to change mail object before it's persisted or used in mails
  - Own spamshield methods could be registered via TypoScript now
  - Refactoring of upload function
  - Add signals to ValidationDataAttributeViewHelper, PrefillFieldViewHelper and PrefillMultiFieldViewHelper
  - Remove outdated parts of code (PHP, Templates)
  - Some code cleanup
  - Manual update
