# Features/Pi1/Upload/MaxUploadClientside.feature
@Pi1 @Pi1Upload @Pi1UploadMaxUploadClientside
Feature: Check if Uploads in a Form work as expected

  @javascript
  Scenario: Check clientside validation
    Given I am on "/powermail/pi1/upload/upload-filesize/upload-30kb-clientside"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "sally.jpg" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"
    Then I should see "Diese Datei ist zu groß!"

    Given I am on "/powermail/pi1/upload/upload-filesize/upload-30kb-clientside"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "logo1.png" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"

    Then I should see "Sind diese Werte richtig?"
    Then I should see "Daniel Boxhammer"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "logo1_\d+.png|logo1.png"
    And I press "Weiter"

    Then I should see "Alle Werte:"
    Then I should see "Daniel Boxhammer"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "logo1_\d+.png|logo1.png"
