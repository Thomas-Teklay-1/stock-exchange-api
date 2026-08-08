<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StockCategoryRepository;
use App\Repositories\StockRepository;
use InvalidArgumentException;
use RuntimeException;

class StockService
{
    public function __construct(
        private StockRepository $stocks,
        private StockCategoryRepository $categories
    ) {
    }

    /**
     * Retrieve all non-archived stocks.
     */
    public function getAllStocks(): array
    {
        return $this->stocks->findAll();
    }

    /**
     * Retrieve a stock by ID.
     */
    public function getStock(int $stockId): array
    {
        if ($stockId <= 0) {
            throw new InvalidArgumentException(
                'Invalid stock ID.'
            );
        }

        $stock = $this->stocks->findById($stockId);

        if ($stock === null) {
            throw new RuntimeException(
                'Stock not found.'
            );
        }

        return $stock;
    }

    /**
     * Retrieve all stocks belonging to a seller.
     */
    public function getSellerStocks(int $sellerId): array
    {
        if ($sellerId <= 0) {
            throw new InvalidArgumentException(
                'Invalid seller ID.'
            );
        }

        return $this->stocks->findBySellerId($sellerId);
    }

    /**
     * Create a new stock.
     */
    public function createStock(
        int $sellerId,
        array $data
    ): int {
        $this->validateSellerId($sellerId);

        $validated = $this->validateStockData($data);

        $category = $this->categories->findById(
            $validated['category_id']
        );

        if ($category === null) {
            throw new InvalidArgumentException(
                'Selected stock category does not exist.'
            );
        }

        return $this->stocks->create([
            'seller_id' => $sellerId,
            'category_id' => $validated['category_id'],
            'stock_name' => $validated['stock_name'],
            'description' => $validated['description'],
            'quantity_total' => $validated['quantity_total'],
            'quantity_available' => $validated['quantity_total'],
            'price_per_share' => $validated['price_per_share'],
            'image_path' => $validated['image_path'],
            'status' => 'active',
        ]);
    }

    /**
     * Update an existing stock.
     */
    public function updateStock(
        int $stockId,
        int $sellerId,
        array $data
    ): bool {
        $this->validateSellerId($sellerId);

        $stock = $this->stocks->findById($stockId);

        if ($stock === null) {
            throw new RuntimeException(
                'Stock not found.'
            );
        }

        if ((int) $stock['seller_id'] !== $sellerId) {
            throw new RuntimeException(
                'You are not authorized to modify this stock.'
            );
        }

        if ($stock['status'] === 'archived') {
            throw new InvalidArgumentException(
                'Archived stocks cannot be modified.'
            );
        }

        $validated = $this->validateStockData(
            $data
        );

        $category = $this->categories->findById(
            $validated['category_id']
        );

        if ($category === null) {
            throw new InvalidArgumentException(
                'Selected stock category does not exist.'
            );
        }

        return $this->stocks->update(
            $stockId,
            [
                'category_id' => $validated['category_id'],
                'stock_name' => $validated['stock_name'],
                'description' => $validated['description'],
                'quantity_total' => $validated['quantity_total'],
                'quantity_available' => $stock['quantity_available'],
                'price_per_share' => $validated['price_per_share'],
                'image_path' => $validated['image_path'],
                'status' => $validated['status'],
            ]
        );
    }

    /**
     * Delete a stock owned by a seller.
     *
     * For now this performs a database deletion.
     * Trading-related lifecycle rules will be tightened
     * when the trading system is implemented.
     */
    public function deleteStock(
        int $stockId,
        int $sellerId
    ): bool {
        $this->validateSellerId($sellerId);

        $stock = $this->stocks->findById($stockId);

        if ($stock === null) {
            throw new RuntimeException(
                'Stock not found.'
            );
        }

        if ((int) $stock['seller_id'] !== $sellerId) {
            throw new RuntimeException(
                'You are not authorized to delete this stock.'
            );
        }

        if ($stock['status'] === 'archived') {
            throw new InvalidArgumentException(
                'Stock is already archived.'
            );
        }

        return $this->stocks->delete($stockId);
    }

    /**
     * Validate seller ID.
     */
    private function validateSellerId(
        int $sellerId
    ): void {
        if ($sellerId <= 0) {
            throw new InvalidArgumentException(
                'Invalid seller ID.'
            );
        }
    }

    /**
     * Validate and normalize stock data.
     */
    private function validateStockData(
        array $data
    ): array {
        $categoryId = (int) ($data['category_id'] ?? 0);

        $stockName = trim(
            (string) ($data['stock_name'] ?? '')
        );

        $description = isset($data['description'])
            ? trim((string) $data['description'])
            : null;

        $quantityTotal = filter_var(
            $data['quantity_total'] ?? null,
            FILTER_VALIDATE_INT
        );

        $price = $data['price_per_share'] ?? null;

        $imagePath = isset($data['image_path'])
            ? trim((string) $data['image_path'])
            : null;

        if ($categoryId <= 0) {
            throw new InvalidArgumentException(
                'A valid stock category is required.'
            );
        }

        if ($stockName === '') {
            throw new InvalidArgumentException(
                'Stock name is required.'
            );
        }

        if (strlen($stockName) > 150) {
            throw new InvalidArgumentException(
                'Stock name cannot exceed 150 characters.'
            );
        }

        if (
            $quantityTotal === false ||
            $quantityTotal <= 0
        ) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        if (
            !is_numeric($price) ||
            (float) $price <= 0
        ) {
            throw new InvalidArgumentException(
                'Price per share must be greater than zero.'
            );
        }

        return [
            'category_id' => $categoryId,
            'stock_name' => $stockName,
            'description' => $description !== ''
                ? $description
                : null,
            'quantity_total' => $quantityTotal,
            'price_per_share' => number_format(
                (float) $price,
                2,
                '.',
                ''
            ),
            'image_path' => $imagePath !== ''
                ? $imagePath
                : null,
            'status' => $data['status'] ?? 'active',
        ];
    }
}