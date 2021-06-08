<?php

namespace App\Controller\Api\V1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @package App\Controller\Api\V1
 */
class UserController extends AbstractController
{
    /**
     * @Route("/api/v1/user", name="api_v1_user")
     */
    public function index()
    {
        return $this->json($this->getUser(), 200, [], ['groups' => ['main']]);
    }
}
