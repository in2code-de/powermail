.. include:: ../Includes.txt
.. include:: Images.txt

.. _introduction:

Introduction
============

.. only:: html

	:ref:`what` | :ref:`whats-new` | :ref:`compatibility` | :ref:`screenshots` |

.. _what:

What does it do?
----------------

Powermail is a powerful and – in addition – a very easy mailform
extension with a wide range of tools and features for editors, admins
and developers. Define your form in the backend with a few clicks and
look at the final output in the frontend.

Some basic points:

- Main features of this mailform extension is to store the mails into
  the database. Export it from the backend module (xls, csv) or list the
  values in the frontend again (Pi2).

- Powermail send one or more mails to a static receiver or to dynamic receivers or
  to a whole Frontent-User Group.

- Different HTML-Templates (Fluid) and RTE fields in backend for all
  needed views.

- Input Validation in different ways (HTML5, JavaScript and PHP).

- A main focus of the form is to prevent spam (Captcha, Spam Factor,
  Different Checks, etc...).

- Another focus is to track some interesting information of users
  (funnel, browser, language, country, etc...)

- For Developers: Powermail is a very flexible extension, which also
  could be extended by your code or extension (hooks, signalslots,
  own Finishers, own DataProcessors, own Spam-Prevention-Methods,
  own Validators, TypoScript cObjects and userFuncs, debugoutput, etc...).

Cut a long story short: With powermail editors are able to create complex
mailforms without knowledge of html, php or javascript and that's the main difference
between powermail and the most other form extensions


.. _whats-new:

What's new in powermail 5?
--------------------------

- A large refactoring for TYPO3 9.x
- A large refactoring for PHP 7.0 - 7.2

What's new in powermail 4?
--------------------------

- A small refactoring of the mail related service classes
- Add a possibility to create columns in a form for e.g. bootstrap
- TYPO3 7.6 is still supported in this major version

What's new in powermail 3?
--------------------------

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

University Package
^^^^^^^^^^^^^^^^^^

Powermail 2.1 was mainly supported from a consortium of german universities and colleges.
We want to thank them for their trust in powermail and the further development.
See **UP** for features which are part of the University Package.

- h-da Hochschule Darmstadt – University of Applied Sciences
- Leibniz Universität Hannover
- Technische Universität München
- Hochschule für Wirtschaft und Umwelt Nürtingen-Geislingen
- Hochschule Osnabrück – University of Applied Sciences
- Universität Rostock
- Universität Ulm (ULM University)
- Bauhaus-Universität Weimar
- Bergische Universität Wuppertal


Facts
^^^^^

- New Features

  - Validation

    - HTML5 Validation in combination of JavaScript and PHP Validation (**UP**)
    - New JavaScript Validation Framework ParsleyJs was included (**UP**)
    - Combine different validators are just disable some
    - Validation support Multistep Form and AJAX submit now (**UP**)
    - Add own serverside and clientside validators

  - AJAX Form Submit possible
  - New Field: Countryselection (with or without static_info_tables) (**UP**)
  - Extended Field Select: Switch between single- or multiselect (**UP**)
  - Extended Field Upload: Switch between single- or multiupload (HTML5 only) (**UP**)
  - HTML5 Datepicker (Datetime, Date, Time) with JavaScript Fallback
  - Fill Options of Fields (Select, Checkboxes and Radiobuttons) out of TypoScript (static or dynamic)
  - New Field Property Placeholder
  - New Field Property Description
  - Decrease number of forms to selection in plugin for editors with Page TSConfig
  - Scheduler Task with CommandController allows admins to remove unused uploaded files (**UP**)

- Usability
  - A note is displayed if an editor opens a form and there is no sender_name or sender_email defined
  - Another note within the Plugin helps editors to find and edit the current form
  - A new View in Backend Module lists all Powermail Forms and where they are used. This helps to manage the forms.

- Misc
  - A form converter (for forms from 1.x to 2.x) was added in a new view in the Backend Module (**UP**)
  - A powermail_cond update (see manual of powermail_cond) was also created for powermail 2.1 (**UP**)

- Code-Refactoring

  - Namespaces will be used now
  - Removing of unused Methods and ViewHelpers
  - Clean Up Field Rendering (better select, markers are used instead of uids, property replaces name attribute)
  - Sendpost Function rewritten with cObject Parsing
  - Rewritten SaveToAnyTable Function for even more flexibility
  - Move Marketing Tracking from USER_INT to AJAX (to enable e.g. static filecache)
  - Converted all language files from xml to xliff
  - Manual was converted from sxw to rst
  - Debug Outputs changed to devlog methods
  - PHPsniffer used for Refactoring

- Quality
  - Added Behavior-Tests for many Frontend-Functions (With Behat with Mink and Selenium)
  - Unittests added for different methods (e.g. all serverside validators)
  - A Testparcour was generated to test the main features of powermail (**UP**)
  - We focussed on TYPO3 6.2 LTS to increase quality

**UP** = Part of the University Package (see sponsors note)

.. _compatibility:

Compatible TYPO3 and PHP versions
---------------------------------

Short story: We will **support powermail 2 as long as TYPO3 6.2LTS will be supported** with bugfixes and security updates.

But **new features and further development will be only included into powermail 3**.

Small note: 2.18 is the last powermail version which support PHP 5.3

.. t3-field-list-table::
 :header-rows: 1

 - :PowermailVersion:
      Powermail Version
   :TYPO3Version:
      TYPO3 Versions
   :PHPVersion:
      PHP Version
   :Support:
      Support

 - :PowermailVersion:
      3.x
   :TYPO3Version:
      7.6 LTS - 8.x
   :PHPVersion:
      5.5 - 7.x
   :Support:
      This version will be provided with

      - New Features
      - Bugfixes
      - Security Updates

 - :PowermailVersion:
      2.25.x
   :TYPO3Version:
      6.2 LTS - 7.6 LTS
   :PHPVersion:
      5.5 - 7.x
   :Support:
      This version will be still provided with

      - Bugfixes
      - Security Updates

 - :PowermailVersion:
      2.19 - 2.24
   :TYPO3Version:
      6.2 LTS - 7.6 LTS
   :PHPVersion:
      5.5 - 7.x
   :Support:
      This version will no longer be supported in any way

 - :PowermailVersion:
      2.1 - 2.18
   :TYPO3Version:
      6.2 LTS - 7.6 LTS
   :PHPVersion:
      5.3 - 5.5
   :Support:
      This version will no longer be supported in any way

 - :PowermailVersion:
      2.0.x
   :TYPO3Version:
      4.6, 4.7, 6.1, 6.2 LTS
   :PHPVersion:
      5.0 - 5.3
   :Support:
      This version will no longer be supported in any way

 - :PowermailVersion:
      1.x
   :TYPO3Version:
      4.4, 4.5, 4.6, 4.7
   :PHPVersion:
      < 5.3
   :Support:
      This version will no longer be supported in any way

.. _screenshots:

Example Screenshots
-------------------


Frontend: Show a form with different field types
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

|frontend1|

Example Form with Input, Textarea, Select, Multiselect, Checkboxes, Radiobuttons, and Submit


Frontend: Multistep Form
^^^^^^^^^^^^^^^^^^^^^^^^

|frontend2|

Example Multistep Form with clientside validation


Frontend: powermail_frontend integration shows mails in frontend
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

|frontend_pi2|

Listing of stored mails with the possibility to see a Detail view or to re-edit the entries for a defined Frontend Usergroup
Define your ABC- and Searchterm Filter
Define the export possibilities (RSS, CSV, XLS)


Backend: Mail Listing
^^^^^^^^^^^^^^^^^^^^^

|backend1|

Manage the delivered mails with a fulltext search and some export possibilities


Backend: Reporting
^^^^^^^^^^^^^^^^^^

|backend2|

See the reporting about the delivered mails (Form or Marketing Data Analyses are possible)
