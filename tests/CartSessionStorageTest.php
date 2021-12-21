<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Storage\CartSessionStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorageTest extends TestCase
{
    private CartSessionStorage $testClass;

    private MockObject $requestStackMock;
    private MockObject $orderRepositoryMock;
    private MockObject $sessionMock;
    private MockObject $userMock;
    private MockObject $orderMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepository::class);

        $this->sessionMock = $this->createMock(SessionInterface::class);
        $this->userMock = $this->createMock(User::class);
        $this->orderMock = $this->createMock(Order::class);

        $this->testClass = new CartSessionStorage(
            $this->requestStackMock,
            $this->orderRepositoryMock
        );
    }

    public function testGetCart(): void
    {
        $this->requestStackMock->expects($this->once())
            ->method('getSession')
            ->willReturn($this->sessionMock);

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->with(CartSessionStorage::CART_USER)
            ->willReturn($this->userMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'user' => $this->userMock,
                'status' => Order::STATUS_CART
            ])
            ->willReturn($this->orderMock);

        $result = $this->testClass->getCart();
        $this->assertEquals($this->orderMock, $result);
    }

    public function testSetCart(): void
    {
        $this->requestStackMock->expects($this->exactly(2))
            ->method('getSession')
            ->willReturn($this->sessionMock);

        $this->orderMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->orderMock->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->sessionMock->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive([CartSessionStorage::CART_ID, 1],
                [CartSessionStorage::CART_USER, $this->userMock]);

        $this->testClass->setCart($this->orderMock);
    }
}
