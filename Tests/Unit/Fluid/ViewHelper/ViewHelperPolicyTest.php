<?php
namespace In2code\Powermail\Tests\Unit\Fluid\ViewHelper;

use In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ViewHelperPolicyTest
 * @coversDefaultClass \In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy
 */
class ViewHelperPolicyTest extends UnitTestCase
{
    /**
     * Dataprovider isAllowedReturnsBool()
     *
     * @return array
     */
    public function isAllowedReturnsBoolDataProvider()
    {
        return [
            'exact match is allowed' => [
                ['f:cObject'],
                'f',
                'cObject',
                true,
            ],
            'exact match is case sensitive' => [
                ['f:cObject'],
                'f',
                'cobject',
                false,
            ],
            'other viewhelper is not allowed' => [
                ['f:cObject'],
                'f',
                'format.raw',
                false,
            ],
            'prefix match is allowed' => [
                ['f:format.*'],
                'f',
                'format.raw',
                true,
            ],
            'prefix does not match a different method' => [
                ['f:format.*'],
                'f',
                'cObject',
                false,
            ],
            'namespace wildcard allows everything in the namespace' => [
                ['f:*'],
                'f',
                'anything.here',
                true,
            ],
            'empty allowlist allows nothing' => [
                [],
                'f',
                'cObject',
                false,
            ],
        ];
    }

    /**
     * @param string[] $identifiers
     * @param string $namespace
     * @param string $method
     * @param bool $expectedResult
     * @dataProvider isAllowedReturnsBoolDataProvider
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::isAllowed
     */
    public function isAllowedReturnsBool(array $identifiers, $namespace, $method, $expectedResult)
    {
        $policy = ViewHelperPolicy::fromIdentifiers($identifiers);
        $this->assertSame($expectedResult, $policy->isAllowed($namespace, $method));
    }

    /**
     * Dataprovider isNamespaceRelevantReturnsBool()
     *
     * @return array
     */
    public function isNamespaceRelevantReturnsBoolDataProvider()
    {
        return [
            'exact entry makes namespace relevant' => [['f:cObject'], 'f', true],
            'prefix entry makes namespace relevant' => [['f:format.*'], 'f', true],
            'wildcard entry makes namespace relevant' => [['f:*'], 'f', true],
            'unrelated namespace is not relevant' => [['f:cObject'], 'i', false],
            'empty allowlist has no relevant namespace' => [[], 'f', false],
        ];
    }

    /**
     * @param string[] $identifiers
     * @param string $namespace
     * @param bool $expectedResult
     * @dataProvider isNamespaceRelevantReturnsBoolDataProvider
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::isNamespaceRelevant
     */
    public function isNamespaceRelevantReturnsBool(array $identifiers, $namespace, $expectedResult)
    {
        $policy = ViewHelperPolicy::fromIdentifiers($identifiers);
        $this->assertSame($expectedResult, $policy->isNamespaceRelevant($namespace));
    }

    /**
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::isEmpty
     * @covers ::hasUnexpandableEntries
     */
    public function emptyAllowlistIsEmptyAndExpandable()
    {
        $policy = ViewHelperPolicy::fromIdentifiers([]);
        $this->assertTrue($policy->isEmpty());
        $this->assertFalse($policy->hasUnexpandableEntries());
    }

    /**
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::hasUnexpandableEntries
     */
    public function wildcardEntriesAreUnexpandable()
    {
        $this->assertTrue(ViewHelperPolicy::fromIdentifiers(['f:*'])->hasUnexpandableEntries());
        $this->assertTrue(ViewHelperPolicy::fromIdentifiers(['f:format.*'])->hasUnexpandableEntries());
        $this->assertFalse(ViewHelperPolicy::fromIdentifiers(['f:cObject'])->hasUnexpandableEntries());
    }

    /**
     * Garbage entries without a namespace separator are ignored instead of breaking the policy.
     *
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::isEmpty
     * @covers ::getIdentifiers
     */
    public function malformedIdentifiersAreIgnored()
    {
        $policy = ViewHelperPolicy::fromIdentifiers(['nonsense', 'f:', ':method', '']);
        $this->assertTrue($policy->isEmpty());
        $this->assertSame([], $policy->getIdentifiers());
    }

    /**
     * The fingerprint is stable for the same allowlist regardless of order, and differs for a
     * different allowlist - it is what invalidates compiled templates when the allowlist changes.
     *
     * @return void
     * @test
     * @covers ::fromIdentifiers
     * @covers ::getFingerprint
     * @covers ::getIdentifiers
     */
    public function fingerprintIsStableAndOrderIndependent()
    {
        $a = ViewHelperPolicy::fromIdentifiers(['f:cObject', 'f:format.*']);
        $b = ViewHelperPolicy::fromIdentifiers(['f:format.*', 'f:cObject']);
        $c = ViewHelperPolicy::fromIdentifiers(['f:cObject']);

        $this->assertSame($a->getFingerprint(), $b->getFingerprint());
        $this->assertNotSame($a->getFingerprint(), $c->getFingerprint());
    }
}
