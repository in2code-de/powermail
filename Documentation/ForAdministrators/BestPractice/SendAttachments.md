# How to send attachments

## Defined by TypoScript

In versions < 12.5 it was only possible to define, whether attachments are sent or not, via TypoScript. This setting
was used for all forms and plugins (unless you used conditions or set the value in the database).

```typo3_typoscript
plugin.tx_powermail.settings.setup {
    reciever {
        attachment = {$plugin.tx_powermail.settings.receiver.attachment}
    }
    sender {
        attachment = {$plugin.tx_powermail.settings.sender.attachment}
    }
}
```

## Defined by editors

Since version 12.5 it is possible, that editors define, whether attachments should be sent or not. This feature is
"hidden" behind a feature toggle and restrictive page tsconfig settings.

### Steps for activation

1) Activate the feature toggle in site settings

```php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['powermailEditorsAreAllowedToSendAttachments'] = true;
```

2) Activate the fields in PageTSconfig

Due to security reasons these fields are hidden by default, so that the admin must take an additional step. Editors
should not get more permissions, than necessary by a feature release. ;-)

```typo3_typoscript
TCEFORM {
  tt_content {
    pi_flexform {
      powermail_pi1 {
        sender {
          settings\.flexform\.sender\.attachment {
            disabled = 0
          }
        }
        receiver {
          settings\.flexform\.receiver\.attachment {
            disabled = 0
          }
        }
      }
    }
  }
}
```

