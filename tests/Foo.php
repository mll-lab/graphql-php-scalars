<?php

declare(strict_types=1);

namespace Tests;

use MLL\GraphQLScalars\Regex;

class Foo extends Regex
{
    public $description = 'Bar';
    
    /**
     * Return the Regex that the values are validated against.
     *
     * Must be a valid
     *
     * @return string
     */
    protected function regex(): string
    {
        return '/foo/';
    }
}
