# Features/Pi1/Default/Upload.feature
@Pi1DefaultUpload
Feature: Upload
  In order to see a word definition
  As a website user
  I need to be able to submit a form

  @javascript
  Scenario: Check if Uploads in a Form work as expected
    Given I am on "/index.php?id=26"
    Then I should see "Name"
    Then I should see "E-Mail"
    Then I should see "Upload"
    Then I should see "Uploads"
    Then I should see "Uploads2"

    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see text matching "test_\d+.txt|test.txt"
    And I press "Zurück"

    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "sally.jpg" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "sally_\d+.jpg|sally.jpg"
    And I press "Weiter"

    Then I should see "Alle Werte:"
    Then I should see "Alex"
    Then I should see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "sally_\d+.jpg|sally.jpg"