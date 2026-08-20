# Upgrade Instructions and breaking changes

## Version 10.9.3

### ViewHelpers in values that powermail parses with Fluid

Powermail replaces variables like `{firstname}` in a couple of configured values by parsing them with
Fluid: the mail subject, the receiver name and email, the sender name and email, field titles and the
options of select, radio and checkbox fields.

Two things changed there for security reasons:

1. **Only allowlisted ViewHelpers are executed in these values.** The allowlist is the extension
   configuration `allowedViewHelpersInParsedStrings` and contains `f:cObject` by default, because
   using it in the receiver name, the receiver mail and the subject is a documented feature. Every
   other ViewHelper is removed from the output and the removal is written to the TYPO3 log.
   If your installation uses further ViewHelpers in one of these values, add them to the setting -
   for example `f:cObject,f:if,f:translate` or, less restrictive, `f:cObject,f:format.*`.
   Variables like `{firstname}` keep working in any case and need no configuration.
2. **Values that a website visitor submitted are no longer parsed at all.** The sender name and
   address of a mail to the receiver, and the receiver name and address of a mail to the sender, come
   from the submitted form. Fluid in such a value is no longer evaluated, it stays as it was
   submitted. As a side effect the value that is stored in `tx_powermail_domain_model_mail` is now the
   submitted one, and no longer the result of a Fluid rendering.

There is one case that stops working without a replacement: a ViewHelper call inside a TypoScript
`overwrite.*` value of a key that holds submitted data, e.g.
`plugin.tx_powermail.settings.setup.receiver.overwrite.senderName` with a `{f:cObject(...)}` in it.
Use the ViewHelper in a key that is not fed from the submitted data, or resolve the value in
TypoScript instead.

Templates and RTE fields are not affected. Arbitrary ViewHelpers and own namespaces keep working
there, see `Documentation/ForAdministrators/BestPractice/Templates.md`.


## Version 10.9.0

### Breaking Change

We removed the export and rss functionality completely without any replacement, because there is no
reliable security concept behind it and is not easy to fix.

If you need this, please contact [in2code](https://www.in2code.de/en/contact/) for paid assistance or implement it yourself.


## Version 10.7.4

If you want to contribute, the URLs for the development instance changed slightly. The TYPO3 version was added
to the url.

Please use https://powermail-v11.ddev.site or https://local.powermail-v11.de as URLs for the local dev environment.

This change eases the parallel execution of local dev environments for the various versions.

## Version 10.0.0

In version 10 we completely removed jQuery, jQuery UI, Datetimepicker, Parsley.js and other old JS stuff from frontend
rendering. We now use an own form framework, that runs with vanilla JS and can be included via async or defer and does
not need any old jQuery version.
To make the switch as smooth as possible for you, the validation output is nearly the same as with parsley.js.
As a new feature we now validate while the input is done from the user.

Nevertheless, some HTML templates have changed:
* Morestep validation is build in the HTML template:
  * EXT:powermail/Resources/Private/Partials/Form/Page.html
* ViewHelper name changed from {vh:validation.enableParsleyAndAjax(form:form)} to {vh:validation.enableJavascriptValidationAndAjax(form:form)}:
  * EXT:powermail/Resources/Private/Templates/Form/Form.html
  * EXT:powermail/Resources/Private/Templates/Output/Edit.html
  * EXT:powermail/Resources/Private/Templates/Form/Confirmation.html
* If you have added jQuery manually, you can remove the implementation (if it was only for powermail)

## Version 9.0

| Version                                         | Description                                                                                                                                                     |
|-------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Resources/Private/Partials/Form/Field/Html.html | Uses now <f:sanitize> instead of <f:format.raw>. This means, that forms which uses the html element, will now clean the HTML for incorrect / possibly bad code. |
