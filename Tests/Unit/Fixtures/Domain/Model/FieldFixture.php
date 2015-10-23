<?php
namespace In2code\Powermail\Tests\Fixtures\Domain\Model;

use In2code\Powermail\Domain\Model\Field;

/**
 * Fixture class for mocking getPagesTSconfig
 */
class FieldFixture extends Field
{

    /**
     * Extend dataType with TSConfig
     *
     * @param string $fieldType
     * @param array $types
     * @return array
     */
    protected function extendTypeArrayWithTypoScriptTypes($fieldType, array $types)
    {
        return $types;
    }
}
