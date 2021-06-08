<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController
{
    public function lastComments(CommentRepository $commentRepository): Response
    {
        return $this->render('partial/last_comments.html.twig', [
            'comments' => $commentRepository->findLatestWithArticle(3)
        ]);
    }
}
