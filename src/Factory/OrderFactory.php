<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * @param User $user
     * @return Order
     */
    public function create(User $user): Order
    {
        $order = new Order();

        $order
            ->setUser($user)
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        return $order;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param Order $cart
     * @return OrderItem
     */
    public function createItem(Product $product, int $quantity, Order $cart): OrderItem
    {
        $item = new OrderItem();
        $item->setProductId($product);
        $item->setQuantity($quantity);
        $item->setOrderId($cart);

        return $item;
    }
}
