# Features/Pi1/Misc/2Forms.feature
@Pi1 @Pi1Misc @Pi1MiscTemplatePaths @Pi1MiscTemplatePathsOriginal
Feature: Original

  Scenario: Check if original Templates render the form
    Given I am on "/index.php?id=100"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"
    Then I should not see "fileadmin"
    Then I should not see "Form.html"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Harald"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Müller"
    When I fill in "tx_powermail_pi1[field][email]" with "hm@in2code.de"
    And I press "Jetzt Absenden"

    Then I should see "Harald"
    Then I should see "Müller"
    Then I should see "hm@in2code.de"