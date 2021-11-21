<?php

namespace App\Controller;

use App\Form\EventListener\ClearCartListenerType;
use App\Manager\CartManager;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package App\Controller
 */
#[Route('/cart', name: 'cart.')]
class CartController extends AbstractController
{
    /**
     * @param CartManager $cartManager
     * @param OrderItemRepository $orderItemRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'detail')]
    public function index(
        CartManager $cartManager,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository,
        Request $request
    ): Response {
        $cart = $cartManager->getCurrentCart();

        $items = $orderItemRepository->findBy([
            'orderId' => $cart->getId(),
        ]);

        $products = [];
        $quantityItems = [];
        $cartTotal = 0;

        foreach ($items as $item) {
            $product = $productRepository->findOneBy(['id' => $item->getProductId()]);
            $products[] = $product;

            $priceTotalItem = $item->getQuantity() * $product->getPrice();
            $cartTotal += $priceTotalItem;

            $quantityItems[] = [
                'id' => $item->getId(),
                'productId' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'priceTotal' => $priceTotalItem
            ];
        }

        $form = $this->createForm(ClearCartListenerType::class);

        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            $cartManager->addItemToCart($product, $form->getData()->getQuantity());
//
//            return $this->redirectToRoute('product.detail', ['id' => $product->getId()]);
//        }

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'products' => $products,
            'quantityItems' => $quantityItems,
            'itemsNumber' => count($items),
            'cartTotal' => $cartTotal,
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $quantityItems
     * @param array $products
     * @param array $items
     * @return Response
     */
    #[Route('', name: 'list')]
    public function list(array $quantityItems, array $products, array $items, $form): Response
    {
        dump($form);   /////////////////////////

        return $this->render('cart/list.html.twig', [
            'quantityItems' => $quantityItems,
            'products' => $products,
            'items' => $items,
            'form' => $form
        ]);
    }

//    /**
//     * @param array $quantityItems
//     * @param array $products
//     * @param array $items
//     * @return Response
//     */
//    #[Route('/save', name: 'save')]
//    public function save(Request $request): Response
//    {
//        dump($request);
//        $searchString = $request->get('quantity_28');
//        dump($searchString);
//
//        return $this->render('cart/clear.html.twig', []);
//    }

    /**
     * @param CartManager $cartManager
     * @param OrderItemRepository $orderItemRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/clear', name: 'clear')]
    public function clear(
        CartManager $cartManager,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository,
        Request $request
    ): Response
    {
        $cartManager->clearCart();

        return $this->index($cartManager, $orderItemRepository, $productRepository, $request);
    }
}
