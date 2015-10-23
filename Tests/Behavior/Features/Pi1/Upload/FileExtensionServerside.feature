# Features/Pi1/Upload/FileExtensionServerside.feature
@Pi1 @Pi1Upload @Pi1UploadFileExtensionServerside
Feature: Check if Uploads in a Form work as expected with given file extension

  @javascript
  Scenario: Check serverside validation
    Given I am on "/index.php?id=189"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Der Dateityp ist nicht erlaubt, bitte versuchen Sie einen anderen Typ!"

    When I attach the file "logo1.png" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Sind diese Eingaben korrekt?"
