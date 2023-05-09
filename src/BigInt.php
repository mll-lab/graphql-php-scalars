<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

class BigInt extends Regex
{
    public ?string $description = <<<'DESCRIPTION'
A hexadecimal color is specified with: `#RRGGBB`, where `RR` (red), `GG` (green) and `BB` (blue)
are hexadecimal integers between `00` and `FF` specifying the intensity of the color.
DESCRIPTION;

    public static function regex(): string
    {
        return "/\d+/";
    }
}
