<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Homework\CommentContentProviderInterface;
use App\Repository\ArticleRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var CommentContentProviderInterface
     */
    private $commentContent;

    /**
     * CommentFixtures constructor.
     *
     * @param ArticleRepository               $articleRepository
     * @param CommentContentProviderInterface $commentContent
     */
    public function __construct(ArticleRepository $articleRepository, CommentContentProviderInterface $commentContent)
    {
        $this->articleRepository = $articleRepository;
        $this->commentContent = $commentContent;
    }

    /**
     * @param ObjectManager $manager
     */
    function loadData(ObjectManager $manager)
    {
        $this->createMany(
            Comment::class,
            $this->faker->numberBetween(50, 100),
            function (Comment $comment) {
                $comment
                    ->setAuthorName($this->faker->name)
                    ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 day'))
                    ->setContent(
                        $this->commentContent->get(
                            $this->faker->boolean(70) ? $this->faker->word : null,
                            $this->faker->numberBetween(1, 5)
                        )
                    )->setArticle($this->getRandomReference(Article::class));

                if ($this->faker->boolean) {
                    $comment->setDeletedAt($this->faker->dateTimeThisMonth);
                }
            }
        );
    }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
        ];
    }
}
