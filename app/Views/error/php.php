<?php
/** @var array $params */

declare(strict_types=1);

echo "<div class='php-error'>\n";
echo "<h2>PHP error</h2>\n";
echo "<pre>File: $errorFile</pre>\n";
echo "<pre>Line: $errorLine</pre>\n";
echo "<pre>Message:\n$errorString</pre>\n";
echo "</div>\n";
