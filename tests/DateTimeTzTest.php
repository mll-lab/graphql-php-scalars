<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\DateScalar;
use MLL\GraphQLScalars\DateTimeTz;

final class DateTimeTzTest extends DateScalarTestBase
{
    public static function invalidDateValues(): iterable
    {
        yield from parent::invalidDateValues();

        yield "Can't have 30th February" => ['2020-02-30T00:00:00Z'];
        yield 'Date' => ['2020-02-01'];
        yield 'DateTime' => ['2020-02-01 01:02:03'];
        yield '001st' => ['2017-01-001T00:00:00Z'];
    }

    protected function scalarInstance(): DateScalar
    {
        return new DateTimeTz();
    }

    public static function validDates(): iterable
    {
        yield ['2020-04-20T16:20:04+04:00', '2020-04-20T16:20:04.000000+04:00'];
        yield ['2020-04-20T16:20:04Z', '2020-04-20T16:20:04.000000+00:00'];
        yield ['2020-04-20T16:20:04.0Z', '2020-04-20T16:20:04.000000+00:00'];
        yield ['2020-04-20T16:20:04.000Z', '2020-04-20T16:20:04.000000+00:00'];
        yield ['2020-04-20T16:20:04.987Z', '2020-04-20T16:20:04.987000+00:00'];
        yield ['2020-04-20T16:20:04.000000Z', '2020-04-20T16:20:04.000000+00:00'];
    }
}
