# Features/Pi1/Validation/Honeypod.feature
@Pi1 @Pi1Validation @Pi1ValidationHoneypod
Feature: Honeypod

  Scenario: Check if Honeypod is rendered in form source
    Given I am on "/powermail/pi1/default/shortformprefilled"
    Then the sourcecode should contain 'name="tx_powermail_pi1[field][__hp]"'
