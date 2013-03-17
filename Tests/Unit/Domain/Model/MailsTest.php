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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_Powermail_Domain_Model_Fields.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage powermail
 *
 * @author Alex Kellner <alexander.kellner@in2code.de>
 */
class Tx_Powermail_Domain_Model_MailsTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Powermail_Domain_Model_Mails
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Powermail_Domain_Model_Mails();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getSenderNameReturnsInitialValueForString() {
        $this->assertSame(
            '',
            $this->fixture->getSenderName()
        );
    }

	/**
	 * @test
	 */
	public function setTitleForStringSetsSenderName() {
		$this->fixture->setSenderName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSenderName()
		);
	}

    /**
	 * @test
	 */
	public function getSenderMailReturnsInitialValueForString() {
        $this->assertSame(
            '',
            $this->fixture->getSenderMail()
        );
    }

	/**
	 * @test
	 */
	public function setTitleForStringSetsSenderMail() {
		$this->fixture->setSenderMail('Conceived at T3CON10');
		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSenderMail()
        );
	}


    /**
     * @test
     */
    public function getSubjectReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getSubject()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsSubject() {
        $this->fixture->setSubject('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getSubject()
        );
    }

    /**
     * @test
     */
    public function getReceiverMailReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getReceiverMail()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringReceiverMail() {
        $this->fixture->setReceiverMail('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getReceiverMail()
        );
    }

     /**
     * @test
     */
    public function getBodyReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getBody()
        );
    }

    /**
     * @test
     */
    public function setBodyForStringSetsBody() {
        $this->fixture->setBody('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getBody()
        );
    }

    /**
     * @test
     */
    public function getFeuserReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getFeuser()
        );
    }

    /**
     * @test
     */
    public function setFeuserForStringSetsFeuser() {
        $this->fixture->setFeuser('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getFeuser()
        );
    }

     /**
     * @test
     */
    public function getSenderipReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getSenderIp()
        );
    }

    /**
     * @test
     */
    public function setsenderIpForStringSetssenderIp() {
        $this->fixture->setSenderIp('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getSenderIp()
        );
    }

     /**
     * @test
     */
    public function getuserAgentReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getSenderIp()
        );
    }

    /**
     * @test
     */
    public function setuserAgentForStringSetsUseragent() {
        $this->fixture->setUserAgent('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getUserAgent()
        );
    }

     /**
     * @test
     */
    public function getSpamfactorReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getSpamFactor()
        );
    }

    /**
     * @test
     */
    public function setSpamfactorForStringSetsSpamfactor() {
        $this->fixture->setSpamFactor('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getSpamFactor()
        );
    }


     /**
     * @test
     */
    public function getTimeReturnsInitialValueForNull(){
        $this->assertSame(
            NULL,
            $this->fixture->getTime()
        );
    }

    /**
     * @test
     */
    public function setTimeForDatetimeSetsTime() {
        $now = mktime();
        $this->fixture->setTime($now);
        $this->assertSame(
            $now,
            $this->fixture->getTime()
        );
    }

    /**
     * @test
     */
    public function getFormReturnsInitialValueForNull(){
        $this->assertSame(
            NULL,
            $this->fixture->getForm()
        );
    }

    /**
     * @test
     */
    public function setFormForTx_Powermail_Domain_Model_FormsSetsForm() {
        $form = new Tx_Powermail_Domain_Model_Forms;
        $this->fixture->setForm($form);
        $this->assertSame(
            $form,
            $this->fixture->getForm()
        );
    }


    /**
     * @test
     */
    public function getAnswersReturnsInitialValueForNull(){
        $this->assertSame(
            NULL,
            $this->fixture->getAnswers()
        );
    }

    /**
     * @test
     */
    public function setAnswersForTx_Powermail_Domain_Model_AnswersSetsAnswers() {
        $dummy = new Tx_Extbase_Persistence_ObjectStorage;
        $this->fixture->setAnswers($dummy);
        $this->assertSame(
            $dummy,
            $this->fixture->getAnswers()
        );
    }


    /**
     * @test
     */
    public function getCrdateReturnsInitialValueForNull(){
        $this->assertSame(
            NULL,
            $this->fixture->getCrdate()
        );
    }

    /**
     * @test
     */
    public function setCrdateForDatetimeSetsCrdate() {
        $now = mktime();
        $this->fixture->setCrdate($now);
        $this->assertSame(
            $now,
            $this->fixture->getCrdate()
        );
    }

    /**
     * @test
     */
    public function getHiddenReturnsInitialValueForFalse(){
        $this->assertSame(
            false,
            $this->fixture->getHidden()
        );
    }

    /**
     * @test
     */
    public function setHiddenToTrue() {

        $this->fixture->setHidden(true);
        $this->assertSame(
            true,
            $this->fixture->getHidden()
        );
    }

    /**
     * @test
     */
    public function getMarketingsearchtermReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getMarketingSearchterm()
        );
    }

    /**
     * @test
     */
    public function setMarketingsearchtermForStringSetsMarketingsearchterm() {
        $this->fixture->setMarketingSearchterm('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingSearchterm()
        );
    }

    /**
     * @test
     */
    public function getmarketingRefererReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getMarketingSearchterm()
        );
    }

    /**
     * @test
     */
    public function setmarketingRefererForStringSetsmarketingReferer() {
        $this->fixture->setMarketingReferer('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingReferer()
        );
    }

    /**
     * @test
     */
    public function getmarketingPayedSearchResultReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getMarketingPayedSearchResult()
        );
    }

    /**
     * @test
     */
    public function setmarketingPayedSearchResultForStringSetsmarketingPayedSearchResult() {
        $this->fixture->setMarketingPayedSearchResult('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingPayedSearchResult()
        );
    }

   /**
     * @test
     */
    public function getmarketingLanguageReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getMarketingLanguage()
        );
    }

    /**
     * @test
     */
    public function setmarketingLanguageForStringSetsmarketingLanguage() {
        $this->fixture->setMarketingLanguage('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingLanguage()
        );
    }

   /**
     * @test
     */
    public function getmarketingBrowserLanguageReturnsInitialValueForString(){
        $this->assertSame(
            '',
            $this->fixture->getMarketingBrowserLanguage()
        );
    }

    /**
     * @test
     */
    public function setmarketingBrowserLanguageForStringSetsmarketingBrowserLanguage() {
        $this->fixture->setMarketingBrowserLanguage('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingBrowserLanguage()
        );
    }

     /**
     * @test
     */
    public function getmarketingFunnelReturnsInitialValueForFalse(){
        $this->assertSame(
            false,
            $this->fixture->getMarketingFunnel()
        );
    }

    /**
     * @test
     */
    public function setmarketingFunnelForStringSetsmarketingFunnel() {
        $this->fixture->setMarketingFunnel('Conceived at T3CON10');
        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getMarketingFunnel()
        );
    }
}

?>