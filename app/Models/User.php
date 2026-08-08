<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        private int $userId,
        private string $role,
        private string $name,
        private string $email,
        private ?string $phone = null,
        private ?string $profilePicture = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'role' => $this->role,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_picture' => $this->profilePicture,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}