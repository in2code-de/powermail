# Features/Pi1/FieldType/Content.feature
@Pi1 @Pi1FieldType @Pi1FieldTypeContent
Feature: Content

  Scenario: Check if field type "content" is rendered correctly
    Given I am on "/index.php?id=10"
    Then the sourcecode should contain '<h1 class="csc-firstHeader">Content Element (Sally Dalmatiner)</h1>'
