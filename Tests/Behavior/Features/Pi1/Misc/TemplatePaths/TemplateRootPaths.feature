# Features/Pi1/Misc/2Forms.feature
@Pi1 @Pi1Misc @Pi1MiscTemplatePaths @Pi1MiscTemplatePathsTemplateRootPaths
Feature: Original

  Scenario: Check if templateRootPaths will work
    Given I am on "/powermail/pi1/misc/templatepaths/templaterootpaths"
    Then I should see "Vorname"
    Then I should see "Nachname"
    Then I should see "E-Mail"
    Then I should see "fileadmin/powermail/rootPaths/Templates/Form/Form.html"
    Then I should see "fileadmin/powermail/rootPaths/Partials/Form/Input.html"
    When I fill in "tx_powermail_pi1[field][firstname]" with "Daniel"
    When I fill in "tx_powermail_pi1[field][lastname]" with "Boxhammer"
    When I fill in "tx_powermail_pi1[field][email]" with "Daniel_Boxhammer25@fake-yahoo-10000.com"
    And I press "Jetzt Absenden"

    Then I should see "Daniel"
    Then I should see "Boxhammer"
    Then I should see "Daniel_Boxhammer25@fake-yahoo-10000.com"
