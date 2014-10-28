# Features/Pi1/Misc/GoogleAdwords.feature
@Pi1 @Pi1Misc @Pi1MiscGoogleAdwords
Feature: Google Adwords

  Scenario: Check if conversion JavaScript is rendered correct
    Given I am on "/index.php?id=113"
    Then I should see "Short Form Prefilled"
    And I press "Jetzt Absenden"

    Then the sourcecode should contain 'var google_conversion_id = 123;'
    Then the sourcecode should contain 'var google_conversion_language = "de";'
    Then the sourcecode should contain 'var google_conversion_format = "3";'
    Then the sourcecode should contain 'var google_conversion_color = "ffffff";'
    Then the sourcecode should contain 'var google_conversion_label = "def";'
    Then the sourcecode should contain 'var google_conversion_value = 0;'