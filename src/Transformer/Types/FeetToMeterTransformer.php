<?php
declare(strict_types=1);

namespace App\Transformer\Types;

abstract class FeetToMeterTransformer extends AbstractTransformer
{
    protected const FEET_TO_METERS = 0.3048;

    protected function transformValue(int $value): float
    {
        return round($value * self::FEET_TO_METERS, 2);
    }
}
