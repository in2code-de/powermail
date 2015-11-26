# Features/Pi1/Misc/AddQueryString.feature
@Pi1 @Pi1Misc @Pi1MiscAddQueryString
Feature: AddQueryString

  Scenario: Check third-party GET-params are passed after a submit
    Given I am on "/index.php?id=199"
    Then I should see "GET param NOT available"
    Given I am on "/index.php?id=199&get=xxx"
    Then I should see "GET param available"
    And I press "Jetzt Absenden"
    Then I should see "GET param available"
    And I press "Zurück"
    Then I should see "GET param available"
    And I press "Jetzt Absenden"
    Then I should see "GET param available"
    And I press "Weiter"
    Then I should see "GET param available"
    Then I should not see "GET param NOT available"
