.. include:: ../../Includes.txt

.. _addDataProcessors:


Add Data Processors
^^^^^^^^^^^^^^^^^^^

Introduction
""""""""""""

Let's say you want to easily add some own php functions,
that should be called before the mail object will be persisted
and send to the receivers or if you want to override original
DataProcessors from powermail, a DataProcessor will be the
best choice.

Maybe you want to:

* Change user inputs
* Do a redirect before mails will be sent
* Add additional information before the mail object will be stored
* Something else...

Small example
"""""""""""""

Just define which classes should be used. Every method like \*Finisher() will be called - e.g. myFinisher():

::

   plugin.tx_powermail.settings.setup {
       dataProcessors {
           1 {
               class = Vendor\Ext\DataProcessor\DoSomethingDataProcessor
           }
       }
   }


Add a php-file and extend your class with the AbstractDataProcessor from powermail:
::

   <?php
   namespace Vendor\Ext\DataProcessor;

   use In2code\Powermail\DataProcessor\AbstractDataProcessor;

   /**
    * Class DoSomethingDataProcessor
    *
    * @package Vendor\Ext\DataProcessor
    */
   class DoSomethingDataProcessor extends AbstractDataProcessor
   {

       /**
        * MyDataProcessor
        *
        * @return void
        */
       public function doSomethingElseDataProcessor()
       {
           // do some magic ...
       }
   }

Extended example
""""""""""""""""

See the advanced example with some configuration
in TypoScript and with the possibility to load the file
(useful if file could not be loaded from autoloader
because it's stored in fileadmin or elsewhere)

::

   plugin.tx_powermail.settings.setup {
       dataProcessors {
           1 {
               # Classname that should be called with method *DataProcessor()
               class = Vendor\Ext\DataProcessor\DoSomethingDataProcessor

               # optional: Add configuration for your PHP
               config {
                   foo = bar

                   fooCObject = TEXT
                   fooCObject.value = do something with this text
               }

               # optional: If file will not be loaded from autoloader, add path and it will be called with require_once
               require = fileadmin/powermail/dataProcessor/DoSomethingDataProcessor.php
           }
       }
   }



Add your php-file again and extend your class with the AbstractDataProcessor from powermail:

::

   <?php
   namespace Vendor\Ext\DataProcessor;

   use In2code\Powermail\Domain\Model\Mail;
   use In2code\Powermail\DataProcessor\AbstractDataProcessor;

   /**
    * Class DoSomethingDataProcessor
    *
    * @package Vendor\Ext\DataProcessor
    */
   class DoSomethingDataProcessor extends AbstractDataProcessor
   {

       /**
        * @var Mail
        */
       protected $mail;

       /**
        * @var array
        */
       protected $configuration;

       /**
        * @var array
        */
       protected $settings;

       /**
        * Will be called always at first
        *
        * @return void
        */
       public function initializeDataProcessor()
       {
       }

       /**
        * Will be called before myDataProcessor()
        *
        * @return void
        */
       public function initializeMyDataProcessor()
       {
       }

       /**
        * MyDataProcessor
        *
        * @return void
        */
       public function doSomethingElseDataProcessor()
       {
           // get value from configuration
           $foo = $this->configuration['foo'];

           // get subject from mail
           $subject = $this->getMail()->getSubject();

           // get a value by markername
           $value = $mail->getAnswersByFieldMarker()['markerName']->getValue();

           // get a value by field uid
           $value = $mail->getAnswersByFieldUid()[123]->getValue();

           // do some more magic ...
       }
   }

Some notices
""""""""""""

* All methods which are ending with "DataProcessor" will be called - e.g. uploadDataProcessor()
* The method initializeDataProcessor() will always be called at first
* Every dataProcessor method could have its own initialize method, which will be called before. Like initializeMyDataProcessor() before myDataProcessor()
* Classes in extensions (if namespace and filename fits) will be automaticly included from TYPO3 autoloader. If you place a single file in fileadmin, use "require" in TypoScript
* Per default 10 and 20 is already in use from powermail itself (SessionDataProcessor, UploadDataProcessor).
