<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentsController
 * @IsGranted("ROLE_ADMIN_COMMENT")
 * @package App\Controller\Admin
 */
class CommentsController extends AbstractController
{

    /** @var int[] */
    private const LIMIT_VARIANTS = [10, 20, 50];

    /**
     * @Route("/admin/comments", name="app_admin_comments")
     * @param Request            $request
     * @param CommentRepository  $commentRepository
     *
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $tagsQuery = $commentRepository->findAllWithSearchQuery(
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

        return $this->render('admin/comments/index.html.twig', [
            'pagination' => $pagination,
            'limit_variants' => self::LIMIT_VARIANTS,
            'limit' => $limit
        ]);
    }
}
