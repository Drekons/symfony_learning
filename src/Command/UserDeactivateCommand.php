<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserDeactivateCommand extends Command
{
    protected static $defaultName = 'app:user:deactivate';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserDeactivateCommand constructor.
     *
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Деактивировать пользователя')
            ->addArgument('id', InputArgument::REQUIRED, 'Id пользователя')
            ->addOption('reverse', null, InputOption::VALUE_NONE, 'Активировать пользователя');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $user = $this->userRepository->find($id);

        if (!$user) {
            $io->error(sprintf("Пользователь с id %s не найден", $id));
            return Command::FAILURE;
        }

        $this->setActive($user, $input->getOption('reverse'));

        if ($input->getOption('reverse')) {
            $io->success(sprintf("Пользователь с id %s активирован", $id));
        } else {
            $io->success(sprintf("Пользователь с id %s деактивирован", $id));
        }

        return Command::SUCCESS;
    }

    private function setActive(User $user, bool $active)
    {
        $user->setIsActive($active);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
