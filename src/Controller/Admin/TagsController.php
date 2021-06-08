<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagsController
 * @IsGranted("ROLE_ADMIN_TAG")
 * @package App\Controller\Admin
 */
class TagsController extends AbstractController
{
    private const LIMIT_VARIANTS = [10, 20, 50];

    /**
     * @Route("/admin/tags", name="app_admin_tags")
     * @param Request            $request
     * @param TagRepository      $tagRepository
     *
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, TagRepository $tagRepository, PaginatorInterface $paginator)
    {
        $tagsQuery = $tagRepository->findAllWithSearchQuery(
            $request->query->get('q'),
            $request->query->has('showDeleted')
        );

        $limit = in_array($request->query->get('limit'), self::LIMIT_VARIANTS)
            ? (int) $request->query->get('limit')
            : 20;

        $pagination = $paginator->paginate(
            $tagsQuery,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/tags/index.html.twig', [
            'pagination' => $pagination,
            'limit_variants' => self::LIMIT_VARIANTS,
            'limit' => $limit
        ]);
    }
}
