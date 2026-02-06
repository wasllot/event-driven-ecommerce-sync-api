<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ProductData;
use PHPUnit\Framework\TestCase;

class ProductDataTest extends TestCase
{
    public function test_can_create_from_array(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test Product',
            'reference' => 'REF-001',
            'price' => 99.99,
            'stock' => 10,
            'active' => true,
            'attributes' => ['color' => 'red'],
        ];

        $dto = ProductData::fromArray($data);

        $this->assertInstanceOf(ProductData::class, $dto);
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('Test Product', $dto->name);
        $this->assertEquals('REF-001', $dto->reference);
        $this->assertEquals(99.99, $dto->price);
        $this->assertEquals(10, $dto->stock);
        $this->assertTrue($dto->active);
        $this->assertEquals(['color' => 'red'], $dto->attributes);
    }

    public function test_can_convert_to_array(): void
    {
        $dto = new ProductData(
            id: 1,
            name: 'Test Product',
            reference: 'REF-001',
            price: 50.00,
            stock: 5,
            active: false,
            attributes: []
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test Product', $array['name']);
        $this->assertEquals('REF-001', $array['reference']);
        $this->assertEquals(50.00, $array['price']);
        $this->assertEquals(5, $array['stock']);
        $this->assertFalse($array['active']);
        $this->assertEmpty($array['attributes']);
    }
}
