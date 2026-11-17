# Features/Pi1/FieldType/Country.feature
@Pi1 @Pi1FieldType @Pi1FieldTypeCountry
Feature: Country

  # Countries are sorted by the locale of the current language, not by byte value
  # Related: in2code-de/powermail#1415

  # L=0
  Scenario: Countries with umlauts are sorted alphabetically in german
    Given I am on "/powermail/pi1/default/all-fields"
    Then the option "Afghanistan" should be listed before "Ägypten" in "tx_powermail_pi1[field][country]"
    Then the option "Ägypten" should be listed before "Albanien" in "tx_powermail_pi1[field][country]"
    Then the option "Åland-Inseln" should be listed before "Albanien" in "tx_powermail_pi1[field][country]"
    Then the option "Oman" should be listed before "Österreich" in "tx_powermail_pi1[field][country]"
    Then the option "Österreich" should be listed before "Pakistan" in "tx_powermail_pi1[field][country]"
    Then the option "Österreich" should be listed before "Zypern" in "tx_powermail_pi1[field][country]"

  # L=1
  Scenario: Countries with accents are sorted alphabetically in english
    Given I am on "/en/powermail/pi1/default/all-fields-ml-en"
    Then the option "Afghanistan" should be listed before "Åland Islands" in "tx_powermail_pi1[field][country]"
    Then the option "Åland Islands" should be listed before "Albania" in "tx_powermail_pi1[field][country]"
    Then the option "Åland Islands" should be listed before "Zimbabwe" in "tx_powermail_pi1[field][country]"
