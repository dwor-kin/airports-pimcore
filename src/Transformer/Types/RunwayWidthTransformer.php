<?php
declare(strict_types=1);

namespace App\Transformer\Types;

final class RunwayWidthTransformer extends FeetToMeterTransformer
{
    public function supports(string $type): bool
    {
        return $type === 'runwayWidthTransformer';
    }

    public function getTransformedValue(mixed $value): float|null
    {
        if (empty($data['width_ft'])) {
            return null;
        }

        return $this->transformValue($data['width_ft']);
    }
}
