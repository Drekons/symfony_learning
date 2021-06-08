<?php

namespace App\Controller;

use App\Controller\Request\ArticleContentRequest;
use App\Entity\Article;
use App\Homework\ArticleContentProviderInterface;
use App\Homework\ArticleProvider;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ArticleController
 *
 * @package App\Controller
 */
class ArticleController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     * @param  ArticleRepository  $repository
     * @param  CommentRepository  $commentRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ArticleRepository $repository)
    {
        return $this->render('article/index.html.twig', [
            'articles' => $repository->findLatestPublishedWithCommentsWithTags()
        ]);
    }

    /**
     * @Route("/articles/article_content", name="app_article_content")
     * @param ArticleContentRequest           $request
     * @param ArticleContentProviderInterface $articleContent
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function content(ArticleContentRequest $request, ArticleContentProviderInterface $articleContent)
    {
        $text = null;
        if ($request->getParagraphs()) {
            $text = $articleContent->get(
                $request->getParagraphs(),
                $request->getWord(),
                $request->getWordsCount(),
                false
            );
        }

        return $this->render('article/content.html.twig', [
            'text' => $text
        ]);
    }

    /**
     * @Route("/articles/{slug<[\w-]+>}", name="app_article_show")
     * @param Article         $article
     * @param ArticleProvider $articleProvider
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Article $article, ArticleProvider $articleProvider)
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

}
