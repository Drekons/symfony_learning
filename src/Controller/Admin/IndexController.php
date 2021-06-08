<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @IsGranted("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin_index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }
}
