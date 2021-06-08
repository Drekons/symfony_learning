<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Homework\ArticleContentProviderInterface;
use App\Service\FileUploader;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\HttpFoundation\File\File;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private static $articleTitles = [
        'Есть ли жизнь после девятой жизни?',
        'Когда в машинах поставят лоток?',
        'В погоне за красной точкой',
        'В чем смысл жизни сосисок',
    ];

    private static $articleImages = [
        'article-1.jpeg',
        'article-2.jpeg',
        'article-3.jpg',
    ];

    /**
     * @var ArticleContentProviderInterface
     */
    private $articleContent;
    /**
     * @var FileUploader
     */
    private $articleFileUploader;

    /**
     * ArticleFixtures constructor.
     *
     * @param  ArticleContentProviderInterface  $articleContent
     * @param  FileUploader                     $articleFileUploader
     */
    public function __construct(ArticleContentProviderInterface $articleContent, FileUploader $articleFileUploader)
    {
        $this->articleContent = $articleContent;
        $this->articleFileUploader = $articleFileUploader;
    }

    function loadData(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $this->createMany(
            Article::class,
            25,
            function (Article $article) {
                $fileName = $this->faker->randomElement(self::$articleImages);

                $article
                    ->setTitle($this->faker->randomElement(self::$articleTitles))
                    ->setDescription($this->faker->text(100))
                    ->setBody(
                        $this->articleContent->get(
                            $this->faker->numberBetween(2, 10),
                            $this->faker->numberBetween(0, 9) > 2 ? $this->faker->word : '',
                            $this->faker->numberBetween(5, 10),
                            false
                        )
                    )
                    ->setAuthor($this->getRandomReference(User::class))
                    ->setKeywords(join(', ', $this->faker->words($this->faker->numberBetween(2, 10))))
                    ->setImageFilename(
                        $this->articleFileUploader->uploadFile(
                            new File(dirname(dirname(__DIR__)) . '/public/images/' . $fileName)
                        )
                    );

                if ($this->faker->boolean(60)) {
                    $article->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
                }

                /** @var Tag $tags */
                $tags = [];
                for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
                    $tags[] = $this->getRandomReference(Tag::class);
                }

                foreach ($tags as $tag) {
                    $article->addTag($tag);
                }
            }
        );
    }

    public function getDependencies()
    {
        return [
            ClearMediaFixtures::class,
            TagFixtures::class,
            UserFixtures::class,
        ];
    }
}
