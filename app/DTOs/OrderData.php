<?php

namespace App\DTOs;

readonly class OrderData
{
    public function __construct(
        public int $id,
        public string $reference,
        public string $customerEmail,
        public float $total,
        public array $items = [],
        public string $status,
        public array $shippingAddress = [],
        public array $billingAddress = [],
        public int $carrierId = 0,
        public string $module = '',
        public string $currency = 'EUR'
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
            status: $data['status'],
            shippingAddress: $data['shipping_address'] ?? [],
            billingAddress: $data['billing_address'] ?? [],
            carrierId: (int) ($data['carrier_id'] ?? 0),
            module: $data['module'] ?? '',
            currency: $data['currency'] ?? 'EUR'
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
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
            'carrier_id' => $this->carrierId,
            'module' => $this->module,
            'currency' => $this->currency,
        ];
    }
}
