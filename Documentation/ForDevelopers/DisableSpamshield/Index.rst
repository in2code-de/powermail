.. include:: ../../Includes.txt

.. _disableSpamshield:


Disable spamshield
^^^^^^^^^^^^^^^^^^

Introduction
""""""""""""

Spamshield is a nice feature of powermail to prevent spam. Nevertheless sometimes you want to test powermail but
spamshield blocks your tests. Wouldn't it be great to disable spamshield on some conditions (like on your IP-Address)?

Available since version
"""""""""""""""""""""""

powermail 5.1.0

Small example
"""""""""""""

Just define which classes should be used. Extend your class with
`\In2code\Powermail\Domain\Validator\SpamShield\Breaker\AbstractBreaker` and add a method isDisabled()

::

   plugin.tx_powermail.settings.setup {
       spamshield._disable {
           1 {
             class = Vendor\PowermailExtended\Domain\Validator\SpamShield\Breaker\MyBreaker
             configuration {
                 anyConfiguration = foo,bar
             }
         }
       }
   }


::

   <?php
   namespace Vendor\PowermailExtended\Domain\Validator\SpamShield\Breaker;

   use In2code\Powermail\Domain\Validator\SpamShield\Breaker\AbstractBreaker;

   /**
    * Class MyBreaker
    */
   class MyBreaker extends AbstractBreaker
   {

        /**
         * @return bool true if spamshield should be disabled
         */
        public function isDisabled(): bool
        {
           return true;
        }
   }

Extended examples and existing breakers
"""""""""""""""""""""""""""""""""""""""

There are already two breakers that can be used in powermail

::

   plugin.tx_powermail.settings.setup {
       spamshield._disable {
         1 {
             # Disable spamcheck if visitor is in IP-Range
             class = In2code\Powermail\Domain\Validator\SpamShield\Breaker\IpBreaker
             configuration {
                 // Commaseparated list of IPs. Use * for wildcards in the IP-address
                 ipWhitelist = 127.0.0.1,192.168.0.*
             }
         }
         2 {
             # Disable spamcheck if any field contains a given value - like "powermailTestCase"
             class = In2code\Powermail\Domain\Validator\SpamShield\Breaker\ValueBreaker
             configuration {
                 value = powermailTestCase
             }
         }
       }
   }



Have a look into the extension PHP-Files to get some inspirations:

::

   <?php
   declare(strict_types=1);
   namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

   use In2code\Powermail\Domain\Model\Answer;

   /**
    * Class ValueBreaker
    */
   class ValueBreaker extends AbstractBreaker
   {

       /**
        * @return bool
        */
       public function isDisabled(): bool
       {
           $configuration = $this->getConfiguration();
           $this->checkConfiguration($configuration);
           foreach ($this->getMail()->getAnswers() as $answer) {
               /** @var $answer Answer */
               if ($answer->getValue() === $configuration['value']) {
                   return true;
               }
           }
           return false;
       }

       /**
        * @param array $configuration
        * @return void
        */
       protected function checkConfiguration(array $configuration)
       {
           if (empty($configuration['value'])) {
               throw new \UnexpectedValueException('No value given to check for', 1516025541289);
           }
       }
   }

Or:

::

   <?php
   declare(strict_types=1);
   namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

   use TYPO3\CMS\Core\Utility\GeneralUtility;

   /**
    * Class IpBreaker
    */
   class IpBreaker extends AbstractBreaker
   {

       /**
        * @return bool
        */
       public function isDisabled(): bool
       {
           foreach ($this->getIpAddresses() as $ipAddress) {
               if ($this->isIpMatching(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $ipAddress)) {
                   return true;
               }
           }
           return false;
       }

       /**
        * @param string $givenIp like "127.0.0.1"
        * @param string $ipRange like "127.0.0.1" or "192.168.*.*"
        * @return bool
        */
       protected function isIpMatching(string $givenIp, string $ipRange): bool
       {
           if (stristr($ipRange, '*')) {
               $rangeParts = explode('.', $ipRange);
               $givenParts = explode('.', $givenIp);
               if (count($rangeParts) !== count($givenParts)) {
                   throw new \UnexpectedValueException(
                       'Number of segments between current ip and compared ip does not match',
                       1516024779382
                   );
               }
               foreach (array_keys($rangeParts) as $key) {
                   if ($rangeParts[$key] === '*') {
                       $givenParts[$key] = '*';
                   }
               }
               $givenIp = implode('.', $givenParts);
           }
           return $givenIp === $ipRange;
       }

       /**
        * @return array
        */
       protected function getIpAddresses(): array
       {
           $configuration = $this->getConfiguration();
           if (empty($configuration['ipWhitelist'])) {
               throw new \UnexpectedValueException(
                   'Setup ...spamshield.disable.NO.configuration.ipWhitelist not given',
                   1516024283512
               );
           }
           return GeneralUtility::trimExplode(',', $configuration['ipWhitelist'], true);
       }
   }



Some notices
""""""""""""

* You have to add the method isDisabled() and you have to return a boolean value
* The method initialize() will always be called at first
