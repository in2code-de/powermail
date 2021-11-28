# Features/Pi1/Default/ShortForm.feature
@Mod1 @Mod1Basic
Feature: Basic
  Basicly checks backend module

  @javascript
  Scenario: Login into backend
    Given I am on "/typo3/index.php"
    Then the sourcecode should contain 'typo3-login-logo'
    When I fill in "username" with "editor"
    When I fill in "p_field" with "editor"
    And I press "t3-login-submit"

    And I wait "3" seconds
    Given I am on "typo3/module/web/PowermailM1?id=15"

    And I switch to iframe number 1
    Then I should see "Mail Listings"
    Then I should see "Fulltext Search"
    Then I should see "Senders Name"
