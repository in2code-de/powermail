<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 *
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Domain_Repository_FormsRepositoryTests extends Tx_Extbase_Tests_Unit_BaseTestCase {

    /**
     * @var Tx_Powermail_Domain_Repository_FormsRepository
     */
    protected $fixture;

    /**
     * @var Tx_Phpunit_Framework: null
     */
    protected $testDatabase = NULL;

    public function setUp() {
        $this->fixture = new Tx_Powermail_Domain_Repository_FormsRepository();
        $this->testDatabase = new Tx_Phpunit_Framework('tx_powermail_domain_model_forms');
    }

    public function tearDown() {
        $this->testDatabase->cleanUp();
        unset($this->fixture,$this->testDatabase);
    }

    /**
     * @test
     */
    public function findByUidsReturnsCorrectCountForString() {
        $uidArray = array();

        $uidArray[]=$this->testDatabase>createRecord('tx_powermail_domain_model_forms',array());
        $uidArray[]=$this->testDatabase>createRecord('tx_powermail_domain_model_forms',array());
        $uidArray[]=$this->testDatabase>createRecord('tx_powermail_domain_model_forms',array());
        $uidArray[]=$this->testDatabase>createRecord('tx_powermail_domain_model_forms',array());
        $uidArray[]=$this->testDatabase>createRecord('tx_powermail_domain_model_forms',array());

        $this->assertSame(5,$this->fixture->findByUids(implode(',',$uidArray))->count());
    }
}
?>