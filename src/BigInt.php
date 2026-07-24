<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Type\Definition\ScalarType;

/**
 * @phpstan-import-type ScalarConfig from ScalarType
 */
class BigInt extends Regex
{
    public ?string $description = <<<'DESCRIPTION'
An arbitrarily long sequence of digits that represents a big integer.
DESCRIPTION;

    /** @phpstan-param ScalarConfig $config */
    public function __construct(array $config = [])
    {
        parent::__construct($config + SpecifiesByReadme::readmeSpecification('bigint'));
    }

    public static function regex(): string
    {
        return "/\d+/";
    }
}
