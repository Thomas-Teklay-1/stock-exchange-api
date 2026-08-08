<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\SessionRepositoryInterface;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

class AuthenticationService
{
    public function __construct(
        private UserRepositoryInterface $users, 
        private SessionRepositoryInterface $sessions
    ) {
    }

    public function register(
        string $role,
        string $name,
        string $email,
        string $password,
        ?string $phone = null
    ): User {
        $role = strtolower(trim($role));
        $name = trim($name);
        $email = strtolower(trim($email));

        if (!in_array($role, ['buyer', 'seller'], true)) {
            throw new InvalidArgumentException(
                'Invalid user role.'
            );
        }

        if ($name === '') {
            throw new InvalidArgumentException(
                'Name is required.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'A valid email address is required.'
            );
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException(
                'Password must contain at least 8 characters.'
            );
        }

        if ($this->users->findByEmail($email) !== null) {
            throw new InvalidArgumentException(
                'An account with this email already exists.'
            );
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            throw new RuntimeException(
                'Unable to securely hash password.'
            );
        }

        $userId = $this->users->create([
            'role' => $role,
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'phone' => $phone,
            'profile_picture' => null,
        ]);

        $user = $this->users->findById($userId);

        if ($user === null) {
            throw new RuntimeException(
                'User was created but could not be retrieved.'
            );
        }

        return $this->toUserModel($user);
    }

    public function authenticate(
        string $email,
        string $password
    ): array {
        $email = strtolower(trim($email));

        $user = $this->users->findByEmail($email);

        if ($user === null) {
            throw new InvalidArgumentException(
                'Invalid email or password.'
            );
        }

        if (!password_verify(
            $password,
            $user['password_hash']
        )) {
            throw new InvalidArgumentException(
                'Invalid email or password.'
            );
        }

        $token = bin2hex(random_bytes(32));

        $expiresAt = date(
            'Y-m-d H:i:s',
            time() + (60 * 60 * 24 * 7)
        );

        $this->sessions->create(
            userId: (int) $user['user_id'],
            token: $token,
            expiresAt: $expiresAt
        );

        return [
            'user' => $this->toUserModel($user),
            'token' => $token,
            'expires_at' => $expiresAt
        ];
    }

    public function logout(string $token): bool
    {
        return $this->sessions->deleteByToken($token);
    }

    private function toUserModel(array $data): User
    {
        return new User(
            userId: (int) $data['user_id'],
            role: $data['role'],
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            profilePicture: $data['profile_picture'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    public function getUserBySessionToken(
        string $token
    ): ?User {
        $session = $this->sessions->findByToken($token);

        if ($session === null) {
            return null;
        }

        if (strtotime($session['expires_at']) <= time()) {
            $this->sessions->deleteByToken($token);

            return null;
        }

        $this->sessions->updateLastUsed(
            (int) $session['session_id']
        );

        $user = $this->users->findById(
            (int) $session['user_id']
        );

        if ($user === null) {
            return null;
        }

        return $this->toUserModel($user);
    }
}