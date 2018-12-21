<?php

declare(strict_types=1);

namespace Tests;

use MLL\GraphQLScalars\Regex;

class MyRegex extends Regex
{
    /**
     * @var string
     */
    public $description = 'Bar';

    /**
     * Return the Regex that the values are validated against.
     *
     * @return string
     */
    public static function regex(): string
    {
        return '/foo/';
    }
}
