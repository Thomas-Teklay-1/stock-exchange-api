<?php

declare(strict_types=1);

namespace App\Contracts;

interface SessionRepositoryInterface
{
    public function create(
        int $userId,
        string $token,
        string $expiresAt
    ): int;

    public function findByToken(string $token): ?array;

    public function updateLastUsed(int $sessionId): bool;

    public function deleteByToken(string $token): bool;

    public function deleteAllForUser(int $userId): bool;
}