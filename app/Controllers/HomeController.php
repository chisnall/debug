<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Services\LogEntries;

class HomeController extends Controller
{
    private array $logEntries;

    public function __construct()
    {
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());

        // Get log entries
        $this->logEntries = LogEntries::entries();
    }

    public function home(Request $request): string
    {
        // Check request
        //if ($request->isGet()) {
        //    // ...
        //} elseif ($request->isPost()) {
        //    // ...
        //}

        // Set view params
        $params = [
            'logIds' => LogEntries::ids($this->logEntries),
            'logEntries' => $this->logEntries
        ];

        // Render view
        return self::render('home', $params);
    }
}
