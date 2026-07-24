<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Type\Definition\ScalarType;

/**
 * @phpstan-import-type ScalarConfig from ScalarType
 */
class DateTime extends DateScalar
{
    public ?string $description /** @lang Markdown */
        = 'A datetime string with format `Y-m-d H:i:s`, e.g. `2018-05-23 13:43:32`.';

    /** @phpstan-param ScalarConfig $config */
    public function __construct(array $config = [])
    {
        parent::__construct($config + SpecifiesByReadme::readmeSpecification('datetime'));
    }

    protected static function outputFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    protected static function regex(): string
    {
        return '~^(?<date>\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][\d]|3[01])) ([01][\d]|2[0-3]):([0-5][\d]):([0-5][\d]|60)$~';
    }
}
