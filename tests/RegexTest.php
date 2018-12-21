<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    /**
     * Provide the same Regex class using the different methods for instantiation.
     *
     * @return array[]
     */
    public function regexClassProvider(): array
    {
        return [
            [
                new class() extends Regex {
                    public $name = 'MyRegex';

                    public $description = 'Bar';

                    /**
                     * Return the Regex that the values are validated against.
                     *
                     * Must be a valid
                     *
                     * @return string
                     */
                    public static function regex(): string
                    {
                        return '/foo/';
                    }
                },
            ],
            [
                new class(['name' => 'MyRegex', 'description' => 'Bar']) extends Regex {
                    /**
                     * Return the Regex that the values are validated against.
                     *
                     * Must be a valid
                     *
                     * @return string
                     */
                    public static function regex(): string
                    {
                        return '/foo/';
                    }
                },
            ],
            [
                new MyRegex(),
            ],
            [
                Regex::make('MyRegex', 'Bar', '/foo/'),
            ],
        ];
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     */
    public function testCreateNamedRegexClass(Regex $regex): void
    {
        $this->assertSame('MyRegex', $regex->name);
        $this->assertSame('Bar', $regex->description);
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     */
    public function testSerializeThrowsIfUnserializableValueIsGiven(Regex $regex): void
    {
        $this->expectException(InvariantViolation::class);

        $regex->serialize(
            new class() {
            }
        );
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     */
    public function testSerializeThrowsIfRegexIsNotMatched(Regex $regex): void
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage($regex->unmatchedRegexMessage('bar'));

        $regex->serialize('bar');
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     */
    public function testSerializePassesWhenRegexMatches(Regex $regex): void
    {
        $serializedResult = $regex->serialize('foo');

        $this->assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     */
    public function testSerializePassesForStringableObject(Regex $regex): void
    {
        $serializedResult = $regex->serialize(
            new class() {
                public function __toString(): string
                {
                    return 'Contains foo right?';
                }
            }
        );

        $this->assertSame('Contains foo right?', $serializedResult);
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseValueThrowsIfValueCantBeString(Regex $regex): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/can not be serialized/');

        $regex->parseValue(new class() {
        });
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseValueThrowsIfValueDoesNotMatch(Regex $regex): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/did not match the regex/');

        $regex->parseValue('');
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseValuePassesOnMatch(Regex $regex): void
    {
        $this->assertSame(
            'foo',
            $regex->parseValue('foo')
        );
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseLiteralThrowsIfNotString(Regex $regex): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/'.NodeKind::INT.'/');

        $regex->parseLiteral(new IntValueNode([]));
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseLiteralThrowsIfValueDoesNotMatch(Regex $regex): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/did not match the regex/');

        $regex->parseLiteral(new StringValueNode(['value' => 'asdf']));
    }

    /**
     * @dataProvider regexClassProvider
     *
     * @param Regex $regex
     *
     * @throws Error
     */
    public function testParseLiteralPassesOnMatch(Regex $regex): void
    {
        $this->assertSame(
            'foo',
            $regex->parseLiteral(new StringValueNode(['value' => 'foo']))
        );
    }
}
