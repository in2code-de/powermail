# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1[action]=optinConfirm&tx_powermail_pi1[controller]=Form&tx_powermail_pi1[hash]=7f40ffddedcaf03b89df5a3ec60a66aa50ab91ea9078e200098990529ac5fb08&tx_powermail_pi1[mail]=13881&cHash=b1d60f6cf20f7a4bc2fbf69df4151208 [/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1[action]=optinConfirm&tx_powermail_pi1[controller]=Form&tx_powermail_pi1[hash]=7f40ffddedcaf03b89df5a3ec60a66aa50ab91ea9078e200098990529ac5fb08&tx_powermail_pi1[mail]=13881&cHash=b1d60f6cf20f7a4bc2fbf69df4151208]"
    Then I should see "Die Nachricht wurde bereits bestätigt"
