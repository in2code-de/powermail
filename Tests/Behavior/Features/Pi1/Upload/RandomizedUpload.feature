# Features/Pi1/Upload/RandomizedUpload.feature
@Pi1 @Pi1Upload @Pi1UploadRandomizedUpload
Feature: RandomizedUpload

  @javascript
  Scenario: Check if Uploads in a Form work as expected
    Given I am on "/index.php?id=77"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    When I attach the file "logo2.png" to "tx_powermail_pi1[field][uploads][]"
    When I attach the file "sally.jpg" to "tx_powermail_pi1[field][uploads2][]"
    And I press "Submit"

    Then I should see "Sind diese Eingaben korrekt?"
    Then I should see "Alex"
    Then I should not see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "[a-z0-9]{32}.txt"
    Then I should not see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "[a-z0-9]{32}.png"
    Then I should not see text matching "sally_\d+.jpg|sally.jpg"
    Then I should see text matching "[a-z0-9]{32}.jpg"
    And I press "Weiter"

    Then I should see "Alle Werte:"
    Then I should see "Alex"
    Then I should not see text matching "test_\d+.txt|test.txt"
    Then I should see text matching "[a-z0-9]{32}.txt"
    Then I should not see text matching "logo2_\d+.png|logo2.png"
    Then I should see text matching "[a-z0-9]{32}.png"
    Then I should not see text matching "sally_\d+.jpg|sally.jpg"
    Then I should see text matching "[a-z0-9]{32}.jpg"