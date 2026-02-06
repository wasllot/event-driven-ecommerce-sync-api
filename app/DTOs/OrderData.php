<?php

namespace App\DTOs;

readonly class OrderData
{
    public function __construct(
        public int $id,
        public string $reference,
        public string $customerEmail,
        public float $total,
        public array $items = [], // Array of product references and quantities
        public string $status
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            reference: $data['reference'],
            customerEmail: $data['customer_email'],
            total: (float) $data['total'],
            items: $data['items'] ?? [],
            status: $data['status']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'customer_email' => $this->customerEmail,
            'total' => $this->total,
            'items' => $this->items,
            'status' => $this->status,
        ];
    }
}
