<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    /**
     * @param ProductRepository $productRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository, UserInterface $user = null): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
            'user' => $user
        ]);
    }
}
