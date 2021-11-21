<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 */
#[Route('/category', name: 'category.')]
class CategoryController extends AbstractController
{
    /**
     * @param Category $category
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/{id}', name: 'detail')]
    public function index(Category $category, ProductRepository $productRepository): Response
    {
        $products = $productRepository->getAllProductsFromCategory($category->getId());

        return $this->render('category/detail.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('', name: 'navbar')]
    public function navbar(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/navbar.html.twig', [
            'categories' => $categories
        ]);
    }
}
