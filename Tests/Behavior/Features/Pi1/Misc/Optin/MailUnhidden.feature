# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/index.php?id=65&tx_powermail_pi1[hash]=e15ae9239a&tx_powermail_pi1[mail]=8734&tx_powermail_pi1[action]=optinConfirm"
    Then I should see "Die Nachricht wurde bereits bestätigt"
