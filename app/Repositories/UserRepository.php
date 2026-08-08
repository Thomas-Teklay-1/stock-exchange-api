<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function findById(int $userId): ?array
    {
        return $this->fetchOne(
            "
            SELECT
                user_id,
                role,
                name,
                email,
                password_hash,
                phone,
                profile_picture,
                created_at,
                updated_at
            FROM users
            WHERE user_id = :user_id
            LIMIT 1
            ",
            [
                'user_id' => $userId
            ]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne(
            "
            SELECT
                user_id,
                role,
                name,
                email,
                password_hash,
                phone,
                profile_picture,
                created_at,
                updated_at
            FROM users
            WHERE email = :email
            LIMIT 1
            ",
            [
                'email' => $email
            ]
        );
    }

    public function create(array $userData): int
    {
        return $this->insert(
            "
            INSERT INTO users (
                role,
                name,
                email,
                password_hash,
                phone,
                profile_picture
            )
            VALUES (
                :role,
                :name,
                :email,
                :password_hash,
                :phone,
                :profile_picture
            )
            ",
            [
                'role' => $userData['role'],
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password_hash' => $userData['password_hash'],
                'phone' => $userData['phone'] ?? null,
                'profile_picture' => $userData['profile_picture'] ?? null
            ]
        );
    }

    public function update(int $userId, array $userData): bool
    {
        $fields = [];
        $parameters = [
            'user_id' => $userId
        ];

        $allowedFields = [
            'name',
            'email',
            'phone',
            'profile_picture',
            'password_hash',
            'role'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $userData)) {
                $fields[] = "{$field} = :{$field}";
                $parameters[$field] = $userData[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "
            UPDATE users
            SET " . implode(', ', $fields) . "
            WHERE user_id = :user_id
        ";

        $statement = $this->query($sql, $parameters);

        return $statement->rowCount() > 0;
    }

    public function delete(int $userId): bool
    {
        $statement = $this->query(
            "
            DELETE FROM users
            WHERE user_id = :user_id
            ",
            [
                'user_id' => $userId
            ]
        );

        return $statement->rowCount() > 0;
    }
}