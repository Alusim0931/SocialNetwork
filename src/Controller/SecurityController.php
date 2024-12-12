<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'logout', methods: ["GET"])]
    public function logout(): void
    {
        throw new \RuntimeException('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/isLogged', name: 'isLogged', methods: ["GET"])]
    public function isLoggedIn(Security $security): JsonResponse
    {
        $user = $security->getUser();
        return new JsonResponse(['loggedIn' => $user !== null]);
    }
}