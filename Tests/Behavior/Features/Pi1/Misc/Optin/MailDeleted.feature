# Features/Pi1/Misc/OptinMailDeleted.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailDeleted
Feature: Follow an optin link but mail is deleted

  Scenario: Check if an error message appears if I try to open an optin URI with a deleted mail
    Given I am on "/index.php?id=65&tx_powermail_pi1[hash]=731a0deff2&tx_powermail_pi1[mail]=8733&tx_powermail_pi1[action]=optinConfirm"
    Then I should see "Der eingegebene Link ist ungültig"
