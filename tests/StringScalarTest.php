<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\StringScalar;

class StringScalarTest extends \PHPUnit\Framework\TestCase
{
    public function stringClassProvider()
    {
        return [
            [
                new class() extends StringScalar {
                    public $name = 'MyStringScalar';

                    public $description = 'Bar';
    
                    /**
                     * Check if the given string is a valid email.
                     *
                     * @param string $stringValue
                     *
                     * @return bool
                     */
                    protected function isValid(string $stringValue): bool
                    {
                        return $stringValue === 'foo';
                    }
                },
            ],
            [
                new class(['name' => 'MyStringScalar', 'description' => 'Bar']) extends StringScalar {
                    /**
                     * Check if the given string is a valid email.
                     *
                     * @param string $stringValue
                     *
                     * @return bool
                     */
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
                    function(string $string){
                        return $string === 'foo';
                    }
                ),
            ],
        ];
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testCreateNamedStringScalarClass(StringScalar $stringScalar)
    {
        $this->assertSame('MyStringScalar', $stringScalar->name);
        $this->assertSame('Bar', $stringScalar->description);
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializeThrowsIfUnserializableValueIsGiven(StringScalar $stringScalar)
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
    public function testSerializeThrowsIfStringScalarIsNotValid(StringScalar $stringScalar)
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('The given string "bar" is not a valid MyStringScalar.');

        $stringScalar->serialize('bar');
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializePassesWhenStringIsValid(StringScalar $stringScalar)
    {
        $serializedResult = $stringScalar->serialize('foo');

        $this->assertSame('foo', $serializedResult);
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testSerializePassesForStringableObject(StringScalar $stringScalar)
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
     */
    public function testParseValueThrowsIfValueCantBeString(StringScalar $stringScalar)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/can not be serialized/');

        $stringScalar->parseValue(new class() {
        });
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseValueThrowsIfValueDoesNotMatch(StringScalar $stringScalar)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string (empty string) is not a valid MyStringScalar.');

        $stringScalar->parseValue('');
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseValuePassesOnMatch(StringScalar $stringScalar)
    {
        $this->assertSame(
            'foo',
            $stringScalar->parseValue('foo')
        );
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralThrowsIfNotString(StringScalar $stringScalar)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessageRegExp('/'.NodeKind::INT.'/');

        $stringScalar->parseLiteral(new IntValueNode([]));
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralThrowsIfValueDoesNotMatch(StringScalar $stringScalar)
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "bar" is not a valid MyStringScalar.');

        $stringScalar->parseLiteral(new StringValueNode(['value' => 'bar']));
    }

    /**
     * @dataProvider stringClassProvider
     */
    public function testParseLiteralPassesOnMatch(StringScalar $stringScalar)
    {
        $this->assertSame(
            'foo',
            $stringScalar->parseLiteral(new StringValueNode(['value' => 'foo']))
        );
    }
}
