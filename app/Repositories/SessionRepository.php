<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\SessionRepositoryInterface;

class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    public function create(
        int $userId,
        string $token,
        string $expiresAt
    ): int {
        return $this->insert(
            "
            INSERT INTO user_sessions (
                user_id,
                session_token,
                expires_at
            )
            VALUES (
                :user_id,
                :session_token,
                :expires_at
            )
            ",
            [
                'user_id' => $userId,
                'session_token' => $token,
                'expires_at' => $expiresAt
            ]
        );
    }

    public function findByToken(string $token): ?array
    {
        return $this->fetchOne(
            "
            SELECT
                session_id,
                user_id,
                session_token,
                expires_at,
                last_used_at,
                created_at
            FROM user_sessions
            WHERE session_token = :session_token
            LIMIT 1
            ",
            [
                'session_token' => $token
            ]
        );
    }

    public function updateLastUsed(int $sessionId): bool
    {
        $statement = $this->query(
            "
            UPDATE user_sessions
            SET last_used_at = CURRENT_TIMESTAMP
            WHERE session_id = :session_id
            ",
            [
                'session_id' => $sessionId
            ]
        );

        return $statement->rowCount() > 0;
    }

    public function deleteByToken(string $token): bool
    {
        $statement = $this->query(
            "
            DELETE FROM user_sessions
            WHERE session_token = :session_token
            ",
            [
                'session_token' => $token
            ]
        );

        return $statement->rowCount() > 0;
    }

    public function deleteAllForUser(int $userId): bool
    {
        $statement = $this->query(
            "
            DELETE FROM user_sessions
            WHERE user_id = :user_id
            ",
            [
                'user_id' => $userId
            ]
        );

        return $statement->rowCount() > 0;
    }
}