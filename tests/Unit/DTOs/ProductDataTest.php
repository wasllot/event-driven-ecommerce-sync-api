<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ProductData;
use PHPUnit\Framework\TestCase;

class ProductDataTest extends TestCase
{
    public function test_it_can_be_created_from_array_with_all_fields()
    {
        $data = [
            'id' => 123,
            'name' => 'Test Product',
            'reference' => 'REF-123',
            'price' => 99.99,
            'stock' => 10,
            'active' => true,
            'attributes' => ['color' => 'red'],
            'description' => '<p>Full Description</p>',
            'description_short' => '<p>Short</p>',
            'categories' => ['Electronics', 'Gadgets'],
            'images' => ['http://example.com/img1.jpg'],
            'weight' => 1.5,
            'ean13' => '1234567890123'
        ];

        $dto = ProductData::fromArray($data);

        $this->assertEquals(123, $dto->id);
        $this->assertEquals('<p>Full Description</p>', $dto->description);
        $this->assertEquals(['Electronics', 'Gadgets'], $dto->categories);
        $this->assertEquals(1.5, $dto->weight);
        $this->assertEquals('1234567890123', $dto->ean13);
    }

    public function test_it_converts_to_array_correctly()
    {
        $dto = new ProductData(
            id: 1,
            name: 'P1',
            reference: 'R1',
            price: 10.0,
            stock: 5,
            active: true,
            attributes: [],
            description: 'Desc',
            categories: ['Cat1'],
            weight: 2.0
        );

        $array = $dto->toArray();

        $this->assertArrayHasKey('description', $array);
        $this->assertEquals('Desc', $array['description']);
        $this->assertEquals(2.0, $array['weight']);
        $this->assertNull($array['ean13']); // Default null
    }
}
