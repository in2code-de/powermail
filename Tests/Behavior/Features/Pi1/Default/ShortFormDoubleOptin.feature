# Features/Pi1/Default/ShortFormDoubleOptin.feature
@Pi1 @Pi1Default @Pi1DefaultShortDoubleOptin
Feature: ShortFormDoubleOptin

  Scenario: Check if form is rendered correct
    Given I am on "/powermail/pi1/default/shortform-doubleoptin"
    Then I should see "Short Form Prefilled"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Check if submit works properly
    Given I am on "/powermail/pi1/default/shortform-doubleoptin"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Weiter"
    Then I should see "Bitte überprüfen Sie Ihr E-Mail-Postfach und bestätigen Sie diese Aktion."

  Scenario: Check if optinConfirm shows error if wrong cHash
    Given I am on "/index.php?id=65&tx_powermail_pi1%5Bhash%5D=abc&tx_powermail_pi1%5Bmail%5D=3178&tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form"
    Then I should see "Der eingegebene Link ist ungültig"
