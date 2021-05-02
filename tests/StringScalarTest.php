<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\StringScalar;
use PHPUnit\Framework\TestCase;

class StringScalarTest extends TestCase
{
    /**
     * Provide a StringScalar class instantiated in different ways.
     *
     * @return array<int, array<int, StringScalar>>
     */
    public function stringClassProvider(): array
    {
        return [
            [
                new class() extends StringScalar {
                    public $name = 'MyStringScalar';

                    public $description = 'Bar';

                    protected function isValid(string $stringValue): bool
                    {
                        return $stringValue === 'foo';
                    }
                },
            ],
            [
                new class(['name' => 'MyStringScalar', 'description' => 'Bar']) extends StringScalar {
                    protected function isValid(string $stringValue): bool
                    {
                        return $stringValue === 'foo';
                    }
                },
            ],
            [
                new MyStringScalar(),
            ],
            [
                StringScalar::make(
                    'MyStringScalar',
                    'Bar',
                    function (string $string) {
                        return $string === 'foo';
                    }
                ),
            ],
        ];
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testCreateNamedStringScalarClass(StringScalar $stringScalar): void
    {
        $this->assertSame('MyStringScalar', $stringScalar->name);
        $this->assertSame('Bar', $stringScalar->description);
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializeThrowsIfUnserializableValueIsGiven(StringScalar $stringScalar): void
    {
        $this->expectException(InvariantViolation::class);

        $stringScalar->serialize(
            new class() {
            }
        );
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializeThrowsIfStringScalarIsNotValid(StringScalar $stringScalar): void
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('The given string "bar" is not a valid MyStringScalar.');

        $stringScalar->serialize('bar');
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializePassesWhenStringIsValid(StringScalar $stringScalar): void
    {
        $serializedResult = $stringScalar->serialize('foo');

        $this->assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializePassesForStringableObject(StringScalar $stringScalar): void
    {
        $serializedResult = $stringScalar->serialize(
            new class() {
                public function __toString(): string
                {
                    return 'foo';
                }
            }
        );

        $this->assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseValueThrowsIfValueCantBeString(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches(/** @lang RegExp */'/can not be serialized/');

        $stringScalar->parseValue(new class() {
        });
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseValueThrowsIfValueDoesNotMatch(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string (empty string) is not a valid MyStringScalar.');

        $stringScalar->parseValue('');
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseValuePassesOnMatch(StringScalar $stringScalar): void
    {
        $this->assertSame(
            'foo',
            $stringScalar->parseValue('foo')
        );
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseLiteralThrowsIfNotString(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches(/** @lang RegExp */'/'.NodeKind::INT.'/');

        $stringScalar->parseLiteral(new IntValueNode([]));
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseLiteralThrowsIfValueDoesNotMatch(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "bar" is not a valid MyStringScalar.');

        $stringScalar->parseLiteral(new StringValueNode(['value' => 'bar']));
    }

    /**
     * @dataProvider stringClassProvider
     *
     * @throws Error
     */
    public function testParseLiteralPassesOnMatch(StringScalar $stringScalar): void
    {
        $this->assertSame(
            'foo',
            $stringScalar->parseLiteral(new StringValueNode(['value' => 'foo']))
        );
    }
}
