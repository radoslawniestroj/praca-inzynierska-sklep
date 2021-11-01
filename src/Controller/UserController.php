<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user', name: 'user.')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
