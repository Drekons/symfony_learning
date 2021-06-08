<?php

namespace SymfonySkillbox\HomeworkBundle;

class UnitFactory
{
    private $strategy;
    /**
     * @var UnitProviderInterface
     */
    private $unitProvider;

    /**
     * @param  StrategyInterface      $strategy
     * @param  UnitProviderInterface  $unitProvider
     */
    public function __construct(StrategyInterface $strategy, UnitProviderInterface $unitProvider)
    {
        $this->strategy = $strategy;
        $this->unitProvider = $unitProvider;
    }

    /**
     * Производит армию
     *
     * @param int $resources
     * @return Unit[]
     */
    public function produceUnits(int $resources): array
    {
        $units = $this->unitProvider->getUnits();

        $army = [];
        while ($unit = $this->strategy->next($units, $resources)) {
            $army[] = $unit;
            $resources -= $unit->getCost();
        }

        return $army;
    }
}
