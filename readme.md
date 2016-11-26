# TYPO3 Extension powermail

Powermail is a well-known, editor-friendly, powerful
and easy to use mailform extension with a lots of features
(spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)

# Whats new in Version 3

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

# Official documentation

see https://docs.typo3.org/typo3cms/extensions/powermail/ for the complete documentation


### Example form with bootstrap classes:

![Example form](https://box.everhelper.me/attachment/445407/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-KmKJsSfGKDz6bnVO/screen.png "Example Form")


### Backend module mail list:

![Backend Module](https://box.everhelper.me/attachment/445409/3910b9da-83f9-477d-83b1-f7e21ead9433/262407-HFuHtr8E9DoGfJE6/screen.png "Backend Module")
