<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\Regex;

class RegexTest extends \PHPUnit\Framework\TestCase
{
    public function regexClassProvider()
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
     */
    public function testCreateNamedRegexClass(Regex $regex)
    {
        $this->assertSame('MyRegex', $regex->name);
        $this->assertSame('Bar', $regex->description);
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testSerializeThrowsIfUnserializableValueIsGiven(Regex $regex)
    {
        $this->expectException(InvariantViolation::class);

        $regex->serialize(
            new class() {
            }
        );
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testSerializeThrowsIfRegexIsNotMatched(Regex $regex)
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage($regex->unmatchedRegexMessage('bar'));

        $regex->serialize('bar');
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testSerializePassesWhenRegexMatches(Regex $regex)
    {
        $serializedResult = $regex->serialize('foo');

        $this->assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testSerializePassesForStringableObject(Regex $regex)
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
     */
    public function testParseValueThrowsIfValueCantBeString(Regex $regex)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/can not be serialized/');

        $regex->parseValue(new class() {
        });
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testParseValueThrowsIfValueDoesNotMatch(Regex $regex)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/did not match the regex/');

        $regex->parseValue('');
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testParseValuePassesOnMatch(Regex $regex)
    {
        $this->assertSame(
            'foo',
            $regex->parseValue('foo')
        );
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testParseLiteralThrowsIfNotString(Regex $regex)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/'.NodeKind::INT.'/');

        $regex->parseLiteral(new IntValueNode([]));
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testParseLiteralThrowsIfValueDoesNotMatch(Regex $regex)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/did not match the regex/');

        $regex->parseLiteral(new StringValueNode(['value' => 'asdf']));
    }

    /**
     * @dataProvider regexClassProvider
     */
    public function testParseLiteralPassesOnMatch(Regex $regex)
    {
        $this->assertSame(
            'foo',
            $regex->parseLiteral(new StringValueNode(['value' => 'foo']))
        );
    }
}
