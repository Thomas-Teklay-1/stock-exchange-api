<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class StockRepository extends BaseRepository
{
    public function findAll(): array
    {
        return $this->fetchAll(
            'SELECT
                stock_id,
                seller_id,
                category_id,
                stock_name,
                description,
                quantity_total,
                quantity_available,
                price_per_share,
                image_path,
                status,
                created_at,
                updated_at
             FROM stocks
             WHERE status != :status
             ORDER BY created_at DESC',
            [
                'status' => 'archived',
            ]
        );
    }

    public function findById(int $stockId): ?array
    {
        return $this->fetchOne(
            'SELECT
                stock_id,
                seller_id,
                category_id,
                stock_name,
                description,
                quantity_total,
                quantity_available,
                price_per_share,
                image_path,
                status,
                created_at,
                updated_at
             FROM stocks
             WHERE stock_id = :stock_id
             LIMIT 1',
            [
                'stock_id' => $stockId,
            ]
        );
    }

    public function findBySellerId(int $sellerId): array
    {
        return $this->fetchAll(
            'SELECT
                stock_id,
                seller_id,
                category_id,
                stock_name,
                description,
                quantity_total,
                quantity_available,
                price_per_share,
                image_path,
                status,
                created_at,
                updated_at
             FROM stocks
             WHERE seller_id = :seller_id
             ORDER BY created_at DESC',
            [
                'seller_id' => $sellerId,
            ]
        );
    }

    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO stocks (
                seller_id,
                category_id,
                stock_name,
                description,
                quantity_total,
                quantity_available,
                price_per_share,
                image_path,
                status
            ) VALUES (
                :seller_id,
                :category_id,
                :stock_name,
                :description,
                :quantity_total,
                :quantity_available,
                :price_per_share,
                :image_path,
                :status
            )',
            [
                'seller_id' => $data['seller_id'],
                'category_id' => $data['category_id'],
                'stock_name' => $data['stock_name'],
                'description' => $data['description'] ?? null,
                'quantity_total' => $data['quantity_total'],
                'quantity_available' => $data['quantity_available'],
                'price_per_share' => $data['price_per_share'],
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]
        );
    }

    public function update(
        int $stockId,
        array $data
    ): bool {
        $statement = $this->query(
            'UPDATE stocks
             SET
                category_id = :category_id,
                stock_name = :stock_name,
                description = :description,
                quantity_total = :quantity_total,
                quantity_available = :quantity_available,
                price_per_share = :price_per_share,
                image_path = :image_path,
                status = :status
             WHERE stock_id = :stock_id',
            [
                'stock_id' => $stockId,
                'category_id' => $data['category_id'],
                'stock_name' => $data['stock_name'],
                'description' => $data['description'] ?? null,
                'quantity_total' => $data['quantity_total'],
                'quantity_available' => $data['quantity_available'],
                'price_per_share' => $data['price_per_share'],
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status'],
            ]
        );

        return $statement->rowCount() > 0;
    }

    public function delete(int $stockId): bool
    {
        $statement = $this->query(
            'DELETE FROM stocks
             WHERE stock_id = :stock_id',
            [
                'stock_id' => $stockId,
            ]
        );

        return $statement->rowCount() > 0;
    }
}