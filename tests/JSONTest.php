<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\JSON;
use PHPUnit\Framework\TestCase;
use Safe\Exceptions\JsonException;

final class JSONTest extends TestCase
{
    public const INVALID_UTF8_SEQUENCE = "\xB1\x31";

    public function testSerializeThrowsIfNonEncodableValueIsGiven(): void
    {
        $json = new JSON();

        $this->expectException(JsonException::class);
        $json->serialize(self::INVALID_UTF8_SEQUENCE);
    }

    public function testSerializeThrowsIfJSONIsInvalid(): void
    {
        $json = new JSON();

        $this->expectException(JsonException::class);
        $json->serialize(self::INVALID_UTF8_SEQUENCE);
    }

    public function testSerializePassesWhenJSONIsValid(): void
    {
        $serializedResult = (new JSON())->serialize(1);

        self::assertSame('1', $serializedResult);
    }

    public function testParseValueThrowsIfJSONIsInvalid(): void
    {
        $json = new JSON();

        $this->expectException(Error::class);
        $json->parseValue('foo');
    }

    public function testParseValuePassesIfJSONIsValid(): void
    {
        self::assertSame(
            [1, 2],
            (new JSON())->parseValue('[1, 2]')
        );
    }

    public function testParseLiteralThrowsIfNotValidJSON(): void
    {
        $json = new JSON();
        $stringValueNode = new StringValueNode(['value' => 'foo']);

        $this->expectException(Error::class);
        $json->parseLiteral($stringValueNode);
    }

    public function testParseLiteralPassesIfJSONIsValid(): void
    {
        $parsed = (new JSON())->parseLiteral(new StringValueNode([
            'value' => /** @lang JSON */ '{"foo": "bar"}',
        ]));

        self::assertInstanceOf(\stdClass::class, $parsed);
        self::assertSame('bar', $parsed->foo);
    }
}
