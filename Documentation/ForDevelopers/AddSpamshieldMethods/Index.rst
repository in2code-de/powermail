.. include:: ../../Includes.txt

.. _addSpamshieldMethods:


Add spamshield methods
^^^^^^^^^^^^^^^^^^^^^^

Introduction
""""""""""""

Powermail adds spamshield methods per default (details see :ref:`spamprevention`).
If you want to add own spamshield methods or if you want to override a original
method, you can do that with a bit of TypoScript and a PHP-file.

Maybe you want to:

* Validate IP addresses
* Validate duplicate entries
* Add external spam prevention methods
* Something else...

Small example
"""""""""""""

Just define which classes should be used. Every method like \*Finisher() will be called - e.g. myFinisher():

::

   plugin.tx_powermail.settings.setup {
       spamshield.methods {
           10 {
               class = Vendor\Ext\Domain\Validator\Spamshield\SpamPreventionMethod
           }
       }
   }


Add a php-file and extend your class with the AbstractMethod from powermail:
::

   <?php
   namespace Vendor\Ext\Domain\Validator\Spamshield;

   use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;

   /**
    * Class DoSomethingDataProcessor
    */
   class DoSomethingDataProcessor extends AbstractMethod
   {

        /**
         * My spamcheck
         *
         * @return bool true if spam recognized
         */
        public function spamCheck()
        {
           // ...
        }
   }

Extended example
""""""""""""""""

See the advanced example with some configuration
in TypoScript

::

   plugin.tx_powermail.settings.setup {
       spamshield.methods {
           10 {
               # enable or disable this check
               _enable = 1

               # Spamcheck name for log, email and validation message in frontend
               name = IP blacklist check

               # Classname to load with method spamCheck()
               class = Vendor\Ext\Domain\Validator\Spamshield\SpamPreventionMethod

               # if this check failes - add this indication value to indicator
               indication = 7

               # method configuration
               configuration {
                   myConfiguration = something
               }
           }
       }
   }



Add your php-file again and extend your class with the AbstractMethod from powermail:

::

   <?php
   namespace Vendor\Ext\Domain\Validator\Spamshield;

   use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;

   /**
    * Class SpamPreventionMethod
    */
   class SpamPreventionMethod extends AbstractMethod
   {

        /**
         * @var null|Mail
         */
        protected $mail = null;

        /**
         * @var array
         */
        protected $configuration = [];

        /**
         * @var array
         */
        protected $arguments = [];

        /**
         * @var array
         */
        protected $settings = [];

       /**
        * Will be called always at first
        *
        * @return void
        */
       public function initialize()
       {
       }

       /**
        * Will be called before spamCheck()
        *
        * @return void
        */
       public function initializeSpamCheck()
       {
       }

       /**
        * My spam check
        *
        * @return void true if spam recognized
        */
       public function spamCheck()
       {
           // get value from configuration
           $foo = $this->configuration['myConfiguration'];

           foreach ($this->mail->getAnswers() as $answer) {
               if ($answer->getField()->getMarker() === 'markerName') {
                   if ($answer->getValue() === 'foo') {
                       // if spam recognized, return true
                       return false;
                   }
               }
           }

           return true;
       }
   }

Some notices
""""""""""""

* Method spamCheck() will be called always
* The method initialize() and initializeSpamCheck() will always be called at first
* Per default 1 - 7 is already in use from powermail itself (HoneyPodMethod, LinkMethod, NameMethod, etc...).
