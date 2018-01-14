<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * ForeignValidator
 */
class ForeignValidator extends AbstractValidator
{

    /**
     * @var string
     */
    protected $validatorInterface = 'In2code\Powermail\Domain\Validator\ValidatorInterface';

    /**
     * Include foreign validators
     *
     * @param Mail $mail
     * @return bool
     * @throws \Exception
     */
    public function isValid($mail)
    {
        foreach ((array)$this->settings['validators'] as $validatorConf) {
            $this->loadFile($validatorConf['require']);
            if (!class_exists($validatorConf['class'])) {
                throw new \UnexpectedValueException(
                    'Class ' . $validatorConf['class'] . ' does not exists - check if file was loaded with autoloader'
                );
            }
            if (is_subclass_of($validatorConf['class'], $this->validatorInterface)) {
                /** @var AbstractValidator $validator */
                $validator = ObjectUtility::getObjectManager()->get($validatorConf['class']);
                $validator->setConfiguration((array)$validatorConf['config']);
                $validator->initialize();
                /** @var Result $result */
                $this->addErrors($validator->validate($mail));
            } else {
                throw new \UnexpectedValueException('Validator does not implement ' . $this->validatorInterface);
            }
        }

        return $this->isValidState();
    }

    /**
     * Add errors and set validstate to false
     *
     * @param Result $result
     * @return void
     */
    protected function addErrors(Result $result)
    {
        $errors = $result->getErrors();
        if (!empty($errors)) {
            /** @var Error $error */
            foreach ($errors as $error) {
                $this->addError($error->getMessage(), $error->getCode());
            }
            $this->setValidState(false);
        }
    }

    /**
     * @param string $pathAndFile
     * @return void
     */
    protected function loadFile($pathAndFile)
    {
        if (!empty($pathAndFile) && file_exists($pathAndFile)) {
            require_once($pathAndFile);
        }
    }
}
