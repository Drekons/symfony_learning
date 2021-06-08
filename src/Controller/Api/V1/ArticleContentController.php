<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\V1\Request\ArticleContentRequest;
use App\Entity\User;
use App\Homework\ArticleContentProviderInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleContentController
 *
 * @method User|null getUser()
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 *
 * @package App\Controller\Api\V1
 */
class ArticleContentController extends AbstractController
{
    /**
     * @var ArticleContentProviderInterface
     */
    private $articleContent;

    /**
     * ArticleContentProviderCommand constructor.
     *
     * @param  ArticleContentProviderInterface  $articleContent
     */
    public function __construct(ArticleContentProviderInterface $articleContent)
    {
        $this->articleContent = $articleContent;
    }

    /**
     * @Route("/api/v1/article_content", name="api_article_content")
     * @param  ArticleContentRequest  $articleContentRequest
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     */
    public function index(ArticleContentRequest $articleContentRequest, LoggerInterface $apiLogger)
    {
        if (!$this->isGranted('ROLE_API')) {
            $apiLogger->warning(
                "Попытка получить доступ к API",
                [
                    'id'        => $this->getUser()->getId(),
                    'email'     => $this->getUser()->getEmail(),
                    'firstName' => $this->getUser()->getFirstName(),
                    'roles'     => $this->getUser()->getRoles(),
                ]
            );

            return $this->json(['status' => '403 Forbidden'], 403);
        }

        return $this->json(
            [
                'text' => $this->articleContent->get(
                    $articleContentRequest->getParagraphs(),
                    $articleContentRequest->getWord(),
                    $articleContentRequest->getWordsCount()
                ),
            ]
        );
    }
}
