# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form&tx_powermail_pi1%5Bhash%5D=5ac6ed0164b67822a0235a2f3b544d7266a97f131d7de71480953c0d6ae970dd&tx_powermail_pi1%5Bmail%5D=1&cHash=785a789668b09da69570db53b79e242e"
    Then I should see "Die Nachricht wurde bereits bestätigt"
