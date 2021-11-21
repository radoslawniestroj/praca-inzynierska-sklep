<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * @param int $userId
     * @return Order
     */
    public function create(int $userId): Order
    {
        $order = new Order();

        $order
            ->setUserId($userId)
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        return $order;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param int $cartId
     * @return OrderItem
     */
    public function createItem(Product $product, int $quantity, int $cartId): OrderItem
    {
        $item = new OrderItem();
        $item->setProductId($product->getId());
        $item->setProductId($product->getId());
        $item->setQuantity($quantity);
        $item->setOrderId($cartId);

        return $item;
    }
}
