<?php

declare(strict_types=1);

use App\Core\Application;

function exceptionHandler(Throwable $exception) {
    http_response_code(500);
    echo "<div style=\"font-size: 120%;\">\n";
    echo "<pre style=\"font-weight: bold;\">Error</pre>\n";
    echo "<pre>Unable to start framework.</pre>\n";
    echo "<pre>" . $exception->getMessage() . "</pre>\n";
    echo "<pre>" . $exception->getTraceAsString() . "</pre>\n";
    echo "</div>\n";
    exit;
}

set_exception_handler('exceptionHandler');

define('APP_START', microtime(true));
define('APP_DEBUG', false);
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Boot application
(new Application())->run();
