<?php

namespace App\Homework;

use SymfonySkillbox\HomeworkBundle\StrategyInterface;
use SymfonySkillbox\HomeworkBundle\Unit;

class ZergRushStrategy implements StrategyInterface
{

    public function next(array $units, int $resource): ?Unit
    {
        /** @var Unit $min */
        $min = null;

        /** @var Unit $unit */
        foreach ($units as $unit) {
            if ($unit->getCost() > $resource) {
                continue;
            }

            if (!$min || $unit->getCost() < $min->getCost()) {
                $min = $unit;
            }
        }

        return $min;
    }
}
