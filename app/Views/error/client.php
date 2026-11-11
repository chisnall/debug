<?php
/** @var array $params */

declare(strict_types=1);

$title = 'Error';
$exception = $params["exception"];
$code = $params["code"];
$message = $params["message"];
$file = $params["file"];
$line = $params["line"];

// Default show trace
$trace = false;

// Check for exception object
if (is_object($exception)) {
    // Get class name and set heading
    $className = get_class($exception);

    // Check for database error
    if ($className == 'PDOException') {
        $className = 'Database Error';
    }

    // Check for client errors
    if ($code >= 400 && $code <= 499) {
        // Set heading
        $heading = "Error / $code";
    } else {
        // Set heading and trace
        $heading = $className;
        $trace = true;
    }
} else {
    // Set heading
    $heading = 'Error';
}

// Show heading
echo "<h1>$heading</h1>\n";
echo "<p>" . $exception->getMessage() . "</h1>\n";
