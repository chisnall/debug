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

        $this->page['layout'] = 'xdebug';

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

        $entry = LogEntries::entryById($id);

        if (!$entry) {
            response()->redirect('/');
        }

        $xdebug = $entry['xdebug'];

        $trace = file_get_contents($xdebug);

        $meta = unserialize(file_get_contents("$xdebug.meta"));

        $params = [
            'id' => $id,
            'xdebug' => $xdebug,
            'meta' => $meta,
            'trace' => $trace,
        ];

        // Render view
        return self::render('xdebug', $params);
    }
}
