<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Controller;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Exception\FrameworkException;

if (!function_exists('app')) {
    function app(): Application {
        return Application::app();
    }
}

if (!function_exists('controller')) {
    function controller(): Controller {
        return Application::app()->controller();
    }
}

if (!function_exists('loadEnv')) {
    function loadEnv(): array
    {
        $env = [];

        $envFile = BASE_PATH . '/.env';

        // Process .env file
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $envLine) {
            // Skip comments
            if (strpos(trim($envLine), '#') === 0) continue;

            // Get key and value and trim
            [$key, $value] = array_map('trim', explode('=', $envLine, 2));

            $env[$key] = $value;
        }

        return $env;
    }
}

if (!function_exists('logger')) {
    function logger(): Logger {
        return Application::app()->logger();
    }
}

if (!function_exists('request')) {
    function request(): Request {
        return Application::app()->request();
    }
}

if (!function_exists('response')) {
    function response(): Response {
        return Application::app()->response();
    }
}

if (!function_exists('router')) {
    function router(): Router {
        return Application::app()->router();
    }
}

if (!function_exists('session')) {
    function session(): Session {
        return Application::app()->session();
    }
}

if (!function_exists('view')) {
    function view(): View {
        return Application::app()->view();
    }
}

if (!function_exists('camelToSnake')) {
    function camelToSnake(string $string): string {
        // Replace uppercase character with underscore and lowercase the whole string
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}

if (!function_exists('dotToCamel')) {
    function dotToCamel(string $string): string {
        // Check for dot character
        if (strpos($string, '.') !== false) {
            return lcfirst(str_replace('.', '', ucwords(strtolower($string), '.')));
        }

        // Return existing string - this means we can run the method again on an existing camelCase string
        return $string;
    }
}

if (!function_exists('snakeToCamel')) {
    function snakeToCamel(string $string): string {
        // Check for underscore character
        if (strpos($string, '_') !== false) {
            return lcfirst(str_replace('_', '', ucwords(strtolower($string), '_')));
        }

        // Return existing string - this means we can run the method again on an existing camelCase string
        return $string;
    }
}

if (!function_exists('checkConfig')) {
    function checkConfig($key) {
        // !!! do not change the {{value}} style output here to returning "null" or "false"
        // that will break config keys which actually use "null" or "false" for values

        // Get config
        $config = app()->getProperty('config');

        // Turn key into array
        $keyArray = explode('/', $key);

        // Process keys
        foreach ($keyArray as $keyItem) {
            // Confirm array key exists
            if (array_key_exists($keyItem, $config)) {
                // Update config variable
                $config = $config[$keyItem];

                // Check for empty or null
                if ($config === '' || $config === null) {
                    return '{{null}}';
                }
            } else {
                return '{{missing}}';
            }
        }

        // Return
        return $config;
    }
}

if (!function_exists('getConfig')) {
    function getConfig($key, $missing = false) {
        // Check config
        $config = checkConfig($key);

        // Check for missing
        if ($config === '{{missing}}') {
            if ($missing === true) {
                return null;
            }
            throw new FrameworkException("Config is missing: $key");
        }

        // Check for null
        if ($config === '{{null}}') {
            if ($missing === true) {
                return null;
            }
            throw new FrameworkException("Config is null: $key");
        }

        // Return
        return $config;
    }
}

if (!function_exists('getEnvironment')) {
    function getEnvironment(string $key) {
        $value = app()->getProperty('env')[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (strtolower($value) === 'true') {
            return true;
        }

        if (strtolower($value) === 'false') {
            return false;
        }

        return $value;
    }
}

if (!function_exists('includeFile')) {
    function includeFile(string $file, ?string $message = null, ?array $variables = null, $once = false) {
        // Set default message
        if (!$message) {
            $message = "File is missing: $file";
        }

        // Set full path
        $filePath = BASE_PATH . $file;

        // Check path exists
        if (!file_exists($filePath)) {
            throw new FrameworkException($message);
        }

        // Process variables so they are available to the include
        if ($variables) {
            foreach ($variables as $variableKey => $variableValue) {
                $$variableKey = $variableValue;
            }

            // Remove variables
            unset($file);
            unset($message);
            unset($variables);
            unset($variableValue);
            unset($variableKey);
        }

        // Return include
        if ($once) {
            return include_once $filePath;
        } else {
            return include $filePath;
        }
    }
}

if (!function_exists('deleteAllFiles')) {
    function deleteAllFiles(string $directory): void {
        if (!is_dir($directory)) {
            return;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            deleteFile("$directory/$file");
        }
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile(string $file): void {
        if (!file_exists($file)) {
            return;
        }

        unlink($file);
    }
}

if (!function_exists('cleanupSessions')) {
    function cleanupSessions(): void {
        // Get web server user ID
        $httpUserId = posix_getuid();

        // Set current time
        $timeNow = time();

        // Age for deletion
        $fileDeleteAge = 86400; // 24 hours

        // Get session save path
        $sessionSavePath = session_save_path() ?: '/tmp';

        // Get files
        $files = glob("$sessionSavePath/sess_*");

        foreach ($files as $filePath) {
            // Get file mtime
            $fileAge = $timeNow - filemtime($filePath);

            // Get file owner
            $fileOwnerId = fileowner($filePath);

            // Delete old files - we can only delete files that are owned by the web server user
            if ($fileAge > $fileDeleteAge && $fileOwnerId == $httpUserId) {
                deleteFile($filePath);
            }
        }
    }
}

if (!function_exists('traceInfo')) {
    function traceInfo(string $position = "end"): array {
        // Get backtrace
        $debugBacktrace = debug_backtrace();

        // Check for start argument
        if ( $position == "start" ) {
            rsort($debugBacktrace);
        }

        // Set return array
        $debugBacktraceInfo = [];
        $debugBacktraceInfo['file'] = basename($debugBacktrace[0]['file']);
        $debugBacktraceInfo['path'] = $debugBacktrace[0]['file'];
        $debugBacktraceInfo['line'] = $debugBacktrace[0]['line'];

        // Return
        return $debugBacktraceInfo;
    }
}

if (!function_exists('appTime')) {
    function appTime(): void {
        // Calculate application time
        $appTotalTime = number_format((microtime(true) - APP_START) * 1000);

        // Update footer
        echo "<script>$('div#app-time').html('$appTotalTime ms');</script>\n";

    }
}
