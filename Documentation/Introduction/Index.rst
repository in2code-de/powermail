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
  TypoScript cObjects and userFuncs, debugoutput, etc...).

Cut a long story short: With powermail editors are able to create complex
mailforms without knowledge of html, php or javascript and that's the main difference
between powermail and the most other form extensions


.. _whats-new:

What's new in powermail 2.1?
----------------------------

Powermail 2.1 uses the same data-structure that was created with powermail 2.0 (see below).
But there was another code-refactoring especially for TYPO3 6.2 LTS and upcoming versions.

University Package
^^^^^^^^^^^^^^^^^^

Powermail 2.1 was mainly supported from a consortium of german universities and colleges.
We want to thank them for their trust in powermail and the further development. See **UP** for features which are part of the University Package.

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

What's new in powermail 2.0?
----------------------------

Powermail >= 2.0 is a complete rebuild of the old powermail. Main
focus was to keep flexibility and all the features and to create even
more. The rebuild was done with Extbase and Fluid.

See videos to 2.0 on youtube

- Powermail 2.0 Introduction: `http://youtu.be/tuhMiwEvhIs <http://youtu.be/tuhMiwEvhIs>`_

- Powermail 2.0 Hidden Secrets: `http://youtu.be/XAkenuTmxZ0 <http://youtu.be/XAkenuTmxZ0>`_

- Powermail integrated  **the most interesting extensions** into the core:

  - powermail\_frontend: Show stored Mails again in the frontend (build a fast guestbook, etc...)
  - wt\_spamshield: Integrated spam-prevention-methods from wt\_spamshield
  - wt\_calculating\_captcha: Integrated a captcha extension to powermail
  - powermail\_optin: Double-Opt-In for powermail
  - powermail\_sendpost: Send values to a third-party-software like a CRM (salesforce, etc...)

- Forms can be used  **more than only one** time now

- **Localization improved** (no more different Field markers)

- **Database model changed** – tt\_content will not longer extended with
  powermail fields

- **Marketing Session** – See the most important information about your
  user now

- **Adwords Implementation** – Adwords Conversion Tracking could be
  enabled by adding the code to the constants

- **CC, BCC, Reply, ReturnPath, Priority** now available

- **Send values to a third-party-software** like a CRM (like salesforce,
  etc...) or a Marketing-Automation-Tool (like eloqua, etc...)

- **Spam Factor** for Mails

- **Spam Prevention Methods** – same methods from wt\_spamshield

- **Calculating Captcha** included

- **Change Design of the backend module**

- **Double-Opt-In** for forms

- **powermail\_frontend** to show mails in frontend (Pi2) with export
  possibilities (XLS, CSV, RSS)

- **Plugin Info** in Web view of backend

- **Backend Module Reports** (Fields and Marketing)

- **Backend Module Check**

- **E-Mails to FE Groups**

- **Form Caching**


.. _compatibility:

Compatible TYPO3 versions
-------------------------

.. t3-field-list-table::
 :header-rows: 1

 - :Tab:
      Powermail Versions
   :Field:
      TYPO3 Versions

 - :Tab:
      2.1.x
   :Field:
      6.2 LTS and newer

 - :Tab:
      2.0.x
   :Field:
      4.6, 4.7, 6.1, 6.2 LTS

.. _screenshots:

Screenshots
-----------


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

|pi2|

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
