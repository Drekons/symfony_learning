<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Events\ArticleCreatedEvent;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticlesController
 *
 * @package App\Controller\Admin
 */
class ArticlesController extends AbstractController
{
    private const LIMIT_VARIANTS = [10, 20, 50];

    /**
     * @Route("/admin/articles", name="app_admin_articles")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {
        $tagsQuery = $articleRepository->findAllWithSearchQuery(
            $request->query->get('q'),
            $request->query->has('showDeleted')
        );

        $limit = in_array($request->query->get('limit'), self::LIMIT_VARIANTS)
            ? (int)$request->query->get('limit')
            : 20;

        $pagination = $paginator->paginate(
            $tagsQuery,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render(
            'admin/articles/index.html.twig',
            [
                'pagination'     => $pagination,
                'limit_variants' => self::LIMIT_VARIANTS,
                'limit'          => $limit,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     * @Route("/admin/articles/create", name="app_admin_articles_create")
     * @param  EntityManagerInterface    $em
     * @param  Request                   $request
     * @param  FileUploader              $articleFileUploader
     * @param  EventDispatcherInterface  $dispatcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(
        EntityManagerInterface $em,
        Request $request,
        FileUploader $articleFileUploader,
        EventDispatcherInterface $dispatcher
    ) {
        $form = $this->createForm(ArticleFormType::class, new Article());

        if ($article = $this->handleFormRequest($form, $em, $request, $articleFileUploader)) {
            $this->addFlash(
                'flash_message',
                [
                    'type'    => 'success',
                    'message' => 'Статья успешно создана',
                ]
            );

            $dispatcher->dispatch(new ArticleCreatedEvent($article));

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render(
            'admin/articles/create.html.twig',
            [
                'articleForm' => $form->createView(),
            ]
        );
    }

    private function handleFormRequest(
        FormInterface $form,
        EntityManagerInterface $em,
        Request $request,
        FileUploader $articleFileUploader
    ) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $em->persist($article);
            $em->flush();

            return $article;
        }

        return null;
    }

    /**
     * @Route("/admin/articles/{id}/edit", name="app_admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(
        Article $article,
        EntityManagerInterface $em,
        Request $request,
        FileUploader $articleFileUploader
    ) {
        $form = $this->createForm(ArticleFormType::class, $article);

        if ($article = $this->handleFormRequest($form, $em, $request, $articleFileUploader)) {
            $this->addFlash(
                'flash_message',
                [
                    'type'    => 'success',
                    'message' => 'Статья успешно изменена',
                ]
            );

            return $this->redirectToRoute(
                'app_admin_article_edit',
                [
                    'id' => $article->getId(),
                ]
            );
        }

        return $this->render(
            'admin/articles/edit.html.twig',
            [
                'articleForm' => $form->createView(),
                'showError'   => $form->isSubmitted(),
            ]
        );
    }
}
