<?php


namespace App\Factory;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function create(UserInterface $user = null): Order
    {
        $order = new Order();

        if ($user === null) {
            $userId = 0;
        } else {
            $userId = $user->getUserIdentifier();
        }

        $order
            ->setUserId($userId)
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

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
        $item->setProductId($product->getId());
        $item->setQuantity($quantity);

        return $item;
    }
}
