<?php

namespace SymfonySkillbox\HomeworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use SymfonySkillbox\HomeworkBundle\UnitFactory;

class ProduceUnits extends Command
{
    protected static $defaultName = 'symfony-skillbox-homework:produce-units';

    /**
     * @var UnitFactory
     */
    private $factory;

    public function __construct(UnitFactory $factory)
    {
        parent::__construct();
        $this->factory = $factory;
    }

    protected function configure()
    {
        $this
            ->setDescription('Создать армию')
            ->addArgument('sum', InputArgument::REQUIRED, 'Сумма на сколько можно купить армию');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $sum = (int)$input->getArgument('sum');

        $army = $this->factory->produceUnits($sum);
        $armyArray = array_map(function ($item) {
            return $item->__toArray();
        }, $army);

        $left = $sum - array_sum(array_column($armyArray, 'cost'));

        $io->writeln("на {$sum} было куплено ". count($army) ." юнитов");

        $io->table(['Имя', 'Цена', 'Сила', 'Здоровье'], $armyArray);

        $io->writeln("Осталось ресурсов: {$left}");

        return Command::SUCCESS;
    }
}
