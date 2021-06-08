<?php

namespace App\Command;

use App\Homework\ArticleContentProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ArticleContentProviderCommand
 *
 * @package App\Command
 */
class ArticleContentProviderCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:article:content_provider';

    /**
     * @var ArticleContentProviderInterface
     */
    private $articleContent;

    /**
     * ArticleContentProviderCommand constructor.
     *
     * @param ArticleContentProviderInterface $articleContent
     */
    public function __construct(ArticleContentProviderInterface $articleContent)
    {
        parent::__construct();
        $this->articleContent = $articleContent;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('Получить сгенерированный контент статьи')
            ->addArgument('paragraphs', InputArgument::REQUIRED, 'Количество абзацев')
            ->addOption('word', 'w', InputOption::VALUE_OPTIONAL, 'Вставить случайное слово')
            ->addOption('wordsCount', 'c', InputOption::VALUE_OPTIONAL, 'Сколько раз вставить слово');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $params = $this->getValidParams($input, $io);

        $io->text($this->articleContent->get($params['paragraphs'], $params['word'], $params['wordsCount']));

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param SymfonyStyle   $io
     *
     * @return array
     * @throws \Exception
     */
    protected function getValidParams(InputInterface $input, SymfonyStyle $io): array
    {
        $paragraphs = (int)$input->getArgument('paragraphs');

        if ($paragraphs < 1) {
            throw new \Exception('Количество абзацев должно быть больше 0');
        }

        $word = $input->getOption('word');
        $wordsCount = (int)$input->getOption('wordsCount');

        if ($word && $wordsCount < 1) {
            $io->warning('Количество повторов слова должно быть больше 0');
            $wordsCount = 0;
        }

        return [
            'paragraphs' => $paragraphs,
            'word'       => $word,
            'wordsCount' => $wordsCount,
        ];
    }
}
