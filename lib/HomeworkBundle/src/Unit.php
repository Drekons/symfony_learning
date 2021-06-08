<?php

namespace SymfonySkillbox\HomeworkBundle;

class Unit
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $cost;
    /**
     * @var int
     */
    private $strength;
    /**
     * @var int
     */
    private $health;

    /**
     * Unit constructor.
     *
     * @param  string  $name - наименование существа (Крестьянин, Солдат, Лучник и т.д.)
     * @param  int     $cost  - стоимость создания существа
     * @param  int     $strength - условная сила существа
     * @param  int     $health - кол-во здоровья
     */
    public function __construct(string $name, int $cost, int $strength, int $health)
    {
        $this->name = $name;
        $this->cost = $cost;
        $this->strength = $strength;
        $this->health = $health;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @return int
     */
    public function getStrength(): int
    {
        return $this->strength;
    }

    /**
     * @return int
     */
    public function getHealth(): int
    {
        return $this->health;
    }

    public function __toArray(): array
    {
        return [
            'name'     => $this->getName(),
            'cost'     => $this->getCost(),
            'strength' => $this->getStrength(),
            'health'   => $this->getHealth(),
        ];
    }
}
