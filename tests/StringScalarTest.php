<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\StringScalar;
use PHPUnit\Framework\TestCase;

final class StringScalarTest extends TestCase
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
                    public string $name = 'MyStringScalar';

                    public ?string $description = 'Bar';

                    protected function isValid(string $stringValue): bool
                    {
                        return 'foo' === $stringValue;
                    }
                },
            ],
            [
                new class(['name' => 'MyStringScalar', 'description' => 'Bar']) extends StringScalar {
                    protected function isValid(string $stringValue): bool
                    {
                        return 'foo' === $stringValue;
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
                        return 'foo' === $string;
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
        self::assertSame('MyStringScalar', $stringScalar->name);
        self::assertSame('Bar', $stringScalar->description);
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

        self::assertSame('foo', $serializedResult);
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

        self::assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseValueThrowsIfValueCantBeString(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches(/** @lang RegExp */ '/can not be coerced to a string/');

        $stringScalar->parseValue(new class() {
        });
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseValueThrowsIfValueDoesNotMatch(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string (empty string) is not a valid MyStringScalar.');

        $stringScalar->parseValue('');
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseValuePassesOnMatch(StringScalar $stringScalar): void
    {
        self::assertSame(
            'foo',
            $stringScalar->parseValue('foo')
        );
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralThrowsIfNotString(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches(/** @lang RegExp */ '/' . NodeKind::INT . '/');

        $stringScalar->parseLiteral(new IntValueNode([]));
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralThrowsIfValueDoesNotMatch(StringScalar $stringScalar): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "bar" is not a valid MyStringScalar.');

        $stringScalar->parseLiteral(new StringValueNode(['value' => 'bar']));
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralPassesOnMatch(StringScalar $stringScalar): void
    {
        self::assertSame(
            'foo',
            $stringScalar->parseLiteral(new StringValueNode(['value' => 'foo']))
        );
    }
}
