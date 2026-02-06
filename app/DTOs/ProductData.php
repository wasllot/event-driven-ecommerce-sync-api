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
        public array $attributes = [],
        public ?string $description = null,
        public ?string $descriptionShort = null,
        public array $categories = [],
        public array $images = [],
        public float $weight = 0.0,
        public ?string $ean13 = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            reference: (string) $data['reference'],
            price: (float) $data['price'],
            stock: (int) $data['stock'],
            active: (bool) $data['active'],
            attributes: $data['attributes'] ?? [],
            description: $data['description'] ?? null,
            descriptionShort: $data['description_short'] ?? null,
            categories: $data['categories'] ?? [],
            images: $data['images'] ?? [],
            weight: (float) ($data['weight'] ?? 0.0),
            ean13: $data['ean13'] ?? null
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
            'description' => $this->description,
            'description_short' => $this->descriptionShort,
            'categories' => $this->categories,
            'images' => $this->images,
            'weight' => $this->weight,
            'ean13' => $this->ean13,
        ];
    }
}
