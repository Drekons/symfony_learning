<?php

namespace SymfonySkillbox\HomeworkBundle;

class HealthStrategy implements StrategyInterface
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

            if (!$max || $unit->getHealth() > $max->getHealth()) {
                $max = $unit;
            }
        }

        return $max;
    }
}
