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

        $item = $this->addItem($product, $quantity, $cart->getId());
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

            if ($user === null) {
                $userId = 0;
            } else {
                $userId = (int) $user->getId();
            }

            $cart = $this->cartFactory->create($userId);
        }

        return $cart;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param int $cartId
     * @return OrderItem
     */
    public function addItem(Product $product, int $quantity, int $cartId): OrderItem
    {
        $item = $this->getItemFromCart($cartId, $product->getId());

        if ($item !== null) {
            $quantity += $item->getQuantity();

            $item->setQuantity($quantity);

        } else {
            $item = $this->cartFactory->createItem($product, $quantity, $cartId);
        }

        return $item;
    }

    /**
     * @param int $cartId
     * @param int $productId
     * @return OrderItem|null
     */
    public function getItemFromCart(int $cartId, int $productId): OrderItem|null
    {
        return $this->orderItemRepository->findOneBy([
            'orderId' => $cartId,
            'productId' => $productId
        ]);
    }

    public function setCurrentCart()
    {
        $cart = $this->getCurrentCart();

        $this->cartSessionStorage->setCart($cart);
    }

    /**
     * @param Order $cart
     */
    public function saveCart(Order $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $this->cartSessionStorage->setCart($cart);
    }

    /**
     * @param OrderItem $item
     */
    public function saveItem(OrderItem $item): void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }
}
