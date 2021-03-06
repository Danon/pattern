<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\isInt;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\Details\Detail;

class MatchDetailTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeInt()
    {
        // given
        $result = pattern('(?<name>\d+)')->match('11')->first(function (Detail $detail) {
            // when
            return $detail->isInt();
        });

        // then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldPseudoInteger_notBeInt_becausePhpSucks()
    {
        // given
        $result = pattern('(.*)', 's')->match('1e3')->first(function (Detail $detail) {
            // when
            return $detail->isInt();
        });

        // then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function shouldNotBeInteger()
    {
        // given
        pattern('(?<name>Foo)')->match('Foo')->first(function (Detail $detail) {
            // when
            $result = $detail->isInt();

            // then
            $this->assertFalse($result);
        });
    }
}
