<?php

declare(strict_types=1);
namespace In2code\Powermail\Tests\Unit\Fluid\ViewHelper;

use In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ViewHelperPolicyTest
 * @covers \In2code\Powermail\Fluid\ViewHelper\ViewHelperPolicy
 */
class ViewHelperPolicyTest extends UnitTestCase
{
    /**
     * Data Provider for isAllowedReturnsBool()
     *
     * @return \Iterator
     */
    public function isAllowedReturnsBoolDataProvider(): \Iterator
    {
        yield 'empty allowlist allows nothing' => [[], 'f', 'cObject', false];
        yield 'exact match' => [['f:cObject'], 'f', 'cObject', true];
        yield 'exact match does not allow a sibling' => [['f:cObject'], 'f', 'asset.script', false];
        yield 'exact match does not allow another namespace' => [['f:cObject'], 'i', 'phpInfo', false];
        yield 'identifiers are case sensitive' => [['f:cobject'], 'f', 'cObject', false];
        yield 'prefix wildcard' => [['f:format.*'], 'f', 'format.nl2br', true];
        yield 'prefix wildcard does not leak into another group' => [['f:format.*'], 'f', 'asset.script', false];
        yield 'namespace wildcard' => [['f:*'], 'f', 'asset.script', true];
        yield 'namespace wildcard is bound to its namespace' => [['f:*'], 'i', 'phpInfo', false];
        yield 'entry without a colon is dropped' => [['nocolon'], 'f', 'cObject', false];
        yield 'entry without a method is dropped' => [['f:'], 'f', 'cObject', false];
        yield 'entry without a namespace is dropped' => [[':cObject'], 'f', 'cObject', false];
        yield 'surrounding whitespace is ignored' => [['  f:cObject  '], 'f', 'cObject', true];
    }

    /**
     * @param array $identifiers
     * @param string $namespace
     * @param string $method
     * @param bool $expectedResult
     * @dataProvider isAllowedReturnsBoolDataProvider
     * @return void
     * @test
     */
    public function isAllowedReturnsBool($identifiers, $namespace, $method, $expectedResult)
    {
        $policy = ViewHelperPolicy::fromIdentifiers($identifiers);
        self::assertSame($expectedResult, $policy->isAllowed($namespace, $method));
    }

    /**
     * Data Provider for isNamespaceRelevantReturnsBool()
     *
     * @return \Iterator
     */
    public function isNamespaceRelevantReturnsBoolDataProvider(): \Iterator
    {
        yield 'empty allowlist makes every namespace irrelevant' => [[], 'f', false];
        yield 'exact entry makes its namespace relevant' => [['f:cObject'], 'f', true];
        yield 'exact entry does not make another namespace relevant' => [['f:cObject'], 'i', false];
        yield 'prefix entry makes its namespace relevant' => [['f:format.*'], 'f', true];
        yield 'namespace wildcard makes its namespace relevant' => [['f:*'], 'f', true];
    }

    /**
     * @param array $identifiers
     * @param string $namespace
     * @param bool $expectedResult
     * @dataProvider isNamespaceRelevantReturnsBoolDataProvider
     * @return void
     * @test
     */
    public function isNamespaceRelevantReturnsBool($identifiers, $namespace, $expectedResult)
    {
        $policy = ViewHelperPolicy::fromIdentifiers($identifiers);
        self::assertSame($expectedResult, $policy->isNamespaceRelevant($namespace));
    }

    /**
     * @return void
     * @test
     */
    public function getFingerprintIsIndependentOfTheOrderOfTheIdentifiers()
    {
        $first = ViewHelperPolicy::fromIdentifiers(['f:cObject', 'f:translate']);
        $second = ViewHelperPolicy::fromIdentifiers(['f:translate', 'f:cObject']);

        self::assertSame($first->getFingerprint(), $second->getFingerprint());
    }

    /**
     * @return void
     * @test
     */
    public function getFingerprintChangesWithTheAllowlist()
    {
        $first = ViewHelperPolicy::fromIdentifiers(['f:cObject']);
        $second = ViewHelperPolicy::fromIdentifiers(['f:cObject', 'f:translate']);

        self::assertNotSame($first->getFingerprint(), $second->getFingerprint());
    }

    /**
     * The fingerprint becomes part of the identifier of a compiled template via
     * RenderingContext::setControllerAction(), which cuts everything after a dot
     *
     * @return void
     * @test
     */
    public function getFingerprintIsAlphanumeric()
    {
        $policy = ViewHelperPolicy::fromIdentifiers(['f:format.*', 'f:cObject']);

        self::assertMatchesRegularExpression('/^[a-z0-9]+$/', $policy->getFingerprint());
    }

    /**
     * @return void
     * @test
     */
    public function getIdentifiersReturnsNormalizedEntries()
    {
        $policy = ViewHelperPolicy::fromIdentifiers(['f:format.*', 'f:cObject', 'vh:*', 'garbage']);

        self::assertSame(['f:cObject', 'f:format.*', 'vh:*'], $policy->getIdentifiers());
    }

    /**
     * @return void
     * @test
     */
    public function hasUnexpandableEntriesIsFalseForExactIdentifiersOnly()
    {
        self::assertFalse(ViewHelperPolicy::fromIdentifiers(['f:cObject'])->hasUnexpandableEntries());
        self::assertTrue(ViewHelperPolicy::fromIdentifiers(['f:format.*'])->hasUnexpandableEntries());
        self::assertTrue(ViewHelperPolicy::fromIdentifiers(['f:*'])->hasUnexpandableEntries());
    }

    /**
     * @return void
     * @test
     */
    public function isEmptyIsTrueWithoutUsableIdentifiers()
    {
        self::assertTrue(ViewHelperPolicy::fromIdentifiers([])->isEmpty());
        self::assertTrue(ViewHelperPolicy::fromIdentifiers(['garbage', 'f:'])->isEmpty());
        self::assertFalse(ViewHelperPolicy::fromIdentifiers(['f:cObject'])->isEmpty());
    }
}
