<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 */
#[Route('/product', name: 'product.')]
class ProductController extends AbstractController
{
    /**
     * @param Product $product
     * @param Request $request
     * @param CartManager $cartManager
     * @return Response
     */
    #[Route('/{id}', name: 'detail')]
    public function index(Product $product, Request $request, CartManager $cartManager): Response
    {
        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartManager->addItemToCart($product, $form->getData()->getQuantity());

            return $this->redirectToRoute('product.detail', ['id' => $product->getId()]);
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $products
     * @return Response
     */
    #[Route('', name: 'list')]
    public function list(array $products): Response
    {
        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}
