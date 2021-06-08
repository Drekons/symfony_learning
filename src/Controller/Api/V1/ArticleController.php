<?php

namespace App\Controller\Api\V1;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param  Article  $article
     * @IsGranted("API", subject="article")
     * @Route("/api/v1/artices/{id}", name="api_article_show")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show(Article $article)
    {
        return $this->json($article, 200, [], ['groups' => ['main']]);
    }
}
