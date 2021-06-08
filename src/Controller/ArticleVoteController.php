<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleVoteController extends AbstractController
{
    /**
     * @Route("/articles/{slug}/vote/{type<up|down>}", methods={"POST"}, name="app_article_vote")
     */
    public function like(Article $article, $type, EntityManagerInterface $em)
    {
        if ($type === 'up') {
            $article->voteUp();
        } else {
            $article->voteDown();
        }

        $em->flush();

        return $this->json(['count' => $article->getVoteCount()]);
    }
}
