# Features/Pi1/Misc/HtmlLabels.feature
@Pi1 @Pi1Misc @Pi1MiscHtmlLabels
Feature: HtmlLabels

  Scenario: Check if Html-Labels are rendered correct with removeXSS()
    Given I am on "/index.php?id=61"
    Then I should see "AGB accepted"
    Then the sourcecode should contain 'Email <a href="/index.php?id=3">AGB accepted</a>'
    Then the sourcecode should contain 'XSS Test <sc<x>ript>alert'