# Password Field

The password field is automatically hashed with the default algorithm that is configured in TYPO3.

The hashed password is saved to the table `tx_powermail_domain_model_answers`.
It is possible to deactivate hashing with an EventListener listening to `MailFactoryBeforePasswordIsHashedEvent` and setting the property `passwordShouldBeHashed` to true.

In all Mails you can access the original value with the placeholder {passwordfieldname_originalValue}.

In the Finisher `SaveToAnyTableFinisher` you can use the field `passwordfieldname_originalValue` in the typoscript configuration and the plaintext password will be saved to the table.

In your own Finisher you can use the field `passwordfieldname_originalValue` to do whatever you want to do with the plaintext value.

## Restoring the old (insecure) behaviour

If you want to restore the old behaviour, to store the password in plaintext, you can apply the following changes.

### 1. Change Typoscript
This changes are all based upon the default configuration of powermail, if you have your own configuration applied you should change it accordingly.
```
plugin.tx_powermail.settings.setup {
  excludeFromPowermailAllMarker {
    receiverMail {
      excludeFromFieldTypes >
    }
    senderMail {
      excludeFromFieldTypes >
    }
    optinMail {
      excludeFromFieldTypes >
    }
  }
}
```

### 2. Integrate an Event Listener

```
<?php

declare(strict_types=1);

namespace Vendor\Ext\EventListener;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Events\MailFactoryBeforePasswordIsHashedEvent;

final class DontHashPasswordEventListener
{
    public function dontHash(MailFactoryBeforePasswordIsHashedEvent $event): void
    {
        if ($event->getAnswer()->getValueType() === Answer::VALUE_TYPE_PASSWORD) {
            $event->setPasswordShouldBeHashed(false);
        }
    }
}
```

### 3. Add EventListener to services.yaml

```
services:
  Vendor\Ext\EventListener\DontHashPasswordEventListener:
    tags:
      - name: 'event.listener'
        identifier: 'vendor-ext/dont-hash-password'
        method: 'dontHash'
```

After this changes the password will be stored in plaintext again in the answer table.
