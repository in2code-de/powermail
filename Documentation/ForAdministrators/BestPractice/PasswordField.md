# Password Field

The password field is automatically hashed with the default algorithm that is configured in TYPO3.

The hashed password is saved to the table `tx_powermail_domain_model_answers`.
It is possible to deactivate hashing with an EventListener listening to `MailFactoryBeforePasswordIsHashedEvent` and setting the property `passwordShouldBeHashed` to true.

In all Mails you can access the original value with the placeholder {passwordfieldname_originalValue}.

In the Finisher `SaveToAnyTableFinisher` you can use the field `passwordfieldname_originalValue` in the typoscript configuration and the plaintext password will be saved to the table.

In your own Finisher you can use the field `passwordfieldname_originalValue` to do whatever you want to do with the plaintext value.
