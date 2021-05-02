<?php declare(strict_types=1);

namespace Tests;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\JSON;
use PHPUnit\Framework\TestCase;
use Safe\Exceptions\JsonException;

class JSONTest extends TestCase
{
    const INVALID_UTF8_SEQUENCE = "\xB1\x31";

    public function testSerializeThrowsIfNonEncodableValueIsGiven(): void
    {
        $this->expectException(JsonException::class);

        (new JSON())->serialize(self::INVALID_UTF8_SEQUENCE);
    }

    public function testSerializeThrowsIfJSONIsInvalid(): void
    {
        $this->expectException(JsonException::class);

        (new JSON())->serialize(self::INVALID_UTF8_SEQUENCE);
    }

    public function testSerializePassesWhenJSONIsValid(): void
    {
        $serializedResult = (new JSON())->serialize(1);

        $this->assertSame('1', $serializedResult);
    }

    public function testParseValueThrowsIfJSONIsInvalid(): void
    {
        $this->expectException(Error::class);

        (new JSON())->parseValue('foo');
    }

    public function testParseValuePassesIfJSONIsValid(): void
    {
        $this->assertSame(
            [1, 2],
            (new JSON())->parseValue('[1, 2]')
        );
    }

    public function testParseLiteralThrowsIfNotValidJSON(): void
    {
        $this->expectException(Error::class);

        (new JSON())->parseLiteral(new StringValueNode(['value' => 'foo']));
    }

    public function testParseLiteralPassesIfJSONIsValid(): void
    {
        $this->assertSame(
            'bar',
            (new JSON())->parseLiteral(new StringValueNode(['value' => /** @lang JSON */ '{"foo": "bar"}']))->foo
        );
    }
}
