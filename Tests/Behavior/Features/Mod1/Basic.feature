# Features/Mod1/Basic.feature
@Mod1 @Mod1Basic
Feature: Basic
  Basically checks if the backend module exists

  @javascript
  Scenario: Login into backend
    Given I am on "/typo3/index.php"
    Then the sourcecode should contain 'typo3-login-logo'
    When I fill in "username" with "editor"
    When I fill in "p_field" with "editor"
    And I press "t3-login-submit"

    And I wait "3" seconds
    Given I am on "/typo3/module/web/PowermailM1"

      Then the response should contain "typo3-backend-module-router"
