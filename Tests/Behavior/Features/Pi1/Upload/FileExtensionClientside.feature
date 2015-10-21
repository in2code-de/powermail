# Features/Pi1/Upload/FileExtensionClientside.feature
@Pi1 @Pi1Upload @Pi1UploadFileExtensionClientside
Feature: Check if Uploads in a Form work as expected with given file extension

  @javascript
  Scenario: Check clientside validation
    Given I am on "/index.php?id=107"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Der Dateityp ist nicht erlaubt, bitte versuchen Sie einen anderen Typ!"
    Then I should not see "Error, this text should not be here"

    When I attach the file "logo1.png" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Sind diese Eingaben korrekt?"
