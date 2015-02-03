# Features/Pi1/Default/ShortFormDoubleOptin.feature
@Pi1 @Pi1Default @Pi1DefaultShortDoubleOptin
Feature: ShortFormDoubleOptin

  Scenario: Check if form is rendered correct
    Given I am on "/index.php?id=65"
    Then I should see "Short Form Prefilled"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"

  Scenario: Check if submit works properly
    Given I am on "/index.php?id=65"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Alex"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Kellner"
    When I fill in "tx_powermail_pi1[field][email]" with "alex@in2code.de"
    And I press "Jetzt Absenden"
    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see "Kellner"
    Then I should see "alex@in2code.de"
    And I press "Weiter"
    Then I should see "Bitte überprüfen Sie Ihr E-Mail-Postfach und bestätigen Sie diese Aktion."

  Scenario: Check if optinConfirm shows error if wrong cHash
    Given I am on "/index.php?id=65&tx_powermail_pi1%5Bhash%5D=abc&tx_powermail_pi1%5Bmail%5D=3178&tx_powermail_pi1%5Baction%5D=optinConfirm&tx_powermail_pi1%5Bcontroller%5D=Form"
    Then I should see "Der eingegebene Link ist ungültig"