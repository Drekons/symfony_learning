<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminStatisticReportCommand extends Command
{
    protected static $defaultName = 'app:admin-statistic-report';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, Mailer $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Отправка статистики администратору')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail администратора')
            ->addOption('dateFrom', null, InputOption::VALUE_NONE, 'Дата начала периода')
            ->addOption('dateTo', null, InputOption::VALUE_NONE, 'Дата окончания периода');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        /** @var User $user */
        $user = $this->userRepository->findBy(['email' => $email])[0] ?? null;

        if (!$user) {
            $io->error(sprintf("Пользователь с e-mail %s не найден", $email));
            return Command::FAILURE;
        }

        $io->note(sprintf('Отправка отчёта на адрес: %s', $user->getEmail()));

        try {
            $dateFrom = new \DateTime($input->getOption('dateFrom') ?? '-1 week');
            $dateTo = new \DateTime($input->getOption('dateTo') ?? 'now');
        } catch (\Throwable $e) {
            $io->error('Указан не верный формат даты');
            return Command::FAILURE;
        }

        $fileName = 'var/test.csv';
        $fp = fopen($fileName, 'w');
        fputcsv($fp, ['Период', 'Всего пользователей', 'Статей создано за период', 'Статей опубликовано за период']);
        fputcsv(
            $fp,
            [
                $dateFrom->format('d.m.Y') . ' - ' . $dateTo->format('d.m.Y'),
                $this->userRepository->findCountUsersByPeriod($dateFrom, $dateTo),
                $this->articleRepository->findAllPublishedByPeriod($dateFrom, $dateTo),
                $this->articleRepository->findAllCreatedByPeriod($dateFrom, $dateTo),
            ]
        );
        fclose($fp);

        $this->mailer->sendAdminStatisticReport($user, fopen($fileName, 'r'));

        unlink($fileName);

        return Command::SUCCESS;
    }
}
