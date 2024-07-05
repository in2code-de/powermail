# Features/Pi1/Validation/Html5Validation.feature
@Pi1 @Pi1Validation @Pi1ValidationMandatory @Pi1ValidationMandatoryHtml5Validation
Feature: Html5Validation

  @javascript
  Scenario: Check if mandatory Validation works (on &L=0)
    Given I am on "/powermail/pi1/validation/mandatory/html5"
    Then I should see "Input"
    Then I should see "Input (Pattern http://ww)"
    Then I should see "Date"
    Then I should see "Textarea"
    Then I should see "Select"
    Then I should see "Select Multi"
    Then I should see "Check"
    Then I should see "Radio"
    And I press "Submit"

    Then I should not see "Dieses Feld muss ausgefüllt werden!"
    Then I should not see "Sind diese Eingaben korrekt?"
    Then I should not see "Danke, Ihre Angaben:"
