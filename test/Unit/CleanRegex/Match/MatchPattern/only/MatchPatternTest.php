<?php
namespace Test\Unit\TRegx\CleanRegex\Match\MatchPattern\only;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Test\PhpunitPolyfill;
use Test\Utils\PhpVersionDependent;
use TRegx\CleanRegex\Internal\InternalPattern;
use TRegx\CleanRegex\Match\MatchPattern;
use TRegx\SafeRegex\Exception\CompilePregException;

class MatchPatternTest extends TestCase
{
    use PhpunitPolyfill;

    /**
     * @test
     */
    public function shouldGetAll()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('([A-Z])?[a-z]+'), 'Nice matching pattern');

        // when
        $only = $pattern->only(2);

        // then
        $this->assertEquals(['Nice', 'matching'], $only);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArray_onNoMatches()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('([A-Z])?[a-z]+'), 'NOT MATCHING');

        // when
        $only = $pattern->only(2);

        // then
        $this->assertEmpty($only, 'Failed asserting that only() returned an empty array');
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArray_onNoMatches_onlyOne()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('([A-Z])?[a-z]+'), 'NOT MATCHING');

        // when
        $only = $pattern->only(1);

        // then
        $this->assertEmpty($only, 'Failed asserting that only() returned an empty array');
    }

    /**
     * @test
     */
    public function shouldThrow_onNegativeLimit()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('([A-Z])?[a-z]+'), 'NOT MATCHING');

        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Negative limit: -2');

        // when
        $pattern->only(-2);
    }

    /**
     * @test
     */
    public function shouldGetOne_withPregMatch()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('(?<group>[A-Z])?(?<group2>[a-z]+)'), 'Nice matching pattern');

        // when
        $only = $pattern->only(1);

        // then
        $this->assertEquals(['Nice'], $only);
    }

    /**
     * @test
     */
    public function shouldGetNone()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('(?<group>[A-Z])?(?<group2>[a-z]+)'), 'Nice matching pattern');

        // when
        $only = $pattern->only(0);

        // then
        $this->assertEmpty($only);
    }

    /**
     * @test
     */
    public function shouldValidatePattern_onOnly0()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard('invalid)'), 'Nice matching pattern');

        // then
        $this->expectException(CompilePregException::class);
        $this->expectExceptionMessageMatches(PhpVersionDependent::getUnmatchedParenthesisMessage(7));

        // when
        $pattern->only(0);
    }
}
