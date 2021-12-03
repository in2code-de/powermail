# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "https://powermail.ddev.site/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form&tx_powermail_pi1%5Bhash%5D=ae1250d37a8256b66fcc34972908c0ff4f9c325a0496e8b5e1174c0e5a019054&tx_powermail_pi1%5Bmail%5D=2&cHash=0c57281f018dfe3a941d451c9ad4c643"
    Then I should see "Die Nachricht wurde bereits bestätigt"
