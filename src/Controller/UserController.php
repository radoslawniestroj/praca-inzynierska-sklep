<?php

namespace App\Controller;

use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user', name: 'user.')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CartManager $cartManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($cartManager->getCurrentCart()->getId() === null) {
            $cartManager->setCurrentCart();
        }

        dump($cartManager->getCurrentCart());

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
