# Features/Pi1/Misc/MailUnhidden.feature
@Pi1 @Pi1Misc @Pi1MiscOptin @Pi1MiscOptinMailUnhidden
Feature: Follow an optin link but mail is already unhidden

  Scenario: Check if an error message appears if I try to open an optin URI but mail is alreay unhidden
    Given I am on "/powermail/pi1/default/shortform-doubleoptin?tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form&tx_powermail_pi1%5Bhash%5D=2af591e1bd492d086331a299b5d7996ba28dde8c148c8d1ac05cdc632ac0fb7a&tx_powermail_pi1%5Bmail%5D=306"
    #Then I should see "Die Nachricht wurde bereits bestätigt"
