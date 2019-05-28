.. include:: Images.txt

Add own global serverside validators
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A global validator is something that could normally not be selected by an editor.

1. Use a validator class
""""""""""""""""""""""""

Introduction
~~~~~~~~~~~~

Let's say you want to easily add your own php function to validate user inputs,
that should be called after a user submits a form, but of course before the createAction() is called.

Small example
~~~~~~~~~~~~~

Just define which classes should be used. Method validate() will be called:

::

   plugin.tx_powermail.settings.setup {
       validators {
           1 {
               class = Vendor\Ext\Domain\Model\DoSomethingValidator
           }
       }
   }


Add a php-file and extend your class with the AbstractValidator from powermail:

::

   <?php
   namespace Vendor\Ext\Domain\Model;

   use In2code\Powermail\Domain\Validator\AbstractValidator;
   use TYPO3\CMS\Extbase\Error\Error;
   use TYPO3\CMS\Extbase\Error\Result;

   /**
    * Class DoSomethingValidator
    */
   class DoSomethingValidator extends AbstractValidator
   {

       /**
        * validate
        *
        * @param Mail $mail
        * @return Result
        */
       public function validate($mail)
       {
           // throw error
           $result = new Result();
           $result->addError(new Error('Error', 'markername'));
           return $result;
       }
   }

Extended example
~~~~~~~~~~~~~~~~

See the advanced example with some configuration
in TypoScript and with the possibility to load the file
(useful if file could not be loaded from autoloader
because it's stored in fileadmin or elsewhere)

::

   plugin.tx_powermail.settings.setup {
       validators {
           1 {
                # Classname that should be called with method *Validator()
                class = Vendor\Ext\Domain\Model\AlexValidator

                # optional: Add configuration for your PHP
                config {
                    allowedValues = alex, alexander
                    form = 210
                }

                # optional: If file will not be loaded from autoloader, add path and it will be called with require_once
                require = fileadmin/powermail/validator/AlexValidator.php
           }
       }
   }

Add your php-file again and extend your class with the AbstractValidator from powermail:

::

    <?php
    namespace Vendor\Ext\Domain\Model;

    use In2code\Powermail\Domain\Model\Mail;
    use In2code\Powermail\Domain\Validator\AbstractValidator;
    use TYPO3\CMS\Core\Utility\GeneralUtility;
    use TYPO3\CMS\Extbase\Error\Result;
    use TYPO3\CMS\Extbase\Error\Error;

    /**
     * Class AlexValidator
     *
     * @package Vendor\Ext\Validator
     */
    class AlexValidator extends AbstractValidator
    {

        /**
         * Field to check - select by {markername}
         *
         * @var string
         */
        protected $fieldMarker = 'firstname';

        /**
         * Validator configuration
         *
         * @var array
         */
        protected $configuration = [];

        /**
         * Check if value in Firstname-Field is allowed
         *
         * @param Mail $mail
         * @return Result
         */
        public function validate($mail)
        {
            $result = new Result();
            if ((int)$this->configuration['form'] === $mail->getForm()->getUid()) {
                foreach ($mail->getAnswers() as $answer) {
                    if ($answer->getField()->getMarker() === $this->fieldMarker && !$this->isAllowedValue($answer->getValue())) {
                        $result->addError(new Error('Firstname must be "Alexander"', $this->fieldMarker));
                    }
                }
            }
            return $result;
        }

        /**
         * Check if this value is allowed
         *
         * @return bool
         */
        protected function isAllowedValue($value)
        {
            $allowedValues = GeneralUtility::trimExplode(',', $this->configuration['allowedValues'], true);
            return in_array(strtolower($value), $allowedValues);
        }
    }

|developer_new_validation2|

2. Use a slot with CustomValidator
""""""""""""""""""""""""""""""""""

Introduction
~~~~~~~~~~~~

You can also use the CustomValidator (used twice in powermail
FormsController: confirmationAction and createAction) to write your
own field validation after a form submit.

The customValidator is located at
powermail/Classes/Domain/Validator/CustomValidator.php. A signalSlot
Dispatcher within the class waits for your extension.


SignalSlot in CustomValidator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Class:
      Signal Class Name
   :Name:
      Signal Name
   :File:
      Located in File
   :Method:
      Located in Method

 - :Class:
      \\In2code\\Powermail\\Domain\\Validator\\CustomValidator
   :Name:
      isValid
   :File:
      CustomValidator.php
   :Method:
      isValid()

Call the Custom Validator from your Extension
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add a new extension (example key powermail_extend).

Example ext_localconf.php:

::

   $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
       \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
   );
   $signalSlotDispatcher->connect(
        'In2code\Powermail\Domain\Validator\CustomValidator',
        'isValid',
        'Vendor\Extkey\Domain\Validator\CustomValidator',
        'addInformation',
        FALSE
   );

Example file:

::

   class \Vendor\Extkey\Domain\Validator\CustomValidator
   {
           public function addInformation($params, $obj)
           {
                   // $field failed - set error
                   $obj->setErrorAndMessage($field, 'error message');
           }
   }




Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
