# Features/Pi1/Default/Ajax.feature
@Pi1 @Pi1Upload @Pi1DefaultUploadAjax
Feature: AJAX Upload

  @javascript
  Scenario: Check if Uploads in a Form work as expected
    Given I am on "/powermail/pi1/upload/upload-ajax"
    Then I should see "Name"
    Then I should see "E-Mail"
    Then I should see "Upload"
    Then I should see "Uploads"
    Then I should see "Uploads2"

    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Daniel Boxhammer"
    Then I should see text matching "test_\d+.txt|test.txt"
    And I press "Zurück"
    And I wait "a few" seconds

    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "sally.jpg" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"
    And I wait "a few" seconds

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Daniel Boxhammer"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "sally_\d+.jpg|sally.jpg"
    And I press "Weiter"
    And I wait "a few" seconds

    Then I should see "Alle Werte:"
    Then I should see "Daniel Boxhammer"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "sally_\d+.jpg|sally.jpg"
