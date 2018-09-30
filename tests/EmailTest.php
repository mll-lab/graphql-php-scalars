<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\Email;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function testSerializeThrowsIfUnserializableValueIsGiven()
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessageRegExp('/^The given value .* can not be serialized\./');

        (new Email())->serialize(
            new class() {
            }
        );
    }

    public function testSerializeThrowsIfEmailIsInvalid()
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->serialize('foo');
    }

    public function testSerializePassesWhenEmailIsInvalid()
    {
        $serializedResult = (new Email())->serialize('foo@bar');

        $this->assertSame('foo@bar', $serializedResult);
    }

    public function testParseValueThrowsIfEmailIsInvalid()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->parseValue('foo');
    }

    public function testParseValuePassesIfEmailIsValid()
    {
        $this->assertSame(
            'foo@bar',
            (new Email())->parseValue('foo@bar')
        );
    }

    public function testParseLiteralThrowsIfNotValidEmail()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->parseLiteral(new StringValueNode(['value' => 'foo']));
    }

    public function testParseLiteralPassesIfEmailIsValid()
    {
        $this->assertSame(
            'foo@bar',
            (new Email())->parseLiteral(new StringValueNode(['value' => 'foo@bar']))
        );
    }
}
