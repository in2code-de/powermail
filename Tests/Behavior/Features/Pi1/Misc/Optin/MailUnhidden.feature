# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/index.php?id=65&tx_powermail_pi1%5Bhash%5D=40901e4a2b1d14fb015ebbb2df98b8b1ea710d0d85668f6fd05feecaba2b665b&tx_powermail_pi1%5Bmail%5D=13803&tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form&cHash=90d6f660bce04da2fb24dd9560e53948"
    Then I should see "Die Nachricht wurde bereits bestätigt"
