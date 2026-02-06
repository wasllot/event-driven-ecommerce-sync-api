<?php

namespace Tests\Unit\DTOs;

use App\DTOs\OrderData;
use PHPUnit\Framework\TestCase;

class OrderDataTest extends TestCase
{
    public function test_can_create_from_array(): void
    {
        $data = [
            'id' => 100,
            'reference' => 'ORD-123',
            'customer_email' => 'test@example.com',
            'total' => 150.50,
            'items' => [['ref' => 'P1', 'qty' => 1]],
            'status' => 'paid',
        ];

        $dto = OrderData::fromArray($data);

        $this->assertInstanceOf(OrderData::class, $dto);
        $this->assertEquals(100, $dto->id);
        $this->assertEquals('ORD-123', $dto->reference);
        $this->assertEquals('test@example.com', $dto->customerEmail);
        $this->assertEquals(150.50, $dto->total);
        $this->assertCount(1, $dto->items);
        $this->assertEquals('paid', $dto->status);
    }

    public function test_can_convert_to_array(): void
    {
        $dto = new OrderData(
            id: 200,
            reference: 'ORD-456',
            customerEmail: 'jane@example.com',
            total: 200.00,
            items: [],
            status: 'pending'
        );

        $array = $dto->toArray();

        $this->assertEquals(200, $array['id']);
        $this->assertEquals('ORD-456', $array['reference']);
        $this->assertEquals('jane@example.com', $array['customer_email']);
        $this->assertEquals(200.00, $array['total']);
        $this->assertEquals('pending', $array['status']);
    }
}
