<?php

namespace App\Storage;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorage
{
    public const CART_ID = 'cart_id';
    public const CART_USER_ID = 'cart_user_id';

    /** @var RequestStack */
    private $requestStack;

    /** @var OrderRepository */
    private $cartRepository;

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
            'id' => $this->getCartId(),
            'userId' => $this->getCartUserId(),
            'status' => Order::STATUS_CART
        ]);
    }

    /**
     * @param Order $cart
     */
    public function setCart(Order $cart): void
    {
        $this->getSession()->set(self::CART_ID, $cart->getId());
        $this->getSession()->set(self::CART_USER_ID, $cart->getUserId());
    }

    /**
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_ID);
    }

    /**
     * @return int|null
     */
    private function getCartUserId(): ?int
    {
        return $this->getSession()->get(self::CART_USER_ID);
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
