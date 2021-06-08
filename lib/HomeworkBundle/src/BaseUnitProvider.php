<?php

namespace SymfonySkillbox\HomeworkBundle;

class BaseUnitProvider implements UnitProviderInterface
{
    /**
     * @var bool
     */
    private $enableSolder;
    /**
     * @var bool
     */
    private $enableArcher;

    /**
     * BaseUnitProvider constructor.
     *
     * @param  bool  $enableSolder
     * @param  bool  $enableArcher
     */
    public function __construct(bool $enableSolder, bool $enableArcher)
    {
        $this->enableSolder = $enableSolder;
        $this->enableArcher = $enableArcher;
    }

    public function getUnits(): array
    {
        $units = [
            new Unit('Крестьянин', 33, 1, 1),
        ];

        if ($this->enableSolder) {
            $units[] = new Unit('Солдат', 100, 5, 100);
        }

        if ($this->enableArcher) {
            $units[] = new Unit('Лучник', 150, 6, 50);
        }

        return $units;
    }
}
