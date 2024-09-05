<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\Error;
use PHPUnit\Framework\TestCase;

final class IntRangeTest extends TestCase
{
    public function testSerializeThrowsIfNotAnInt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: "12".');

        (new UpToADozen())->serialize('12');
    }

    public function testSerializeThrowsIfInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: 13.');

        (new UpToADozen())->serialize(13);
    }

    public function testSerializePassesWhenValid(): void
    {
        $serializedResult = (new UpToADozen())->serialize(12);

        self::assertSame(12, $serializedResult);
    }

    public function testParseValueThrowsIfInvalid(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Value not in range 1-12: 13.');

        (new UpToADozen())->parseValue(13);
    }

    public function testParseValuePassesIfValid(): void
    {
        self::assertSame(
            12,
            (new UpToADozen())->parseValue(12)
        );
    }
}
