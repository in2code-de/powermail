# Features/Pi1/Misc/NoForm.feature
@Pi1 @Pi1Misc @Pi1MiscManipulatePowermailAllViaTyposcript
Feature: ManipulatePowermailAllViaTyposcript

  Scenario: Check if values can be manipulated in from {powermail_all} via typoscript
      Given I am on "/powermail/pi1/misc/manipulate-powermailall-via-ts"
      Then I should see "Manipulate Powermail All via TypoScript"

      When I fill in "tx_powermail_pi1[field][inputfield]" with "This is a test"
      When I check "brown"
      When I check "blue"
      When I check "yellow-no"
      When I check "green-no"
      When I check "Horse"
      When I check "Rabbit"
      When I check "Horse No"
      When I select "Volkswagen" from "tx_powermail_pi1[field][selectsinglecarlabelvaluemanipulation]"
      When I select "Vauxhall No" from "tx_powermail_pi1[field][selectsinglecarlabelvaluenomanipulation]"
      When I select "rose" from "tx_powermail_pi1[field][selectflower]"
      When I select "daisy-no" from "tx_powermail_pi1[field][selectsingleflowerlabelonlymanipulation]"
      When I select "Beech" from "tx_powermail_pi1[field][selectmultitrees][]"
      When I additionally select "Holly" from "tx_powermail_pi1[field][selectmultitrees][]"
      When I select "Beech - No" from "tx_powermail_pi1[field][marker][]"
      When I additionally select "Maple - No" from "tx_powermail_pi1[field][marker][]"
      When I select "Pear" from "tx_powermail_pi1[field][selectmultifruit][]"
      When I additionally select "Strawberry" from "tx_powermail_pi1[field][selectmultifruit][]"
      When I select "Apple - No" from "tx_powermail_pi1[field][selectmultifruitlabelonlynomanipulation][]"
      When I additionally select "Ananas - No" from "tx_powermail_pi1[field][selectmultifruitlabelonlynomanipulation][]"

      And I press "Absenden"

      Then I should see "Sind diese Eingaben korrekt?"
      Then I should see "Input field"
      Then I should see "Hello world"
      Then I should see "Changed color, blue"
      Then I should see "yellow-no, green-no"
      Then I should see "Horse, Changed animal"
      Then I should see "Horse no"
      Then I should see "Changed car"
      Then I should see "Vaushall No"
      Then I should see "Changed flower"
      Then I should see "daisy-no"
      Then I should see "Changed tree, Holly"
      Then I should see "Beech - No, Maple - No"
      Then I should see "Pear, Strawberry"
      Then I should see "Apple - No, Ananas - No"

