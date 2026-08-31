<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Type\Definition\ScalarType;

/**
 * @phpstan-import-type ScalarConfig from ScalarType
 */
class Date extends DateScalar
{
    public ?string $description /** @lang Markdown */
        = 'A date string with format `Y-m-d`, e.g. `2011-05-23`.';

    /** @phpstan-param ScalarConfig $config */
    public function __construct(array $config = [])
    {
        parent::__construct($config + SpecifiesByReadme::readmeSpecification('date'));
    }

    protected static function outputFormat(): string
    {
        return 'Y-m-d';
    }

    protected static function regex(): string
    {
        return '~^(?<date>\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][\d]|3[01]))$~';
    }
}
