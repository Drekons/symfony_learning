<?php

namespace App\Homework;

use SymfonySkillbox\HomeworkBundle\StrategyInterface;
use SymfonySkillbox\HomeworkBundle\Unit;

class BillGatesStrategy implements StrategyInterface
{
    public function next(array $units, int $resource): ?Unit
    {
        /** @var Unit $max */
        $max = null;

        /** @var Unit $unit */
        foreach ($units as $unit) {
            if ($unit->getCost() > $resource) {
                continue;
            }

            if (!$max || $unit->getCost() > $max->getCost()) {
                $max = $unit;
            }
        }

        return $max;
    }
}
