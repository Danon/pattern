<?php
namespace Test\Integration\TRegx\CleanRegex\Internal\Prepared\PrepareFacade\bind;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Internal\Prepared\Parser\BindingParser;
use TRegx\CleanRegex\Internal\Prepared\PrepareFacade;

class PrepareFacadeTest extends TestCase
{
    /**
     * @test
     * @dataProvider validInput
     * @param string $input
     * @param array $values
     * @param string $expected
     */
    public function shouldBind(string $input, array $values, string $expected)
    {
        // given
        $facade = new PrepareFacade(new BindingParser($input, $values), false);

        // when
        $pattern = $facade->getPattern();

        // then
        $this->assertEquals($expected, $pattern);
    }

    public function validInput()
    {
        return [
            [
                // find by @, quote regexp parenthesis
                '(I|We) would like to match: @input (and|or) @input_2',
                [
                    'input' => 'User (input)',
                    'input_2' => 'User (input_2)',
                ],
                '/(I|We) would like to match: User \(input\) (and|or) User \(input_2\)/'
            ],
            [
                // find by ``, quote regexp parenthesis
                '(I|We) would like to match: `input` (and|or) `input_2`',
                [
                    'input' => 'User (input)',
                    'input_2' => 'User (input_2)',
                ],
                '/(I|We) would like to match: User \(input\) (and|or) User \(input_2\)/'
            ],
            [
                // find placeholders without whitespace
                '(I|We) would like to match: @input@input_2',
                [
                    'input' => 'User (input)',
                    'input_2' => 'User (input_2)',
                ],
                '/(I|We) would like to match: User \(input\)User \(input_2\)/'
            ],
            [
                // quote delimiters
                'With delimiters / #@input',
                [
                    'input' => 'Using / delimiters and %',
                ],
                '%With delimiters / #Using / delimiters and \%%'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider ignoredInputs
     * @param string $input
     * @param array $values
     * @param string $expected
     */
    public function shouldIgnorePlaceholders(string $input, array $values, string $expected)
    {
        // given
        $facade = new PrepareFacade(new BindingParser($input, $values), false);

        // when
        $pattern = $facade->getPattern();

        // then
        $this->assertEquals($expected, $pattern);
    }

    public function ignoredInputs(): array
    {
        return [
            [
                // Should allow for inserting @ placeholders again
                '(I|We) would like to match: @input (and|or) @input2',
                [
                    'input' => '@input',
                    'input2' => '@input2',
                ],
                '/(I|We) would like to match: @input (and|or) @input2/',
            ],
            [
                // Should allow for inserting `` placeholders again
                '(I|We) would like to match: `input` (and|or) `input2`',
                [
                    'input' => '`input`',
                    'input2' => '`input2`',
                ],
                '/(I|We) would like to match: `input` (and|or) `input2`/',
            ],
            [
                // Should ignore @@ placeholders
                '(I|We) would like to match: @input (and|or) @input_2@',
                [
                    0 => 'input',
                    1 => 'input_2',
                ],
                '/(I|We) would like to match: @input (and|or) @input_2@/',
            ],
            [
                // Should allow no placeholders
                '(I|We) would like to match',
                [],
                '/(I|We) would like to match/',
            ],
            // Corner values
            ['//', [], '#//#'],
            ['//mi', [], '#//mi#'],
            ['', [], '//'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidInputs
     * @param string $input
     * @param array $values
     * @param string $message
     */
    public function shouldThrow_onInvalidInput(string $input, array $values, string $message)
    {
        // given
        $facade = new PrepareFacade(new BindingParser($input, $values), false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        // when
        $facade->getPattern();
    }

    public function invalidInputs(): array
    {
        return [
            [
                '@input2',
                [],
                "Could not find a corresponding value for placeholder 'input2'",
            ],
            [
                '@input and @input3',
                ['input'],
                "Could not find a corresponding value for placeholder 'input3'",
            ],
            [
                '@input',
                [
                    'input',
                    'input2',
                ],
                "Could not find a corresponding placeholder for name 'input2'",
            ],
            [
                '@input',
                [
                    'input' => 'some value',
                    0 => 'input',
                ],
                "Name 'input' is used more than once (as a key or as ignored value)",
            ],
            [
                '@input',
                [
                    'input' => 4,
                ],
                "Invalid bound value for name 'input'. Expected string, but integer (4) given",
            ],
            [
                '@input',
                [
                    'input' => [],
                ],
                "Invalid bound value for name 'input'. Expected string, but array (0) given",
            ],
            [
                'well',
                [
                    0 => 21,
                ],
                'Invalid bound parameters. Expected string, but integer (21) given',
            ],
            [
                'well',
                [
                    '(asd)' => 21,
                ],
                "Invalid name '(asd)'. Expected a string consisting only of alphanumeric characters and an underscore [a-zA-Z0-9_]",
            ],
        ];
    }
}
