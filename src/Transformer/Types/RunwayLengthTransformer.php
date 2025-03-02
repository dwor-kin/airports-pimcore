<?php
declare(strict_types=1);

namespace App\Transformer\Types;

final class RunwayLengthTransformer extends FeetToMeterTransformer
{
    public function supports(string $type): bool
    {
        return $type === 'runwayLengthTransformer';
    }

    public function getTransformedValue(mixed $value): float|null
    {
        if (empty($data['length_ft'])) {
            return null;
        }

        return $this->transformValue($data['length_ft']);
    }
}
