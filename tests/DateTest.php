<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\Date;
use MLL\GraphQLScalars\DateScalar;

final class DateTest extends DateScalarTest
{
    public function invalidDateValues(): iterable
    {
        yield from parent::invalidDateValues();

        yield "Can't have 29th February" => ['2021-02-29'];
        yield "Can't have 30th February" => ['2020-02-30'];
        yield "Can't have 31th November" => ['2020-11-31'];
        yield 'DateTime' => ['2020-02-01 01:02:03'];
        yield 'DateTimeTz' => ['2017-02-01T00:00:00Z'];
    }

    protected function scalarInstance(): DateScalar
    {
        return new Date();
    }

    public function validDates(): iterable
    {
        yield ['2020-04-20', '2020-04-20T00:00:00.000000+00:00'];
    }
}
