<?php

declare(strict_types=1);

namespace Tests;

use MLL\GraphQLScalars\Regex;

class MyRegex extends Regex
{
    public $description = 'Bar';

    /**
     * Return the Regex that the values are validated against.
     */
    public static function regex(): string
    {
        return /** @lang RegExp */'/foo/';
    }
}
