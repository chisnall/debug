<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Services\LogEntries;

class XdebugController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function view(Request $request): string
    {
        $query = $request->getQuery();

        $id = $query['id'] ?? null;

        if (!$id) {
            response()->redirect('/');
        }

        $entry = LogEntries::entry($id);

        if (!$entry) {
            response()->redirect('/');
        }

        $xdebug = $entry['xdebug'];
        $request = $entry['request'];

        $trace = file_get_contents($xdebug);

        $params = [
            'id' => $id,
            'xdebug' => $xdebug,
            'trace' => $trace,
            'request' => $request,
        ];

        // Render view
        return self::render('xdebug', $params);
    }
}
