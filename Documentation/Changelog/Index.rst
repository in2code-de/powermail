.. include:: ../Includes.txt


Changelog
=========

All changes are documented on https://docs.typo3.org/typo3cms/extensions/powermail/Changelog/


.. t3-field-list-table::
 :header-rows: 1

 - :Version:
      Version
   :Date:
      Release Date
   :Changes:
      Release Description

 - :Version:
      7.0.0
   :Date:
      2018-10-25
   :Changes:

      * Feature: Add a disclaimer link functionality to sender- and optinmail (sender can remove his own mail completely from database now)
      * Task: Increase general hash length from 10 to 64 characters (optin links and new disclaimer links)
      * Task: Some cleanup
      * Bugfix: Reanimate location field (broken because of change in the google API). Now we're using openstreetmap for this.

 - :Version:
      6.2.0
   :Date:
      2018-10-21
   :Changes:

      * Feature: Improve performance with sql indices
      * Feature: Also alow markers in fields in additional languages with no parent element
      * Feature: Make $this->settings readable when extending controller with signals
      * Task: Replace eID script for marketing tracking with a TypeNum script - that bypasses the chash-problem now
      * Task: Update frontend toolchain gulp task and commit a package-lock.json
      * Task: Some documentation updates
      * Task: Make ConfigurationUtility more future proof
      * Task: Remove some other deprecated function calls for future TYPO3 versions
      * Task: Remove deprecated @validate notations
      * Task: Remove deprecated Extbase TypoScriptService calls
      * Bugfix: Don't show forms twice in plugin selection in TYPO3 9.5
      * Bugfix: Make endsWith() more robust
      * Bugfix: Allow also arrays with ManipulateValueWithTypoScriptViewHelper
      * Bugfix: Prevent exception in GetFileWithPathViewHelper
      * Bugfix: Prevent exception in ManipulateValueWithTypoScriptViewHelper
      * Bugfix: Prevent exception in LocalizationUtility

 - :Version:
      6.1.0
   :Date:
      2018-07-16
   :Changes:

      * Feature: Sort powermail tables in backend list view in a useful direction
      * Feature: Add CSS-Classes for powermail_frontend that are reflecting the current action
      * Feature: Add aria-required attributes for required fields
      * Task: Update documentation
      * Task: Add .editorconfig
      * Bugfix: Allow a preselection of country fields
      * Bugfix: Don't validate on not-supported fields in FE (because of a possible misconfiguration)
      * Bugfix: Fix typos in documentation

 - :Version:
      6.0.0
   :Date:
      2018-05-07
   :Changes:

      * General GDPR release:
      * !!! Task: Disable session-spam-check by default (to prevent generating a cookie)
      * !!! Task: Disable IP-logging by default
      * Task: Add a privacy documentation with some hints
      * !!! Task: Randomize filenames of uploaded files by default
      * Task: Update testing readme
      * Bugfix: Update testing requirements

 - :Version:
      5.6.0
   :Date:
      2018-04-24
   :Changes:

      * Task: Add constant for sender email for spam notification mails
      * Task: Update spamshield documentation

 - :Version:
      5.5.0
   :Date:
      2018-02-21
   :Changes:

      * Feature: Remove all invisible HTML tags in plaintext mails
      * Bugfix: Prevent exception in Pi2 (regression related to strict_types in PHP)
      * Bugfix: Prevent exception when using marketing reports in backend (regression related to strict_types in PHP)
      * Bugfix: Prevent exception when using password fields (regression related to strict_types in PHP)

 - :Version:
      5.4.0
   :Date:
      2018-02-03
   :Changes:

      * Bugfix: Prevent SQL error when powermail is in replaceIrreWithElementBrowser-mode
      * Feature: Allow exporting of hidden property of mails in backend module
      * Task: Small code refactoring to simplify extending of ValidationDataAttributeViewHelper

 - :Version:
      5.3.2
   :Date:
      2018-01-31
   :Changes:

      * Bugfix: Prevent SQL error for strict mode if a form is copied in backend
      * Task: Add some administration documentation to readme.md

 - :Version:
      5.3.1
   :Date:
      2018-01-29
   :Changes:

      * Bugfix: Fix type error in backend if custom validators are used
      * Task: Small code cleanup

 - :Version:
      5.3.0
   :Date:
      2018-01-26
   :Changes:

      * Feature: Collect markup of all fieldlabels in one partial. Should be not a breaking change.
      * Feature: Use spaceless ViewHelper for Form outputs to increase FE performance

 - :Version:
      5.2.2
   :Date:
      2018-01-25
   :Changes:

      * Bugfix: Correct notice if TYPO3 is running in classic mode
      * Bugfix: Don't mark powermail as unsafe if there are no informations about the current version

 - :Version:
      5.2.1
   :Date:
      2018-01-24
   :Changes:

      * Task: Change GPL string in composer.json to make packagist happy
      * Task: BE Module: Add notice if TYPO3 is running in composer mode
      * Task: BE Module: Add notice for "no version information" only in Classic Mode
      * Bugfix: Prevent exception in backend module function test if extensionmanager table is empty

 - :Version:
      5.2.0
   :Date:
      2018-01-24
   :Changes:

      * Feature: Don't render labels (form, page, field) if layout is turned to "nolabel"
      * Feature: Add a condition for TypoScript to listen to form submits: [In2code\Powermail\Condition\IsPowermailSubmittedCondition]
      * Bugfix: Accept also partialRootPaths without trailing slash
      * Bugfix: Remove powermail version note from RSS in Pi2
      * Task: Add another signal to manipulate filenames of uploaded files
      * Task: Add some more unit tests
      * Task: Some code cleanup
      * Task: Remove outdated include of partialRootPath
      * Task: Some documentation updates

 - :Version:
      5.1.0
   :Date:
      2018-01-15
   :Changes:

      * Feature: Add breaker class to disable spamshield if given IP address matches
      * Feature: Add breaker class to disable spamshield if there is a defined string in an answer
      * Feature: Possibility to add own breaker classes to disable spamshield on individual conditions

 - :Version:
      5.0.1
   :Date:
      2018-01-15
   :Changes:

      * Task: Very small code cleanup
      * Bugfix: Fix test for secure version in backend
      * Bugfix: Prevent exception if extension configuration is empty

 - :Version:
      5.0.0
   :Date:
      2018-01-14
   :Changes:

      * Task: Powermail for TYPO3 8.7 and 9.x
      * !!!Task: Changed ViewHelper from vh:string.RawAndRemoveXss to vh:string.escapeLabels in all Templates/Partials
      * !!!Task: Some smaller changes in Templates and Layouts
      * Task: Remove update script (Update from powermail 2 to 3 or newer)
      * Task: Larger refactoring for PHP 7 strict mode and TYPO3 9
      * Task: Add readme how to start unit tests (with and without code coverage)
      * Task: Add readme how to start behaviour tests (behat with selenium)
      * Task: Replace font of calculating captcha with a complete free one and add a notice

 - :Version:
      4.4.0
   :Date:
      2017-12-22
   :Changes:

      * Feature: Backend module: Allow exporting of new fields
      * Feature: CommandController: Allow exporting of new fields
      * Task: Bring testing framework via composer
      * Task: Small code refactoring

 - :Version:
      4.3.5
   :Date:
      2017-12-20
   :Changes:

      * Bugfix: Mails are not stored 3 times any more on each ajaxsubmit (with redirect configuration)
      * Bugfix: Type in constants for bootstrapPath was wrong (affected in contstant editor)
      * Task: Log mail sending errors as errors and not was warning
      * Task: Describe in documentation how to register a callback function on an ajax submit
      * Task: Small change in documentation

 - :Version:
      4.3.4
   :Date:
      2017-12-11
   :Changes:

      * Task: Small optic cleanup in backend module action selector
      * Task: Small code cleanup

 - :Version:
      4.3.3
   :Date:
      2017-12-07
   :Changes:

      * Bugfix: Fix prefilling of a datefield in chrome with enforceDatepicker
      * Bugfix: Fix pagebrowser in backend module overviewBeAction()

 - :Version:
      4.3.2
   :Date:
      2017-12-05
   :Changes:

      * Bugfix: Revert FlexForm receiver.type commit from 4.3.1 because TYPO3 throws an exception when the field is not available for displayCond

 - :Version:
      4.3.1
   :Date:
      2017-12-05
   :Changes:

      * Bugfix: FlexForm receiver.type should be an exclude field to disable this field for editors if wanted
      * Task: Small documentation update

 - :Version:
      4.3.0
   :Date:
      2017-11-25
   :Changes:

      * Bugfix: Fix namespace for PluginPreview
      * Bugfix: Small fix to not crash backend module in workspace
      * Task: Update signals in documentation

 - :Version:
      4.2.0
   :Date:
      2017-11-14
   :Changes:

      * Feature: Register your own JS tasks on AJAX complete now in fireAjaxCompleteEvent()  `#182 <https://github.com/einpraegsam/powermail/issues/182>`_
      * Bugfix: Avoid exception in plugin preview together with oldschool extension plugins
      * Bugfix: Backend Module list: Fix search in additional fields
      * Bugfix: Fix typo in german frontend and backend labels
      * Task: Never return an empty type for localized fields (even if there is something wrong in database)
      * Task: Documentation update
      * Task: Small code cleanup

 - :Version:
      4.1.0
   :Date:
      2017-10-16
   :Changes:

      * Task: Increase performance for larger forms
      * Task: Some code cleanups
      * Task: Update UserFunc documentation part
      * Task: Add a lib.receiver example documentation

 - :Version:
      4.0.2
   :Date:
      2017-09-29
   :Changes:

      * Bugfix: Values are not escapted any more by usage of {powermailAll} together with format.nl2br
      * Bugfix: Prevent JS error if parsley is not included
      * Feature: Add a public function in AbstractValidator to decide when there should be a validation and when not
      * Task: Compare only field uids in input validation to allow extending a field model with an own model
      * Task: Some small code refactoring
      * Task: Add a behaviour test for multiline-input in a textarea
      * Task: Add a note in manual for a conflict with extension compatibility6

 - :Version:
      4.0.1
   :Date:
      2017-09-18
   :Changes:

      * Bugfix: Fatal error for TYPO3 7.6 and powermail 4.0.0 - https://github.com/einpraegsam/powermail/issues/174
      * Bugfix: Fix illegal string offset - https://github.com/einpraegsam/powermail/issues/169

 - :Version:
      4.0.0
   :Date:
      2017-09-18
   :Changes:

      * Feature: Add a viewhelper for building responsive columns in forms (e.g. via bootstrap)
      * Feature: Make attachments also work on double opt-in mails
      * Task: Update documentation: Adding JavaScript validators with parsley in a modern way
      * Task: Update documentation: How to use own ViewHelpers in RTE fields in FlexForm
      * Task: Update documentation: How to debug mail failures
      * Task: Code cleanup and refactoring
      * Bugfix: Use advanced field types for command controller exports
      * Bugfix: Don't try to load glyphicons in Backend.css

 - :Version:
      3.22.1
   :Date:
      2017-09-06
   :Changes:

      * No code changes to 3.22.0 - new version due to TER security incident. See https://typo3.org/teams/security/security-bulletins/psa/typo3-psa-2017-001/

 - :Version:
      3.22.0
   :Date:
      2017-08-28
   :Changes:

      * Feature: Add getters for some properties in SendMailService class
      * Bugfix: Backend module list: Make sorting work again
      * Bugfix: Backend module list: Don't show duplicates on search
      * Bugfix: Backend module list: Keep filter params on page change
      * Bugfix: Fix typo in constants comment
      * Bugfix: Set same wrapping classname in password mirror field
      * Task: Update .htaccess file in Private folder for newer Apache versions

 - :Version:
      3.21.1
   :Date:
      2017-08-11
   :Changes:

      * Bugfix: Allow markernames with underscore again. Fixes a bug from february with wrong markernames like marker0101010 or markerAbCd

 - :Version:
      3.21.0
   :Date:
      2017-08-11
   :Changes:

      * Task: Use BackendUtility::getRecord() instead of exec_SELCT...
      * Task: Some documentation updates
      * Task: Enable marketing tracking with cHash check in TYPO3
      * Task: Add documentation how to prevent duplicate emails in webforms
      * Bugfix: Use TSFE:sys_language_uid instead of GP:L for marketing infos
      * Bugfix: Fix empty mails in backend module
      * Bugfix: Fix typo in Partial for hidden fields

 - :Version:
      3.20.0
   :Date:
      2017-07-07
   :Changes:

      * Feature: Use default TYPO3 settings when sender name/email is empty
      * Task: Some small performance updates for backend modules
      * Task: Some small code cleanups
      * Task: Update parsley.js from 2.2.0 to 2.7.2
      * Task: Prevent autofill from chrome for honeypot fields
      * Bugfix: Fix CSV export in T3 8.7
      * Bugfix: Fix Datefields in T3 7.6
      * Bugfix: Prevent exceptions in backend module for uploads

 - :Version:
      3.19.0
   :Date:
      2017-05-21
   :Changes:

      * Task: Update TCA for TYPO3 8.7
      * Task: Remove not needed backend check for filled markers in localized field tables
      * Task: Some performance improvements in some ViewHelpers
      * Task: Change uploadable file extension list in default constants
      * Task: Small documentation update
      * Bugfix: Prevent sql error on field localization if sql is in strict mode
      * Bugfix: DateConverter UserFunc should return an empty string on errors
      * Bugfix: Prevent small error in T3 log if VariableInVarialbeViewHelper is called in PHP7

 - :Version:
      3.18.2
   :Date:
      2017-05-10
   :Changes:

      * Bugfix: Make RTE work again under TYPO3 7.6
      * Bugfix: Fix requirements for TYPO3 in composer.json

 - :Version:
      3.18.1
   :Date:
      2017-05-03
   :Changes:

      * Bugfix: Prevent empty p-tags in CK-Editor in T3 8.7
      * Bugfix: Show powermail all in optin mails again
      * Bugfix: Fix links in optin mails

 - :Version:
      3.18.0
   :Date:
      2017-04-23
   :Changes:

      * Task: Make extension fit for new TYPO3 8.7 LTS testparcours
      * !!!Task: Encode html field output by default. Disable via TypoScript constants: `plugin.tx_powermail.settings.misc.htmlForHtmlFields=1`
      * !!!Task: Encode field labels by default. Disable via TypoScript constants: `plugin.tx_powermail.settings.misc.htmlForLabels=1`
      * Bugfix: Captcha image resource fix

 - :Version:
      3.17.0
   :Date:
      2017-04-01
   :Changes:

      * Feature: Add TypoScript condition that checks if a powermail plugin is on the current page
      * Task: Improve handling of routes in backend
      * Task: Update CSS classes in backend module for some tests
      * Bugfix: Show form icon in backend overview forms module

 - :Version:
      3.16.0
   :Date:
      2017-03-26
   :Changes:

      * Feature: Show error messages instead of a broken captcha on server misconfiguration
      * Task: Add own language labels for generic fields to support TYPO3 7.6 and 8.7
      * Bugfix: Make backlink in backend work again when opening a form from the plugin
      * Bugfix: Fix small typo in documentation
      * Bugfix: Blacklist-Spam-Method should also check email addresses
      * Bugfix: Change validation message if only one checkbox is in use

 - :Version:
      3.15.0
   :Date:
      2017-03-20
   :Changes:

      * Feature: Allow field prefilling for cached forms if no_cache=1
      * Feature: Add css bootstrap classes also for submit and the create view
      * Bugfix: Can not edit a form with a fluid viewhelper in a field title
      * Bugfix: Field parent pointer of translation overridden in TYPO3 CMS 8

 - :Version:
      3.14.0
   :Date:
      2017-03-13
   :Changes:

      * Task: Finally allow upload fields in Pi2
      * Bugfix: Make localized forms without parent selectable in backend
      * Bugfix: AjaxFormSubmit in IE was possibly broken

 - :Version:
      3.13.0
   :Date:
      2017-03-02
   :Changes:

      * Feature: Add .gitattributes file
      * Feature: Passing variable $mail to signal getVariablesWithMarkersFromMail
      * Task: Always render default marker name in frontend even in localized forms for TYPO3 8.6 and newer
      * Task: Remove l10n_mode=noCopy for TYPO3 8.6 and newer
      * Bugfix: More support of MySQL strict mode
      * Bugfix: Update bootstrap.js to version 3.3.7 to work with jQuery 3 in backend in TYPO3 8.6
      * Bugfix: Don't fill marker field in localized records
      * Bugfix: Missing TypoScript in mail standaloneview

 - :Version:
      3.12.0
   :Date:
      2017-02-19
   :Changes:

      * Feature _`#79036`: https://forge.typo3.org/issues/79036 Better marker generation
      * Bugfix _`#79708`: https://forge.typo3.org/issues/79708 Upload problem with more forms on the same page
      * Bugfix _`#79656`: https://forge.typo3.org/issues/79656 Don't show plugin information if wrong CType
      * Task: Implementation of jQuery 1.11.3 if activated via constants (instead of 1.11.0)
      * Task: Locallang fix for error message of type length validation
      * Task: Some documentation fixes

 - :Version:
      3.11.2
   :Date:
      2017-02-01
   :Changes:

      * Bugfix: https://github.com/einpraegsam/powermail/pull/27 Prevent fatal error in DateConverter UserFunc
      * Bugfix: https://github.com/einpraegsam/powermail/pull/33 Bug in add new form wizard in plugin
      * Bugfix _`#79587`: https://forge.typo3.org/issues/79587 AJAX: Push submit multiple times, more then one progress bar is shown
      * Task: Some documentation fixes

 - :Version:
      3.11.1
   :Date:
      2017-01-22
   :Changes:

      * Bugfix _`#79408`: https://forge.typo3.org/issues/79408 "FM old powermail_message_error" message on top of form

 - :Version:
      3.11.0
   :Date:
      2017-01-21
   :Changes:

      * Task Support new FlashMessages ViewHelper for TYPO3 8.6

 - :Version:
      3.10.1
   :Date:
      2017-01-12
   :Changes:

      * Bugfix _`#79217`: https://forge.typo3.org/issues/79217 MySQL strict mode errors

 - :Version:
      3.10.0
   :Date:
      2016-12-29
   :Changes:

      * Task Code cleanup - remove last HTML in PHP file for a TCA helper function to show notes below a form
      * Task Code cleanup - update userFunc comment
      * Task Documentation cleanup
      * Feature SendPost Finisher now with authentication feature

 - :Version:
      3.9.0
   :Date:
      2016-11-26
   :Changes:

      * Task Code update for further TYPO3 versions
      * Task Some documentation updates
      * Task Add some more behaviour tests
      * Task Make FlexForm values available in Spamshield classes
      * Feature Include JQuery not automatically if Pi2 is in use
      * Bugfix Field type Content Element: Output is not escaped in TYPO3 8 any more
      * Bugfix _`#78805`: https://forge.typo3.org/issues/78805 Field of type Content Element: Output is escaped in T3 8.x
      * Bugfix _`#78804`: https://forge.typo3.org/issues/78804 Powermail Location fields are not prefilled any more
      * Bugfix _`#78698`: https://forge.typo3.org/issues/78698 CSV-Export only exports full days
      * Bugfix _`#78690`: https://forge.typo3.org/issues/78690 Honeypod is not activated
      * Bugfix _`#78409`: https://forge.typo3.org/issues/78409 Adding a new field of type file is not possible
      * Bugfix _`#78214`: https://forge.typo3.org/issues/78214 Missing breaks in textarea output

 - :Version:
      3.8.0
   :Date:
      2016-10-22
   :Changes:

      * Feature Add another signal to manipulate variables
      * Feature _`#78092`: https://forge.typo3.org/issues/78092 Respect access for form selection in plugin for editors
      * Feature _`#78147`: https://forge.typo3.org/issues/78147 Support captcha in version 2 or higher
      * Bugfix _`#78146`: https://forge.typo3.org/issues/78146 Using EXT:captcha does not work correctly
      * Bugfix _`#78255`: https://forge.typo3.org/issues/78255 Respect access for form listing for editors in backend module
      * Bugfix _`#78356`: https://forge.typo3.org/issues/78356 Fatal error: Call to a member function setTSlogMessage() on null

 - :Version:
      3.7.0
   :Date:
      2016-09-25
   :Changes:

      * Feature Overview Backend Module: Use backend pagebrowser
      * Feature Overview Backend Module: Sort forms by title
      * Feature Add another signal to manipulate receivers name
      * Bugfix _`#78027`: https://forge.typo3.org/issues/78027 Don't list forms in overview backend module if a user hasn't access to the page

 - :Version:
      3.6.0
   :Date:
      2016-09-06
   :Changes:

      * Feature _`#77625`: https://forge.typo3.org/issues/77625 Add time period option to cleanUploadsFiles task
      * Feature _`#77846`: https://forge.typo3.org/issues/77846 f:be.widget.paginate instead of f:widget.paginate for backend module
      * Feature Add new-link for form preview in plugin, remove ugly add wizard
      * Feature Define now where to store new forms if editors add forms with Page TSConfig
      * Bugfix _`#77868`: https://forge.typo3.org/issues/77868 TYPO3 8.3: Prevent exception by adding new plugins
      * Task Small code refactoring for ext_tables.php and ext_localconf.php

 - :Version:
      3.5.0
   :Date:
      2016-08-24
   :Changes:

      * Task Updating composer.json
      * Task Refactoring of ShowFormNoteEditForm Function
      * Bugfix _`#77610`: https://forge.typo3.org/issues/77610 Rendering of plugin in backend in TYPO3 8.2

 - :Version:
      3.4.0
   :Date:
      2016-08-02
   :Changes:

      * Feature _`#77168`: https://forge.typo3.org/issues/77168 Add be_group to mail receiver
      * Bugfix _`#77154`: https://forge.typo3.org/issues/77154 Missing comma in ext_tables.sql
      * Bugfix _`#76820`: https://forge.typo3.org/issues/76820 RTE config is incorrect in FlexformPi1.xml

 - :Version:
      3.3.0
   :Date:
      2016-06-27
   :Changes:

      * Feature _`#76801`: https://forge.typo3.org/issues/76801 Make fields available in fluid using the marker value, NOT the internal storage id
      * Feature _`#76686`: https://forge.typo3.org/issues/76686 Make array properties changeable with signalslots with references and revert #76473
      * Bugfix _`#76765`: https://forge.typo3.org/issues/76765 Sender-Email could not be validated if not trimmed string
      * Bugfix _`#76703`: https://forge.typo3.org/issues/76703 dbEntry functionality together with update doesn't work
      * Bugfix _`#76681`: https://forge.typo3.org/issues/76681 Signal Slot sendTemplateEmailBeforeSend changed to prepareAndSend

 - :Version:
      3.2.0
   :Date:
      2016-06-13
   :Changes:

      * Feature _`#76587`: https://forge.typo3.org/issues/76587 Automaticly set reply-to email address
      * Bugfix _`#76589`: https://forge.typo3.org/issues/76589 Declaration of CaptchaDataAttributeViewHelper should be compatible ...
      * Bugfix _`#76586`: https://forge.typo3.org/issues/76586 Field classes are not consistent
      * Task added a composer.json file
      * Task add complete request array to spamshield log

 - :Version:
      3.1.1
   :Date:
      2016-06-06
   :Changes:

      * Bugfix _`#76473`: https://forge.typo3.org/issues/76443 PHP error
      * Bugfix _`#76443`: https://forge.typo3.org/issues/76443 Check if mail was deleted in optinConfirm action
      * Bugfix _`#76431`: https://forge.typo3.org/issues/76431 Upload validator in JavaScript is case sensitive

 - :Version:
      3.1.0
   :Date:
      2016-05-18
   :Changes:

      * Feature _`#76221`: https://forge.typo3.org/issues/76221 Add convinience methods to Mail model to avoid repeated iteration over answers
      * Feature _`#76114`: https://forge.typo3.org/issues/76114 Allow multiple pids for tx_powermail.flexForm.formSelection

 - :Version:
      3.0.2
   :Date:
      2016-04-28
   :Changes:

      * Bugfix _`#75938`: https://forge.typo3.org/issues/75938 Localization of flexform edit link to form ignores deleted flag
      * Bugfix _`#75918`: https://forge.typo3.org/issues/75918 JavaScript mandatory valdiation is broken for captcha in powermail 3

 - :Version:
      3.0.1
   :Date:
      2016-04-18
   :Changes:

      * Bugfix _`#75726`: https://forge.typo3.org/issues/75726 Version dependencies of 3.0.0 cannot be fulfilled
      * Bugfix _`#75611`: https://forge.typo3.org/issues/75611 Handle Swift exceptions

 - :Version:
      3.0.0
   :Date:
      2016-04-17
   :Changes:

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

 - :Version:
      2.25.2
   :Date:
      2016-04-11
   :Changes:

      * Bugfix _`#75356`: https://forge.typo3.org/issues/75356 Export module broken in TYPO3 6.2
      * Bugfix _`#75480`: https://forge.typo3.org/issues/75480 Error in Documentation / Manual

 - :Version:
      2.25.1
   :Date:
      2016-03-26
   :Changes:

      * Bugfix _`#75279`: https://forge.typo3.org/issues/75279 Hide message "Do not show this note again for this form"
      * Bugfix _`#75250`: https://forge.typo3.org/issues/75250 Powermail 2.25 with PHP 7
      * Bugfix _`#75227`: https://forge.typo3.org/issues/75227 Mail Listings: Reduce to one form combined with export does not refresh list
      * Bugfix _`#75222`: https://forge.typo3.org/issues/75222 Layout field is not shown on form open in backend
      * Bugfix _`#75221`: https://forge.typo3.org/issues/75221 Flexform redirect after submit - does not work anymore with PHP 7 (changed return value of substr)

 - :Version:
      2.25.0
   :Date:
      2016-03-12
   :Changes:

      * Feature _`#74538`: https://forge.typo3.org/issues/74538 Add new FlexForm fields through TSConfig configuration
      * Feature _`#74504`: https://forge.typo3.org/issues/74504 optinConfirm plaintext: opt in link does not work
      * Feature _`#73807`: https://forge.typo3.org/issues/73807 Detailview Choose Fields to show

 - :Version:
      2.24.0
   :Date:
      2016-02-29
   :Changes:

      * Feature _`#73725`: https://forge.typo3.org/issues/73725 Remove files in uploads/tx_powermail/ folder
      * Feature _`#73696`: https://forge.typo3.org/issues/73696 Add commandController to reset markers of a given form
      * Bugfix _`#73693`: https://forge.typo3.org/issues/73693 Markernames could be deleted on copying forms if editor hasn't the right to see markers
      * Bugfix _`#73676`: https://forge.typo3.org/issues/73693 powermail SelectFieldViewHelper throws an Exception in php 7.0.3
      * Bugfix _`#73651`: https://forge.typo3.org/issues/73651 Marker names don't get refactored if fieldset was copied

 - :Version:
      2.23.0
   :Date:
      2016-02-19
   :Changes:

      * Feature _`#73524`: https://forge.typo3.org/issues/73524 Required argument "mail" is not set ...
      * Feature _`#73228`: https://forge.typo3.org/issues/73228 Don't collapse Pages and Fields in IRRE
      * Bugfix _`#73537`: https://forge.typo3.org/issues/73537 SelectFieldViewHelper throws PHP Warning in frontend
      * Bugfix _`#73522`: https://forge.typo3.org/issues/73522 ParsleyValidator deprecated message
      * Bugfix _`#73469`: https://forge.typo3.org/issues/73469 Missing demo CSS for nolabel layout in check and radio fields

 - :Version:
      2.22.1
   :Date:
      2016-02-10
   :Changes:

      * Bugfix _`#73212`: https://forge.typo3.org/issues/73212 PluginInformation table is not shown als long as a receiver type is not chosen in FlexForm
      * Bugfix _`#73196`: https://forge.typo3.org/issues/73196 redirect with double sign in not working since version 2.17.1
      * Bugfix _`#73192`: https://forge.typo3.org/issues/73192 Length check of strings with linebreak

 - :Version:
      2.22.0
   :Date:
      2016-02-09
   :Changes:

      * Feature _`#73184`: https://forge.typo3.org/issues/73184 Predefined Receiverlists
      * Feature _`#72925`: https://forge.typo3.org/issues/72925 Include JavaScripts as minified versions
      * Feature _`#67349`: https://forge.typo3.org/issues/67349 Export fields in BE missing

 - :Version:
      2.21.0
   :Date:
      2016-01-16
   :Changes:

      * Feature _`#72722`: https://forge.typo3.org/issues/72722 turn _ifUniqueWhereClause into a typoscript content object
      * Bugfix _`#72656`: https://forge.typo3.org/issues/72656 set uploadfolder to true/create upload folder if it's missing

 - :Version:
      2.20.3
   :Date:
      2016-01-11
   :Changes:

      * Bugfix _`#72618`: https://forge.typo3.org/issues/72618 Plaintext Mails with two or more links

 - :Version:
      2.20.2
   :Date:
      2015-12-23
   :Changes:

      * Bugfix _`#72349`: https://forge.typo3.org/issues/72349 CSV export hidden field with field-uids contains fields without answer
      * Bugfix _`#72348`: https://forge.typo3.org/issues/72348 Leading spaces in CSV export
      * Bugfix _`#72297`: https://forge.typo3.org/issues/72297 Make tt_content.* values available in Templates
      * Bugfix _`#72282`: https://forge.typo3.org/issues/72282 Export module broken in Typo3 7.6.1

 - :Version:
      2.20.1
   :Date:
      2015-12-16
   :Changes:

      * Bugfix _`#71299`: https://forge.typo3.org/issues/71299 Captcha does not work

 - :Version:
      2.20.0
   :Date:
      2015-12-13
   :Changes:

      * Feature _`#72184`: https://forge.typo3.org/issues/72184 dbEntry feature with multiple records into same table
      * Bugfix _`#72138`: https://forge.typo3.org/issues/72138 Default Language for new forms is "all" instead of "default" in TYPO3 7.6

 - :Version:
      2.19.0
   :Date:
      2015-12-08
   :Changes:

      * Feature _`#69808`: https://forge.typo3.org/issues/69808 UID of tx_powermail_domain_model_mail for dbEntry
      * Task _`#72090`: https://forge.typo3.org/issues/72090 Move hardcoded implementation of local finisher classes to TypoScript
      * Bugfix _`#72061`: https://forge.typo3.org/issues/72061 Formular Konverter produces mysql Error / TYPO3 7.6 / Powermail 2.18.2

 - :Version:
      2.18.2
   :Date:
      2015-12-02
   :Changes:

      * Bugfix _`#71999`: https://forge.typo3.org/issues/71999 Reintroduce possibility to add mail-header Sender according to RFC2822 - 3.6.2
      * Bugfix _`#72021`: https://forge.typo3.org/issues/72021 Setting encryptionKey in OptinUtility::createHash - Links does not work because of wrong cHash

 - :Version:
      2.18.1
   :Date:
      2015-11-30
   :Changes:

      * Task _`#71976`: https://forge.typo3.org/issues/71976 Add PHP 5.5 to the minimum requirements of powermail
      * Bugfix _`#71987`: https://forge.typo3.org/issues/71971 tx_powermail.flexForm.formSelection seems to be broken in 2.18.0
      * Bugfix _`#71971`: https://forge.typo3.org/issues/71971 Fatal PHP Error in LocalizationUtility in PHP 5.3

 - :Version:
      2.18.0
   :Date:
      2015-11-27
   :Changes:

      * This version needs PHP 5.5
      * Feature _`#71830`: https://forge.typo3.org/issues/71830 Make addQueryString configurable via TypoScript Setup/Constants
      * Bugfix _`#71794`: https://forge.typo3.org/issues/71794 Edit form directly in flexform not work when it is closed

 - :Version:
      2.17.1
   :Date:
      2015-11-18
   :Changes:

      * Bugfix _`#71646`: https://forge.typo3.org/issues/71646 CaptchaValidator make a fatal error
      * Bugfix _`#71613`: https://forge.typo3.org/issues/71613 Template could not be found at "Module/ConverterFlexForm.xml"
      * Bugfix _`#71605`: https://forge.typo3.org/issues/71605 Location fields are not filled in TYPO3 7.6
      * Bugfix _`#71604`: https://forge.typo3.org/issues/71604 Resolve warnings in deprecation log of TYPO3 7.6

 - :Version:
      2.17.0
   :Date:
      2015-11-16
   :Changes:

      * Bugfix _`#71556`: https://forge.typo3.org/issues/71556 remove jQuery's removeProp() function from powermail
      * Task _`#71483`: https://forge.typo3.org/issues/71483 Resolve warnings in deprecation log of TYPO3 7.6
      * Feature _`#71373`: https://forge.typo3.org/issues/71373 Possibility to configure export template by export command controller

 - :Version:
      2.16.1
   :Date:
      2015-11-05
   :Changes:

      * Bugfix _`#71338`: https://forge.typo3.org/issues/71338 Fatal error: Can't use method return value in write context in ...ForeignValidator.php on line 62 with PHP < 5.5
      * Bugfix _`#71308`: https://forge.typo3.org/issues/71308 $(...).parsley(...).subscribe is not a function - Two forms on one page

 - :Version:
      2.16.0
   :Date:
      2015-11-03
   :Changes:

      * Feature _`#71254`: https://forge.typo3.org/issues/71254 Validators-Implementation simplification
      * Feature _`#71184`: https://forge.typo3.org/issues/71184 Hiding Form/Page-Title in FE
      * Bugfix _`#71098`: https://forge.typo3.org/issues/71098 New tab cannot be selected in TYPO3 7.4 or newer

 - :Version:
      2.15.2
   :Date:
      2015-10-27
   :Changes:

      * Bugfix _`#71072`: https://forge.typo3.org/issues/71072 Unknown column 'tx_powermail_domain_model_page.title' in 'field list'

 - :Version:
      2.15.1
   :Date:
      2015-10-27
   :Changes:

      * Bugfix _`#71018`: https://forge.typo3.org/issues/71018 Misspelled translation optin_mail_entries for en
      * Bugfix _`#71037`: https://forge.typo3.org/issues/71037 Unknown column 'tx_powermail_domain_model_field.sorting' in 'order clause'
      * Task _`#71063`: https://forge.typo3.org/issues/71063 Replace http:// calls with https://

 - :Version:
      2.15.0
   :Date:
      2015-10-23
   :Changes:

      * Feature _`#70891`: https://forge.typo3.org/issues/70891 Upload Fields: Clientside validation for file extensions
      * Feature _`#70836`: https://forge.typo3.org/issues/70836 tx_powermail_domain_model_answer: missing keys?
      * Feature _`#70756`: https://forge.typo3.org/issues/70756 Filesize validation via JavaScript

 - :Version:
      2.14.0
   :Date:
      2015-10-19
   :Changes:

      * Feature _`#70827`: https://forge.typo3.org/issues/70827 Write spam notifications to log file
      * Bugfix _`#70820`: https://forge.typo3.org/issues/70820 Fields without markers should be prevented
      * Bugfix _`#70768`: https://forge.typo3.org/issues/70768 Ajax submit: wrong redirection if multiple forms on one page
      * Bugfix _`#70096`: https://forge.typo3.org/issues/70096 Call to a member function getUid() on a non-object in powermail/Classes/Controller/AbstractController.php on line 236

 - :Version:
      2.13.0
   :Date:
      2015-10-14
   :Changes:

      * Feature _`#70655`: https://forge.typo3.org/issues/70655 Add finisher implementation to powermail
      * Bugfix _`#70604`: https://forge.typo3.org/issues/70656 powermail frontend and multilanguage
      * Bugfix _`#70657`: https://forge.typo3.org/issues/70657 Error in documentation (For editors / radio buttons)
      * Bugfix _`#70656`: https://forge.typo3.org/issues/70656 Error in documentation (For editors / checkboxes)

 - :Version:
      2.12.1
   :Date:
      2015-10-13
   :Changes:

      * Bugfix _`#70496`: https://forge.typo3.org/issues/70496 Make upload folder handling more robust
      * Bugfix _`#70491`: https://forge.typo3.org/issues/70491 plugin.tx_powermail.settings.setup.sender.email does not replace errorinemail@tryagain.com anymore [regression bug]
      * Bugfix _`#70446`: https://forge.typo3.org/issues/70446 Captcha-fields should be marked as required
      * Bugfix _`#70388`: https://forge.typo3.org/issues/70388 Maximum function nesting level reached
      * Bugfix _`#69976`: https://forge.typo3.org/issues/70388 Powermail crash after upgrading

 - :Version:
      2.12.0
   :Date:
      2015-10-05
   :Changes:

      * Task _`#66996`: https://forge.typo3.org/issues/66996 Remove variable $variablesMarkers from \In2code\Powermail\ViewHelpers\Misc\VariablesViewHelper::render()
      * Feature _`#70291`: https://forge.typo3.org/issues/70291 Do not use 'Powermail' as default mail name for "Sender"
      * Bugfix _`#70237`: https://forge.typo3.org/issues/70237 Wrong order for templateRootPaths
      * Bugfix _`#70215`: https://forge.typo3.org/issues/70215 Signal/Slots not working from 2.6.0 and above

 - :Version:
      2.11.2
   :Date:
      2015-09-22
   :Changes:

      * Bugfix _`#70075`: https://forge.typo3.org/issues/70075 saveSession Error
      * Bugfix _`#70048`: https://forge.typo3.org/issues/70048 Unique validator doesn't respect plugin settings
      * Bugfix _`#69998`: https://forge.typo3.org/issues/69998 Multiple Double Opt-In on the same page
      * Bugfix _`#69806`: https://forge.typo3.org/issues/69806 Remove head tag from HTML mail template

 - :Version:
      2.11.1
   :Date:
      2015-09-22
   :Changes:

      * Bugfix _`#70022`: https://forge.typo3.org/issues/70022 User gets ask about his location - even without location field in powermail 2.11.0

 - :Version:
      2.11.0
   :Date:
      2015-09-21
   :Changes:

      * Task _`#69992`: https://forge.typo3.org/issues/69992 Using of templateRootPaths (+ Partial + Layout) per default
      * Task _`#69873`: https://forge.typo3.org/issues/69873 Update parsley.js to 2.1.3
      * Feature _`#69870`: https://forge.typo3.org/issues/69870 Backend List: Select lines and delete, hide, unhide
      * Bugfix _`#69950`: https://forge.typo3.org/issues/69950 $ instead of jQuery in form.js
      * Bugfix _`#69911`: https://forge.typo3.org/issues/69911 $ Links in Plaintext Mails are broken

 - :Version:
      2.10.1
   :Date:
      2015-09-15
   :Changes:

      * Task _`#69582`: https://forge.typo3.org/issues/69582
      * Bugfix _`#69803`: https://forge.typo3.org/issues/69803
      * Bugfix _`#69783`: https://forge.typo3.org/issues/69783

 - :Version:
      2.10.0
   :Date:
      2015-09-05
   :Changes:

      * Feature #69494
      * Bugfix #69492, #69469

      See http://forge.typo3.org for Details

 - :Version:
      2.9.0
   :Date:
      2015-09-01
   :Changes:

      * Feature #69424, #69338
      * Bugfix #69425, #69366, #69323, #69146

      See http://forge.typo3.org for Details

 - :Version:
      2.8.0
   :Date:
      2015-08-25
   :Changes:

      * Feature #68999
      * Bugfix #69189

      See http://forge.typo3.org for Details

 - :Version:
      2.7.1
   :Date:
      2015-08-18
   :Changes:
      Fix: Changelog in manual could not be parsed from TER

 - :Version:
      2.7.0
   :Date:
      2015-08-18
   :Changes:
      Bugfix #69016, #69029, #69066, #69127

      See http://forge.typo3.org for Details

      Note: Changed Template/Partial-files:

      * New Partial: EXT:powermail/Resources/Private/Partials/Output/EditHidden.html
      * Changed Partial: EXT:powermail/Resources/Private/Partials/Form/Captcha.html
      * Changed Template: EXT:powermail/Resources/Private/Templates/Output/Edit.html

 - :Version:
      2.6.3
   :Date:
      2015-08-12
   :Changes:
      Bugfix #68977, #68842, #68716

      See http://forge.typo3.org for Details

 - :Version:
      2.6.2
   :Date:
      2015-08-06
   :Changes:
      Bugfix #68794 - Fix for TYPO3 7.4

      See http://forge.typo3.org for Details

 - :Version:
      2.6.1
   :Date:
      2015-08-04
   :Changes:
      Bugfix #68710

      See http://forge.typo3.org for Details

 - :Version:
      2.6.0
   :Date:
      2015-08-03
   :Changes:
      Bugfix #68696, #68647, #68587, #68583, #68576

      Feature #68695

      See http://forge.typo3.org for Details


      Note: Changes in Partial-files:

      * EXT:powermail/Resources/Private/Partials/Form/Check.html
      * EXT:powermail/Resources/Private/Partials/Form/Radio.html

 - :Version:
      2.5.2
   :Date:
      2015-07-24
   :Changes:
      Bugfix #68490, #68414, #68375

      See http://forge.typo3.org for Details

 - :Version:
      2.5.1
   :Date:
      2015-07-16
   :Changes:
      Bugfix #39218, #68044

      Task #68236, #68237

      See http://forge.typo3.org for Details

 - :Version:
      2.5.0
   :Date:
      2015-07-05
   :Changes:
      Bugfix #67872, #67660

      Feature #67392

      Task #67796

      See http://forge.typo3.org for Details

 - :Version:
      2.4.4
   :Date:
      2015-06-22
   :Changes:
      Bugfix #67448, #67548, #67555, #67623

      See http://forge.typo3.org for Details

 - :Version:
      2.4.3
   :Date:
      2015-06-07
   :Changes:
      Bugfix #67157, #67167, #67194, #67255

      See http://forge.typo3.org for Details

 - :Version:
      2.4.2
   :Date:
      2015-05-24
   :Changes:
      Bugfix #67112, #67108, #67102, #67039, #67035

      See http://forge.typo3.org for Details

 - :Version:
      2.4.1
   :Date:
      2015-05-18
   :Changes:
      Bugfix #67003

      See http://forge.typo3.org for Details

 - :Version:
      2.4.0
   :Date:
      2015-05-17
   :Changes:
      Bugfix #66914, #65366

      Features #65716, #65226

      See http://forge.typo3.org for Details

 - :Version:
      2.3.3
   :Date:
      2015-05-08
   :Changes:
      Bugfix #66634, #66732

      See http://forge.typo3.org for Details

 - :Version:
      2.3.2
   :Date:
      2015-04-27
   :Changes:
      Bugfix #66571, #66562, #65481

      See http://forge.typo3.org for Details

 - :Version:
      2.3.1
   :Date:
      2015-04-19
   :Changes:
      Bugfix #66462, #66469, #66470, #66471

      See http://forge.typo3.org for Details

 - :Version:
      2.3.0
   :Date:
      2015-04-17
   :Changes:
      Bugfix #65716, #65635, #65942, #66026

      Feature #66359

      Task #65993

      See http://forge.typo3.org for Details

 - :Version:
      2.2.0
   :Date:
      2015-02-27
   :Changes:
      Updated powermail for TYPO3 6.2, 7.0 and 7.1

 - :Version:
      2.1.17
   :Date:
      2015-02-27
   :Changes:
      Bugfix #65222, #65258, #65263

      See http://forge.typo3.org for Details

 - :Version:
      2.1.16
   :Date:
      2015-02-21
   :Changes:
      Bugfix #65201, #65174, #65173, #64992

      See http://forge.typo3.org for Details

 - :Version:
      2.1.15
   :Date:
      2015-02-09
   :Changes:
      Bugfix #64779, #64937

      See http://forge.typo3.org for Details

 - :Version:
      2.1.14
   :Date:
      2015-02-01
   :Changes:
      Bugfix #64685, #64625, #64564, #64545, #64426, #64424, #64412

      Feature #64594, #64533

      See http://forge.typo3.org for Details

 - :Version:
      2.1.13
   :Date:
      2015-01-19
   :Changes:
      Bugfix #64236, #64250, #64352

      Feature #64279

      See http://forge.typo3.org for Details

 - :Version:
      2.1.12
   :Date:
      2015-01-11
   :Changes:
      Bugfix #64220, #64111

      Feature #64212, #64195

      See http://forge.typo3.org for Details

 - :Version:
      2.1.11
   :Date:
      2014-12-23
   :Changes:
      Bugfix update #63972, #63933, #63797, #63796, #63766, #43502

      Task #64019 Remove Google Image Charts

      See http://forge.typo3.org for Details

 - :Version:
      2.1.10
   :Date:
      2014-12-11
   :Changes:
      Bugfix update #63765, #63724

      Feature #63707, #63404, #63397, #63365, #63149

      See http://forge.typo3.org for Details

 - :Version:
      2.1.9
   :Date:
      2014-11-26
   :Changes:
      Bugfix update #63329, #63152, #63118, #63046, #63028, #63020, #62920

      Feature #63322, #63317, #63302, #63297

      See http://forge.typo3.org for Details

 - :Version:
      2.1.8
   :Date:
      2014-11-13
   :Changes:
      Bugfix update #62761, #62146, #62919

      Feature #62728, #62653

      See http://forge.typo3.org for Details

 - :Version:
      2.1.7
   :Date:
      2014-10-31
   :Changes:
      Bugfix update #62583

      Task #62584

      See http://forge.typo3.org for Details

 - :Version:
      2.1.6
   :Date:
      2014-10-30
   :Changes:
      Bugfix updates #54306, #62485, #62531

      Feature updates #62262, #62433, #62434, #62469, #62535

      See http://forge.typo3.org for Details

 - :Version:
      2.1.5
   :Date:
      2014-10-18
   :Changes:
      Bugfix updates #62058, #62097, #62135

      Feature updates #62262, #60504 (Templa Voila with Form Converter)

      See http://forge.typo3.org for Details

 - :Version:
      2.1.4
   :Date:
      2014-10-06
   :Changes:
      Bugfix update #62048

      See http://forge.typo3.org for Details

 - :Version:
      2.1.3
   :Date:
      2014-10-05
   :Changes:
      Bugfix updates #61987, #61803, #61681, #61118, #61956

      See http://forge.typo3.org for Details

 - :Version:
      2.1.2
   :Date:
      2014-09-17
   :Changes:
      Bugfix updates #61657, #61658

      See http://forge.typo3.org for Details

 - :Version:
      2.1.1
   :Date:
      2014-09-15
   :Changes:
      Bugfix updates #61530, #61533, #61536, #61537, #61551

      Feature updates #61532, #61553, #61583

      See http://forge.typo3.org for Details

 - :Version:
      2.1.0
   :Date:
      2014-09-10
   :Changes:
      Refactored Powermail 2 extension with a lot of usability improvements, a powermail 1.x formconverter and a lot of new cool stuff. See forge.typo3.org for details. We're looking forward for your feedback. Thank to the universities, who sponsored this version!

      See http://forge.typo3.org for Details

 - :Version:
      2.0.18
   :Date:
      2014-09-17
   :Changes:
      Small Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.17
   :Date:
      2014-09-05
   :Changes:
      Small Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.16
   :Date:
      2014-06-26
   :Changes:
      Bugfix updates #59902, #54648, #53786

      Feature updates #59395

      See http://forge.typo3.org for Details

 - :Version:
      2.0.15
   :Date:
      2014-05-26
   :Changes:
      Bugfix updates #58389, #58114, #57963, #56273, #56117

      See http://forge.typo3.org for Details

 - :Version:
      2.0.14
   :Date:
      2014-05-22
   :Changes:
      Security fix. Please update!

      See TYPO3-EXT-SA-2014-007

 - :Version:
      2.0.13
   :Date:
      2014-04-12
   :Changes:
      Bugfix updates #57858, #56198, #54896

      Feature updates #56049

      See http://forge.typo3.org for Details

 - :Version:
      2.0.12
   :Date:
      2014-04-11
   :Changes:
      TYPO3 6.2 update

      Bugfix updates #57804, #57480, ##57337, #56273

      See http://forge.typo3.org for Details

 - :Version:
      2.0.11
   :Date:
      2014-04-10
   :Changes:
      Security fix. Please update!

      See TYPO3-EXT-SA-2014-006

 - :Version:
      2.0.10
   :Date:
      2013-10-11
   :Changes:
      Bugfix update (small)

      - 5 Features

      - 9 Bugfixes

      **Breaking changes** in

      - Resources/Private/Partials/PowermailAll/Mail.html

      - Breaking Change in Resources/Private/Partials/PowermailAll/Web.html

      - Breaking Change in Resources/Private/Templates/Forms/PowermailAll.html

      See http://forge.typo3.org for Details

 - :Version:
      2.0.9
   :Date:
      2013-07-06
   :Changes:
      1 Task

      3 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.8
   :Date:
      2013-07-04
   :Changes:
      15 New Features

      7 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.7
   :Date:
      2013-06-03
   :Changes:
      Security fix. Please update!

      See TYPO3-EXT-SA-2013-006

 - :Version:
      2.0.6
   :Date:
      2013-04-01
   :Changes:
      1 New Feature

      5 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.5
   :Date:
      2012-12-08
   :Changes:
      4 New Feature

      11 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.4
   :Date:
      2012-10-29
   :Changes:
      2 New Feature

      4 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.3
   :Date:
      2012-10-17
   :Changes:
      7 New Feature

      10 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.2
   :Date:
      2012-08-19
   :Changes:
      5 New Feature

      17 Bugfix updates

      See http://forge.typo3.org for Details

 - :Version:
      2.0.1
   :Date:
      2012-08-08
   :Changes:
      Different Security Fixes

 - :Version:
      2.0.0
   :Date:
      2012-05-21
   :Changes:
      Initial upload

      - Complete Redesign of the old powermail 1.x
      - Redesign with Extbase and Fluid
      - Focus on Marketing and Spam-Prevention
      - Keep Flexibility
