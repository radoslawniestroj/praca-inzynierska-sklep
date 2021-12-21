<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Factory\OrderFactory;
use App\Manager\CartManager;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class CartManagerTest  extends TestCase
{
    private CartManager $testClass;

    private MockObject $cartSessionStorageMock;
    private MockObject $securityMock;
    private MockObject $orderFactoryMock;
    private MockObject $entityManagerMock;
    private MockObject $orderItemRepositoryMock;
    private MockObject $sessionMock;
    private MockObject $userMock;
    private MockObject $orderMock;
    private MockObject $productMock;
    private MockObject $orderItemMock;

    protected function setUp(): void
    {
        $this->cartSessionStorageMock = $this->createMock(CartSessionStorage::class);
        $this->securityMock = $this->createMock(Security::class);
        $this->orderFactoryMock = $this->createMock(OrderFactory::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->orderItemRepositoryMock = $this->createMock(OrderItemRepository::class);

        $this->sessionMock = $this->createMock(SessionInterface::class);
        $this->userMock = $this->createMock(User::class);
        $this->orderMock = $this->createMock(Order::class);
        $this->productMock = $this->createMock(Product::class);
        $this->orderItemMock = $this->createMock(OrderItem::class);

        $this->testClass = new CartManager(
            $this->cartSessionStorageMock,
            $this->securityMock,
            $this->orderFactoryMock,
            $this->entityManagerMock,
            $this->orderItemRepositoryMock
        );
    }

    public function testAddItemToCart(): void
    {
        $this->cartSessionStorageMock->expects($this->once())
            ->method('getCart')
            ->willReturn($this->orderMock);

        $this->securityMock->expects($this->never())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->orderFactoryMock->expects($this->never())
            ->method('create')
            ->with($this->userMock)
            ->willReturn($this->orderMock);

        $this->cartSessionStorageMock->expects($this->once())
            ->method('setCart')
            ->with($this->orderMock);

        $this->orderItemRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'order' => $this->orderMock,
                'product' => $this->productMock
            ])
            ->willReturn($this->orderItemMock);

        $this->orderItemMock->expects($this->once())
            ->method('getQuantity')
            ->willReturn(1.0);

        $this->orderItemMock->expects($this->once())
            ->method('setQuantity')
            ->with(2);

        $this->orderFactoryMock->expects($this->never())
            ->method('createItem')
            ->with($this->productMock, 1, $this->orderMock)
            ->willReturn($this->orderItemMock);


        $this->entityManagerMock->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive([$this->orderMock],
                [$this->orderItemMock]);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->testClass->addItemToCart($this->productMock, 1);
        $this->assertEquals($this->orderMock, $result);
    }

    public function testAddItemToCartIfItemNull(): void
    {
        $this->cartSessionStorageMock->expects($this->once())
            ->method('getCart')
            ->willReturn(null);

        $this->securityMock->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->orderFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->userMock)
            ->willReturn($this->orderMock);

        $this->cartSessionStorageMock->expects($this->once())
            ->method('setCart')
            ->with($this->orderMock);

        $this->orderItemRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'order' => $this->orderMock,
                'product' => $this->productMock
            ])
            ->willReturn(null);

        $this->orderItemMock->expects($this->never())
            ->method('getQuantity')
            ->willReturn(1.0);

        $this->orderItemMock->expects($this->never())
            ->method('setQuantity')
            ->with(2);

        $this->orderFactoryMock->expects($this->once())
            ->method('createItem')
            ->with($this->productMock, 1, $this->orderMock)
            ->willReturn($this->orderItemMock);


        $this->entityManagerMock->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive([$this->orderMock],
                [$this->orderItemMock]);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->testClass->addItemToCart($this->productMock, 1);
        $this->assertEquals($this->orderMock, $result);
    }

    public function testSetCurrentCart(): void
    {
        $this->cartSessionStorageMock->expects($this->once())
            ->method('getCart')
            ->willReturn($this->orderMock);

        $this->securityMock->expects($this->never())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->orderFactoryMock->expects($this->never())
            ->method('create')
            ->with($this->userMock)
            ->willReturn($this->orderMock);

        $this->cartSessionStorageMock->expects($this->once())
            ->method('setCart')
            ->with($this->orderMock);

        $result = $this->testClass->setCurrentCart();
        $this->assertEquals($this->orderMock, $result);
    }

    public function testClearCurrentCart(): void
    {
        $this->cartSessionStorageMock->expects($this->once())
            ->method('getCart')
            ->willReturn($this->orderMock);

        $this->securityMock->expects($this->never())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->orderFactoryMock->expects($this->never())
            ->method('create')
            ->with($this->userMock)
            ->willReturn($this->orderMock);

        $this->orderItemRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['order' => $this->orderMock])
            ->willReturn([$this->orderItemMock]);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->orderItemMock);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $this->testClass->clearCart();
        $this->assertEquals($this->orderMock, $result);
    }
}
