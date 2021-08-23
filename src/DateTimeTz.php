<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

class DateTimeTz extends DateScalar
{
    public $description /** @lang Markdown */
        = 'The `DateTime` scalar type represents time data, represented as an ISO-8601 encoded UTC date string.';

    protected static function outputFormat(): string
    {
        return 'Y-m-d\TH:i:s.uP';
    }

    protected static function regex(): string
    {
        return '~^((?<date>\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][\d]|3[01]))T([01][\d]|2[0-3]):([0-5][\d]):([0-5][\d]|60))(\.\d+)?(([Z])|([+|-]([01][\d]|2[0-3]):[0-5][\d]))$~';
    }
}
