<?php

declare(strict_types=1);

namespace App\Models;

class Stock
{
    public function __construct(
        public readonly int $stockId,
        public readonly int $sellerId,
        public readonly int $categoryId,
        public readonly string $stockName,
        public readonly ?string $description,
        public readonly int $quantityTotal,
        public readonly int $quantityAvailable,
        public readonly float $pricePerShare,
        public readonly ?string $imagePath,
        public readonly string $status,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt
    ) {
    }

    public function toArray(): array
    {
        return [
            'stock_id' => $this->stockId,
            'seller_id' => $this->sellerId,
            'category_id' => $this->categoryId,
            'stock_name' => $this->stockName,
            'description' => $this->description,
            'quantity_total' => $this->quantityTotal,
            'quantity_available' => $this->quantityAvailable,
            'price_per_share' => $this->pricePerShare,
            'image_path' => $this->imagePath,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}