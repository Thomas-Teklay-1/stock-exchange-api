<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;

Application::bootstrap();

$userRepository = new UserRepository();
$sessionRepository = new SessionRepository();

$authenticationService = new AuthenticationService(
    $userRepository,
    $sessionRepository
);

$email = 'auth-test@example.com';

try {
    /*
     * Registration
     *
     * If the test user already exists from a previous test,
     * registration will fail because email addresses must be unique.
     */
    $existingUser = $userRepository->findByEmail($email);

    if ($existingUser !== null) {
        echo "Test user already exists. Skipping registration.\n\n";
    } else {
        $user = $authenticationService->register(
            role: 'buyer',
            name: 'Authentication Test',
            email: $email,
            password: 'TestPassword123!'
        );

        echo "Registration successful.\n";
        print_r($user->toArray());
        echo "\n";
    }

    /*
     * Authentication / Login
     */
    $authenticationResult = $authenticationService->authenticate(
        email: $email,
        password: 'TestPassword123!'
    );

    echo "Authentication successful.\n";

    echo "\nAuthenticated user:\n";
    print_r(
        $authenticationResult['user']->toArray()
    );

    echo "\nSession token:\n";
    echo $authenticationResult['token'] . "\n";

    echo "\nExpires at:\n";
    echo $authenticationResult['expires_at'] . "\n";

    /*
     * Session validation
     */
    echo "\nTesting session validation...\n";

    $sessionUser = $authenticationService->getUserBySessionToken(
        $authenticationResult['token']
    );

    if ($sessionUser !== null) {
        echo "Session validation successful.\n";

        print_r($sessionUser->toArray());
    } else {
        echo "Session validation failed.\n";
    }

    /*
     * Logout
     */
    echo "\nTesting logout...\n";

    $logoutSuccessful = $authenticationService->logout(
        $authenticationResult['token']
    );

    echo $logoutSuccessful
        ? "Logout successful.\n"
        : "Logout failed.\n";

    /*
     * Verify that the session was actually invalidated.
     */
    echo "\nVerifying session invalidation...\n";

    $sessionUser = $authenticationService->getUserBySessionToken(
        $authenticationResult['token']
    );

    echo $sessionUser === null
        ? "Session correctly invalidated.\n"
        : "Session is still active.\n";

} catch (\Throwable $exception) {
    echo "Authentication test failed:\n";
    echo $exception->getMessage() . "\n";
}