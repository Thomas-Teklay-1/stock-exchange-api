<?php

declare(strict_types=1);

namespace App\Repositories;

class StockCategoryRepository extends BaseRepository
{
    public function findAll(): array
    {
        return $this->fetchAll(
            'SELECT
                category_id,
                category_name
             FROM stock_categories
             ORDER BY category_name ASC'
        );
    }

    public function findById(int $categoryId): ?array
    {
        return $this->fetchOne(
            'SELECT
                category_id,
                category_name
             FROM stock_categories
             WHERE category_id = :category_id
             LIMIT 1',
            [
                'category_id' => $categoryId,
            ]
        );
    }

    public function findByName(string $categoryName): ?array
    {
        return $this->fetchOne(
            'SELECT
                category_id,
                category_name
             FROM stock_categories
             WHERE category_name = :category_name
             LIMIT 1',
            [
                'category_name' => $categoryName,
            ]
        );
    }

    public function create(string $categoryName): int
    {
        return $this->insert(
            'INSERT INTO stock_categories (
                category_name
             ) VALUES (
                :category_name
             )',
            [
                'category_name' => $categoryName,
            ]
        );
    }
}