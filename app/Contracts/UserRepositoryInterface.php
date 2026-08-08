<?php

declare(strict_types=1);

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function findById(int $userId): ?array;

    public function findByEmail(string $email): ?array;

    public function create(array $userData): int;

    public function update(int $userId, array $userData): bool;

    public function delete(int $userId): bool;
}