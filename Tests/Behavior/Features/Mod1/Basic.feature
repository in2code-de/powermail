# Features/Pi1/Default/ShortForm.feature
@Mod1 @Mod1Basic
Feature: Basic
  Basicly checks backend module

  @javascript
  Scenario: Login into backend
    Given I am on "/typo3/index.php"
    Then the sourcecode should contain 't3-login-logo'
    When I fill in "username" with "editor"
    When I fill in "p_field" with "editor"
    And I press "t3-login-submit"

    And I wait "6" seconds
    Then I click on the element "#web_PowermailM1"
    And I wait "3" seconds
    Then I follow "All Fields"
    And I wait "6" seconds

    And I swith to iframe "content"
    Then I should see an "#powermail_module_search" element
    Then I should see "Mail Listings"
    Then I should see "Fulltext Search"
    Then I should see "Senders Name"
