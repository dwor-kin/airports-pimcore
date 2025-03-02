<?php
declare(strict_types=1);

namespace App\Filter;

use Pimcore\Bundle\DataImporterBundle\Filter\FilterInterface;

class PolishAirportsFilter implements FilterInterface
{
    public function filter(array $item): bool
    {
        return isset($item['country']) && mb_strtolower(trim($item['country'])) === 'PL';
    }

    public function getType(): string
    {
        return 'polish_airports_filter';
    }
}
