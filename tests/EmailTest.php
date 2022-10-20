<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testSerializeThrowsIfUnserializableValueIsGiven(): void
    {
        $this->expectExceptionObject(new InvariantViolation(
            'The given value can not be coerced to a string: object.'
        ));

        (new Email())->serialize(
            new class() {
            }
        );
    }

    public function testSerializeThrowsIfEmailIsInvalid(): void
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->serialize('foo');
    }

    public function testSerializePassesWhenEmailIsValid(): void
    {
        $serializedResult = (new Email())->serialize('foo@bar');

        self::assertSame('foo@bar', $serializedResult);
    }

    public function testParseValueThrowsIfEmailIsInvalid(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->parseValue('foo');
    }

    public function testParseValuePassesIfEmailIsValid(): void
    {
        self::assertSame(
            'foo@bar',
            (new Email())->parseValue('foo@bar')
        );
    }

    public function testParseLiteralThrowsIfNotValidEmail(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('The given string "foo" is not a valid Email.');

        (new Email())->parseLiteral(new StringValueNode(['value' => 'foo']));
    }

    public function testParseLiteralPassesIfEmailIsValid(): void
    {
        self::assertSame(
            'foo@bar',
            (new Email())->parseLiteral(new StringValueNode(['value' => 'foo@bar']))
        );
    }
}
