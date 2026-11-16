# Upgrade Instructions and breaking changes

## Upcoming release

### Database: TEXT columns are nullable again

The TEXT columns of fields, mails and answers (e.g. `tx_powermail_domain_model_field.placeholder_repeat`) are
defined as nullable `text` without a default value again - like the TYPO3 core does for TCA `type=text`.
`tx_powermail_domain_model_mail.sender_ip` changes from `tinytext` to `varchar(255)`.

The previous definition `text DEFAULT '' NOT NULL` could not be applied by the database compare as long as
existing rows contained `NULL` ("Data truncated" / "Invalid use of NULL value"). With the new definition the
database compare only relaxes the columns and runs without errors.

The optional upgrade wizard "Powermail: Update all database TEXT fields that have a null value with a default
value" replaces remaining `NULL` values with an empty string. It is only offered if there are such values.

### Request tokens (CSRF protection) - opt-in, no breaking change

A new extension configuration `enableRequestToken` (default: off) protects the form submit with the
CSRF-like request tokens of the TYPO3 core. As long as it is turned off, nothing changes.

The shipped templates `Form/Form.html` and `Form/Confirmation.html` now pass
`requestToken="{requestToken}"` to `<f:form>`. The variable is empty while the feature is turned off,
so the markup does not change. If you turn the feature on and use your own copies of these templates,
add the attribute there as well - see ForAdministrators/Installation.

## Version 13.1.1

### ViewHelpers in values that powermail parses with Fluid

Powermail replaces variables like `{firstname}` in a couple of configured values by parsing them with
Fluid: the mail subject, the receiver name and email, the sender name and email, the reply-to
addresses, field titles and the options of select, radio and checkbox fields.

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


## Version 13.0.0

### Breaking Changes

#### Plugins pi2 - pi4

These plugins were removed from EXT:powermail. They are available as a premium extension. If you need them, please
contact service@in2code.de for a quote.

#### Debug functionality removed

A bunch of debug options could be set.

- debugMail
- debugSettings
- debugSaveToTable
- debugSpamshield
- debugVariables

These were removed without any replacement. No migration is available. You should use php standard methods like
xdebug to debug the code.

For `debugSpamshield` there is still the possibility to send a notification email to an admin.

## Version 12.4.0

### Breaking Change

We removed the export and rss functionality completely without any replacement, because there is no
reliable security concept behind it and is not easy to fix.

If you need this, please contact [in2code](https://www.in2code.de/en/contact/) for paid assistance or implement it yourself.

## Version 12.0.0

### Upgrade - Wizards

Unfortunately the bugfix for https://github.com/in2code-pro/powermail/issues/56 introduced a breaking
change. There are now five submodules, instead of a single big one.

That means, permissions for backend usergroups must be changed in order to use the new modules.

The new version provides an upgrade wizard to migrate the old permission to the new submodules. Please visit
the upgrade wizard in the backend or run it via cli.

### Events

Many events can now modify the transferred mail object.

If you use events, please check the following ones for changed signatures

* FormControllerCreateActionAfterMailDbSavedEvent got the additional argument hash
* FormControllerOptinConfirmActionBeforeRenderViewEvent uses the mail object instead of the mail uid
* all setters in events do not return the event object (as stated in the official documenation)

## Version 11.1

In Version 11.1 the default behaviour for password fields is hashing the value with the default hashing algorithm before storing it in the database.
If you want to restore the old behaviour you have to apply the changes described [here](/ForAdministrators/BestPractice/PasswordField.md).

## Version 10.0

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
