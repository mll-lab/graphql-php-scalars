<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\InvariantViolation;
use MLL\GraphQLScalars\BigInt;
use PHPUnit\Framework\TestCase;

final class BigIntTest extends TestCase
{
    public function testSerializeThrowsIfBigIntIsInvalid(): void
    {
        $bigInt = new BigInt();

        $this->expectExceptionObject(new InvariantViolation('The given value "foo" did not match the regex /\d+/.'));
        $bigInt->serialize('foo');
    }

    public function testSerializePassesWhenBigIntIsValid(): void
    {
        $serializedResult = (new BigInt())->serialize(10000000000000);

        self::assertSame('10000000000000', $serializedResult);
    }

    public function testSerializePassesWhenBigIntIsValidAsString(): void
    {
        $serializedResult = (new BigInt())->serialize('10000000000000');

        self::assertSame('10000000000000', $serializedResult);
    }

    public function testParseBigIntIsValid(): void
    {
        $parsedResult = (new BigInt())->parseValue(10000000000000);

        self::assertSame('10000000000000', $parsedResult);
    }
}
