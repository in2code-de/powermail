<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * ForeignValidator
 */
class ForeignValidator extends AbstractValidator
{
    protected string $validatorInterface = ValidatorInterface::class;

    /**
     * Include foreign validators
     *
     * @param Mail $mail
     * @throws ClassDoesNotExistException
     * @throws InterfaceNotImplementedException
     */
    protected function isValid($mail): void
    {
        foreach ((array)($this->settings['validators'] ?? []) as $validatorConf) {
            $this->loadFile($validatorConf);
            if (!class_exists($validatorConf['class'])) {
                throw new ClassDoesNotExistException(
                    'Class ' . $validatorConf['class'] . ' does not exists - check if file was loaded with autoloader',
                    1578609804
                );
            }

            if (is_subclass_of($validatorConf['class'], $this->validatorInterface)) {
                /** @var AbstractValidator $validator */
                $validator = GeneralUtility::makeInstance($validatorConf['class']);
                $validator->setRequest($this->getRequest());
                $validator->setConfiguration($validatorConf['config'] ?? []);
                $validator->initFlexform();
                $validator->initialize();
                $this->addErrors($validator->validate($mail));
            } else {
                throw new InterfaceNotImplementedException(
                    'Validator does not implement ' . $this->validatorInterface,
                    1578609814
                );
            }
        }
    }

    /**
     * Add errors and set validstate to false
     */
    protected function addErrors(Result $result): void
    {
        $errors = $result->getErrors();
        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->addError($error->getMessage(), $error->getCode(), $error->getArguments(), $error->getTitle());
            }

            $this->setValidState(false);
        }
    }

    protected function loadFile(array $validatorConf): void
    {
        $pathAndFile = $validatorConf['require'] ?? '';
        if ($pathAndFile !== '' && file_exists($pathAndFile)) {
            require_once($pathAndFile);
        }
    }
}
