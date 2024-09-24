<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\Error;
use PHPUnit\Framework\TestCase;

final class IntRangeTest extends TestCase
{
    public function testSerializeThrowsIfString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: "12".');

        (new UpToADozen())->serialize('12');
    }

    public function testSerializeThrowsIfFloat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: 5.43.');

        (new UpToADozen())->serialize(5.43);
    }

    public function testSerializeThrowsIfTooHigh(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: 13.');

        (new UpToADozen())->serialize(13);
    }

    public function testSerializeThrowsIfZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: 0.');

        (new UpToADozen())->serialize(0);
    }

    public function testSerializeThrowsIfTooLow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in range 1-12: -1.');

        (new UpToADozen())->serialize(-1);
    }

    public function testSerializePassesWithHighest(): void
    {
        self::assertSame(
            12,
            (new UpToADozen())->serialize(12)
        );
    }

    public function testSerializePassesWithLowest(): void
    {
        self::assertSame(
            1,
            (new UpToADozen())->serialize(1)
        );
    }

    public function testParseValueThrowsIfTooHigh(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Value not in range 1-12: 13.');

        (new UpToADozen())->parseValue(13);
    }

    public function testParseValueThrowsIfZero(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Value not in range 1-12: 0.');

        (new UpToADozen())->parseValue(0);
    }

    public function testParseValueThrowsIfTooLow(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Value not in range 1-12: -1.');

        (new UpToADozen())->parseValue(-1);
    }

    public function testParseValuePassesWithHighest(): void
    {
        self::assertSame(
            12,
            (new UpToADozen())->parseValue(12)
        );
    }

    public function testParseValuePassesWithLowest(): void
    {
        self::assertSame(
            1,
            (new UpToADozen())->parseValue(1)
        );
    }
}
