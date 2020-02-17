<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\Domain\Model;

use In2code\Powermail\Domain\Model\Field;

/**
 * Fixture class for mocking getPagesTSconfig
 */
class FieldFixture extends Field
{

    /**
     * Extend dataType with TSConfig
     *
     * @param array $types
     * @return array
     */
    protected function extendTypeArrayWithTypoScriptTypes(array $types): array
    {
        return $types;
    }
}
