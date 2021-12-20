<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Factory\OrderFactory;
use App\Repository\OrderItemRepository;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CartManager
 * @package App\Manager
 */
class CartManager
{
    private CartSessionStorage $cartSessionStorage;

    private Security $security;

    private OrderFactory $cartFactory;

    private EntityManagerInterface $entityManager;

    private OrderItemRepository $orderItemRepository;

    /**
     * @param CartSessionStorage $cartStorage
     * @param Security $security
     * @param OrderFactory $orderFactory
     * @param EntityManagerInterface $entityManager
     * @param OrderItemRepository $orderItemRepository
     */
    public function __construct(
        CartSessionStorage $cartStorage,
        Security $security,
        OrderFactory $orderFactory,
        EntityManagerInterface $entityManager,
        OrderItemRepository $orderItemRepository
    ) {
        $this->cartSessionStorage = $cartStorage;
        $this->security = $security;
        $this->cartFactory = $orderFactory;
        $this->entityManager = $entityManager;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return Order
     */
    public function addItemToCart(Product $product, int $quantity): Order
    {
        $cart = $this->getCurrentCart();
        $this->saveCart($cart);

        $item = $this->addItem($product, $quantity, $cart);
        $this->saveItem($item);

        return $cart;
    }

    /**
     * @return Order
     */
    public function getCurrentCart(): Order
    {
        $cart = $this->cartSessionStorage->getCart();

        if (!$cart) {
            $user = $this->security->getUser();

            $cart = $this->cartFactory->create($user);
        }

        return $cart;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param Order $cart
     * @return OrderItem
     */
    public function addItem(Product $product, int $quantity, Order $cart): OrderItem
    {
        $item = $this->getItemFromCart($cart, $product);

        if ($item !== null) {
            $quantity += $item->getQuantity();

            $item->setQuantity($quantity);

        } else {
            $item = $this->cartFactory->createItem($product, $quantity, $cart);
        }

        return $item;
    }

    /**
     * @param Order $cart
     * @param Product $product
     * @return OrderItem|null
     */
    public function getItemFromCart(Order $cart, Product $product): OrderItem|null
    {
        return $this->orderItemRepository->findOneBy([
            'order' => $cart,
            'product' => $product
        ]);
    }

    /**
     * @return Order
     */
    public function setCurrentCart(): Order
    {
        $cart = $this->getCurrentCart();

        $this->cartSessionStorage->setCart($cart);

        return $cart;
    }

    /**
     * @param Order $cart
     * @return Order
     */
    public function saveCart(Order $cart): Order
    {
        dump($cart);
        $this->entityManager->persist($cart);

        $this->cartSessionStorage->setCart($cart);

        return $cart;
    }

    /**
     * @param OrderItem $item
     * @return OrderItem
     */
    public function saveItem(OrderItem $item): OrderItem
    {
        dump($item);
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * @param Order $cart
     * @return Order
     */
    public function clearCart(): Order
    {
        $cart = $this->getCurrentCart();

        $items = $this->orderItemRepository->findBy([
            'order' => $cart,
        ]);

        foreach ($items as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();

        return $cart;
    }
}
