.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _generateExportMail:

Generate a mail with an export file as attachment
-------------------------------------------------

Introduction
^^^^^^^^^^^^

You can generate mails with a link to an exportfile or simply attach an export file to this mail.
With this task you can (e.g.) send yourself 1 time a day a mail with all mails from the last 24h.

Configuration
^^^^^^^^^^^^^

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
      Propertyname
   :Description:
      Description
   :Default:
      Default value

 - :Property:
      receiverEmails
   :Description:
      Add one or more (comma-separated) recevier email addresses for mail generation
   :Default:
      [empty]

 - :Property:
      senderEmail
   :Description:
      Add a sender email address
   :Default:
      sender@domain.org

 - :Property:
      subject
   :Description:
      Add a subject for the mail
   :Default:
      New mail export

 - :Property:
      pageUid
   :Description:
      You have to enter the page uid where the mails are stored that you want to export
   :Default:
      0

 - :Property:
      domain
   :Description:
      Enter a domainname with a trailingslash for linkgeneration in mail
   :Default:
      http:\/\/domain.org

 - :Property:
      period
   :Description:
      You can define a timeperiod (in seconds) from now to the past.
      If you enter 86400 if you want to get the mails from the last 24 hours
   :Default:
      2592000

 - :Property:
      attachment
   :Description:
      If you want to get the export file as attachment to the mail
   :Default:
      1

 - :Property:
      fieldList
   :Description:
      Define the sorting of the fields that should be in the export file.
      If this field is empty, all **default fields** are exported.
      A commaseparated list with field uids configures the export file. In addition you can use values like
      crdate, sender_name, sender_mail, receiver_mail, subject, marketing_referer_domain,
      marketing_referer, marketing_frontend_language, marketing_browser_language, marketing_country
      marketing_mobile_device, marketing_page_funnel, user_agent, time, sender_ip, uid, feuser
   :Default:
      [empty]

 - :Property:
      format
   :Description:
      Define the export format. "xls" or "csv" is supported.
   :Default:
      xls

 - :Property:
      storageFolder
   :Description:
      Define where the export files should be stored.
   :Default:
      typo3temp/tx_powermail/

 - :Property:
      fileName
   :Description:
      You can define a fix filename for your export file without fileextension.
      If you let this field empty, a randomized filename will be used.

      **Privacy note:** Take care, that your export file is not available for all website-users,
      especially if there are deserving protection datas in your export-files.
   :Default:
      [empty]

 - :Property:
      emailTemplate
   :Description:
      Path and filename to the email template
   :Default:
      EXT:powermail/Resources/Private/Templates/Module/ExportTaskMail.html

Image example
^^^^^^^^^^^^^

|scheduler_export1|

Console example
^^^^^^^^^^^^^^^

You can call a scheduler task directly from the console (if the backend user _cli_lowlevel exists) -
see this example (called from webroot):

.. code-block:: text

	typo3/cli_dispatch.phpsh extbase task:export --receiver-emails="receiver1@domain.org" --page-uid=140 --period=86400

Note
^^^^

If you need your own HTML-Template for XLS- or CSV-generating, you can define the templateRootPath in
your **root TypoScript**

.. code-block:: text

	module.tx_powermail.view.templateRootPaths.1 = fileadmin/yourPath/Templates/

After that, you can copy the ExportXls.html and/or ExportCsv.html to fileadmin/yourPath/Templates/Module/ExportXls.html
and modify it.
