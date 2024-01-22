# Features/Pi1/Upload/FileExtensionClientside.feature
@Pi1 @Pi1Upload @Pi1UploadFileExtensionClientside
Feature: Check if Uploads in a Form work as expected with given file extension

  @javascript
  Scenario: Check clientside validation with wrong format
    Given I am on "/powermail/pi1/upload/upload-fileextension/upload-png/png-clientside"
    When I attach the file "test.txt" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Dateien mit dieser Erweiterung dürfen nicht hochgeladen werden!"
    Then I should not see "Error, this text should not be here"

  @javascript
  Scenario: Check clientside validation with correct format
    Given I am on "/powermail/pi1/upload/upload-fileextension/upload-png/png-clientside"
    When I attach the file "in2code.png" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Sind diese Werte richtig?"

  @javascript
  Scenario: Check clientside validation with correct format (but uppercase)
    Given I am on "/powermail/pi1/upload/upload-fileextension/upload-png/png-clientside"
    When I attach the file "in2code.PNG" to "tx_powermail_pi1[field][upload][]"
    And I press "Submit"
    Then I should see "Sind diese Werte richtig?"
