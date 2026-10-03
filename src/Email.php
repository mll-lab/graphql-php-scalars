<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use GraphQL\Type\Definition\ScalarType;

/**
 * @phpstan-import-type ScalarConfig from ScalarType
 */
class Email extends StringScalar
{
    public ?string $description /** @lang Markdown */
        = 'A [RFC 5321](https://tools.ietf.org/html/rfc5321) compliant email.';

    /** @phpstan-param ScalarConfig $config */
    public function __construct(array $config = [])
    {
        parent::__construct($config + SpecifiesByReadme::readmeSpecification('email'));
    }

    protected function isValid(string $stringValue): bool
    {
        return (new EmailValidator())->isValid(
            $stringValue,
            new RFCValidation()
        );
    }
}
