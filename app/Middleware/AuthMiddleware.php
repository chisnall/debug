<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Middleware;
use App\Exception\ForbiddenException;

class AuthMiddleware extends Middleware
{
    protected array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute(): void
    {
        // Check if access token is enabled
        $accessTokenEnabled = getConfig('debug/token/enabled');

        if (!$accessTokenEnabled) {
            return;
        }

        // Get access token from config and cookie token
        $accessTokenValue = getConfig('debug/token/value');
        $cookieTokenValue = app()->request()->getCookies()['token'] ?? null;

        // Check token
        if ($accessTokenValue !== $cookieTokenValue) {
            // Set HTTP status code
            app()->response()->setStatusCode(403);

            // Determine output
            if (app()->controller()->json()) {
                echo json_encode([
                    'status' => 403,
                ]);
            } else {
                echo "<h1>403 Forbidden</h1>";
                echo "<p>You do not have permission to access this page.</p>";
            }

            die;
        }
    }
}
