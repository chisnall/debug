<?php

declare(strict_types=1);

namespace App\Core;

use Exception;

final class ErrorHandler
{
    public static function framework($errorLevel, $errorString, $errorFile, $errorLine)
    {
        // Increment errors property
        app()->setProperty('errors', app()->getProperty('errors') + 1);

        // Set layout config key
        $layoutKey = 'page/error/php/layout';
        $viewKey = 'page/error/php/view';

        // Get layout value
        $layoutFile = getConfig($layoutKey, true);
        if (!$layoutFile) {
            throw new Exception("Error layout configuration is missing: $layoutKey");
        } else {
            // Check layout file is present
            $layoutPath = BASE_PATH . "/app/Views/layouts/$layoutFile.php";
            if (!file_exists($layoutPath)) {
                throw new Exception("Error layout file is missing: $layoutPath");
            }
        }

        // Set view config key
        $viewFile = getConfig($viewKey, true);
        if (!$viewFile) {
            throw new Exception("View layout configuration is missing: $viewKey");
        } else {
            // Check view file is present
            $viewPath = BASE_PATH . "/app/Views/$viewFile.php";
            if (!file_exists($viewPath)) {
                throw new Exception("Error view file is missing: $viewPath");
            }
        }

        // Set title
        $title = 'Error';

        // Get view
        ob_start();
        include $viewPath;
        $viewContent = ob_get_clean();

        // Get layout
        ob_start();
        include $layoutPath;
        $layoutContent = ob_get_clean();

        // Replace title and content
        $layoutContent = str_replace('{{title}}', $title, $layoutContent);
        $layoutContent = str_replace('{{content}}', $viewContent, $layoutContent);

        // Output
        echo $layoutContent;

        // Log
        logger()->logError($errorLevel, $errorString, $errorFile, $errorLine);
    }

    public static function ajax($errorLevel, $errorString, $errorFile, $errorLine): bool
    {
        // Log
        logger()->logError($errorLevel, $errorString, $errorFile, $errorLine);

        // Don't output error
        return true;
    }
}
