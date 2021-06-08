<?php

namespace App\Homework;

/**
 * Class ArticleProvider
 *
 * @package App\Homework
 */
class ArticleProvider
{
    /**
     * @var ArticleContentProviderInterface
     */
    private $articleContent;

    /**
     * ArticleProvider constructor.
     *
     * @param ArticleContentProviderInterface $articleContent
     */
    public function __construct(ArticleContentProviderInterface $articleContent)
    {
        $this->articleContent = $articleContent;
    }

    /**
     * @param string $slug
     *
     * @return array|null
     * @throws \Exception
     */
    public function article(string $slug): ?array
    {
        $article = array_column($this->articles(), null, 'slug')[$slug] ?? null;

        if (!$article) {
            return null;
        }

        $article['content'] = $this->articleContent->get(
            rand(2, 10),
            ['красный', 'синий', 'жёлтый', 'зелёный', 'белый', 'чёрный'][rand(0, 5)],
            rand(5, 15),
            false
        );

        return $article;
    }

    /**
     * @return \string[][]
     */
    public function articles()
    {
        return [
            [
                'title' => 'Что делать, если надо верстать?',
                'slug'  => 'article-1',
                'image' => '/images/article-2.jpeg',
            ],
            [
                'title' => 'Facebook ест твои данные',
                'slug'  => 'article-2',
                'image' => '/images/article-1.jpeg',
            ],
            [
                'title' => 'Когда пролил кофе на клавиатуру',
                'slug'  => 'article-3',
                'image' => '/images/article-3.jpg',
            ],
        ];
    }
}
