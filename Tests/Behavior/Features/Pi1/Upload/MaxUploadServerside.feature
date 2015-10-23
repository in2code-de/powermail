# Features/Pi1/Upload/MaxUploadServerside.feature
@Pi1 @Pi1Upload @Pi1UploadMaxUploadServerside
Feature: Check if Uploads in a Form work as expected

  @javascript
  Scenario: Check serverside validation
    Given I am on "/index.php?id=186"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "sally.jpg" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"

    Then I should see "Die ausgewählte Datei ist zu groß"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    And I press "Weiter"

    Then I should see "Alle Werte:"
    Then I should see "Alex"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
