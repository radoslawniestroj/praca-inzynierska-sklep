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
     * Creates an order.
     *
     * @return Order
     */
    public function create(): Order
    {
        $order = new Order();
        $order
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $order;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderItem
     */
    public function createItem(Product $product, int $quantity): OrderItem
    {
        $item = new OrderItem();
        $item->setProductId($product->getId());
        $item->setQuantity($quantity);

        return $item;
    }
}
