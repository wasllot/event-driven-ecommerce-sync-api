<?php

namespace App\DTOs;

readonly class ProductData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $reference,
        public float $price,
        public int $stock,
        public bool $active,
        public array $attributes = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            reference: $data['reference'],
            price: (float) $data['price'],
            stock: (int) $data['stock'],
            active: (bool) $data['active'],
            attributes: $data['attributes'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'reference' => $this->reference,
            'price' => $this->price,
            'stock' => $this->stock,
            'active' => $this->active,
            'attributes' => $this->attributes,
        ];
    }
}
