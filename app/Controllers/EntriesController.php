<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Services\LogEntries;

class EntriesController extends Controller
{
    private ?string $token;

    public function __construct()
    {
        parent::__construct();

        // Set JSON response
        $this->json = true;

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());

        // Set response header
        header('Content-Type: application/json');

        // Get token from header
        $this->token = $this->headers['X-DEBUG-TOKEN'] ?? null;

        // Get log entries
        $this->logEntries = LogEntries::entries();
    }

    public function clearLog(): string
    {
        $entriesPath = getConfig('directories/entries');

        deleteAllFiles($entriesPath);

        return json_encode([
            'status' => 200,
        ]);
    }

    public function deleteEntry(): string
    {
        $entryId = $this->data['entryId'];

        $entriesPath = getConfig('directories/entries');

        deleteFile("$entriesPath/$entryId.log");

        return json_encode([
            'status' => 200,
        ]);
    }

    public function logEntries(): string
    {
        $logIds = $this->data['logIds'];

        $logEntries = LogEntries::entries($logIds);

        return json_encode([
            'status' => 200,
            'data' => $logEntries,
        ]);
    }

    public function logIds(): string
    {
        $logIds = LogEntries::ids($this->logEntries);

        return json_encode([
            'status' => 200,
            'data' => $logIds,
        ]);
    }
}
