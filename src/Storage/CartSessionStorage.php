<?php

namespace App\Storage;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CartSessionStorage
 * @package App\Storage
 */
class CartSessionStorage
{
    public const CART_ID = 'cart_id';
    public const CART_USER = 'cart_user';

    private RequestStack $requestStack;

    private OrderRepository $cartRepository;

    /**
     * @param RequestStack $requestStack
     * @param OrderRepository $cartRepository
     */
    public function __construct(RequestStack $requestStack, OrderRepository $cartRepository)
    {
        $this->requestStack = $requestStack;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return Order|null
     */
    public function getCart(): ?Order
    {
        return $this->cartRepository->findOneBy([
            'user' => $this->getCartUserId(),
            'status' => Order::STATUS_CART
        ]);
    }

    /**
     * @param Order $cart
     */
    public function setCart(Order $cart): void
    {
        $this->getSession()->set(self::CART_ID, $cart->getId());
        $this->getSession()->set(self::CART_USER, $cart->getUser());
    }

    /**
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_ID);
    }

    /**
     * @return User|null
     */
    private function getCartUserId(): ?User
    {
        return $this->getSession()->get(self::CART_USER);
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
