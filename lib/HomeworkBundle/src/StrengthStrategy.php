<?php

namespace SymfonySkillbox\HomeworkBundle;

class StrengthStrategy implements StrategyInterface
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

            if (!$max || $unit->getStrength() > $max->getStrength()) {
                $max = $unit;
            }
        }

        return $max;
    }
}
