# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form&tx_powermail_pi1%5Bhash%5D=2b9f72e80618b80cb07425ce1d4bef0a0151186a585a36e424d82974b8877611&tx_powermail_pi1%5Bmail%5D=1&cHash=201380858a703234601b3ba8a38807fd"
    Then I should see "Die Nachricht wurde bereits bestätigt"
