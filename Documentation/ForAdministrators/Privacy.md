# Privacy (GDPR / DSGVO)

## Introduction

This page provides you some useful tips how to handle privacy settings for your visitors with powermail. Please note
that this page is just a help but not a legal binding declaration.

## Cookies

Powermail does not store a cookie per default on the visitors browser since version 6.0.
Nevertheless there are two reasons why powermail is forced to store a cookie because of its settings:

* Marketing Static Template is added: If you are collecting information about page funnel or other useful information about your visitor, powermail needs to store a TYPO3 frontend cookie.
* Session check - spam prevention: If you turn on a session check via TypoScript, a TYPO3 frontend cookie will be used.

**Note:** Session check is turned off by default with powermail. In older versions the session check is turned on by
default and must be disabled via configuration:

`plugin.tx_powermail.settings.setup.spamshield.methods.4._enable = 0`

or disable all spamshield methods:

`plugin.tx_powermail.settings.setup.spamshield._enable = 0`

## IP-Address

Powermail does not store the IP-address of the visitor by default since version 6.0. If you want to store this information
(to provide some anti-spam or anti-hack-methods) you have to turn this feature on in the extension manager settings.

**Note:** If you update your TYPO3, the configuration to save the IP-address may be already stored and must be turned off.

**Note:** If you enable "Marketing" information, **the IP Adress is collected and is send via http to ip-api.com**. Please refer https://ip-api.com/docs/legal for further information.

The service IP-API can be disabled via:
`plugin.tx_powermail.settings.settings.setup.marketing.determineCountry = 0` (default setting is off)

## Add a link in a checkbox label

Now, with the GDPR change, we get a lot of questions how to add a link like "privacy terms accepted" in a checkbox
label.

Just use an option in your FlexForm like (with an example link to page 123 where the privacy terms are located):

`I accept the <f:link.page pageUid="123">privacy terms</f:link.page> | privacy terms accepted`

After that you have to enable html in labels (this feature is turned off for security reasons). Example TypoScript
constants:

`plugin.tx_powermail.settings.misc.htmlForLabels = 1`

## Disable storing of mails in database

If you don't need to store mails in the database, you can simply turn it off with a bit of TypoScript:

`plugin.tx_powermail.settings.db.enable = 0`

## Deleting of old mails

See the Scheduler section in the manual to see how you can remove old mails from the database.

## Notes

Please consider to not ask your website visitors for more information then you need for the request to meet
the requirements of the GDPR.

The GDPR also requires that you only store data with personal information as long as is necessary for the
purpose for which it was obtained. This only applies to records containing personal data (which is already
the case if you obtained email address or name).
