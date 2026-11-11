<?php
/** @var array $params */

declare(strict_types=1);

echo "<h1>$shortClassName</h1>\n";
echo "<pre>File: $exceptionFile</pre>\n";
echo "<pre>Line: $exceptionLine</pre>\n";
echo "<pre>Message:\n$exceptionMessage</pre>\n";
echo "<pre>Trace:\n$exceptionTraceString</pre>\n";
