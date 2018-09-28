<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

class Email extends StringScalar
{
    /** @var string */
    public $description = 'A valid RFC 5321 compliant email.';
    
    /**
     * Check if the given string is a valid email.
     *
     * @param string $stringValue
     *
     * @return bool
     */
    protected function isValid(string $stringValue): bool
    {
        return (new EmailValidator)->isValid(
            $stringValue,
            new RFCValidation
        );
    }
}
