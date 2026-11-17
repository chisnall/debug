<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

if (! function_exists('squiz')) {
    /**
     * @param mixed ...$entries
     * @return bool
     */
    function squiz(...$entries): bool
    {
        // Init
        $comment = null;
        $xdebug = null;
        $terminated = false;
        $htmlDump = null;

        // Process entries
        foreach ($entries as $index => $entry) {
            // Look for comment
            if (is_string($entry) && strncasecmp($entry, 'comment:', 8) === 0) {
                $comment = preg_replace('/^comment:/i', '', $entry);
                unset($entries[$index]);
                continue;
            }

            // Look for Xdebug trace file
            if (is_string($entry) && strncasecmp($entry, 'xdebug:', 7) === 0) {
                $xdebug = preg_replace('/^xdebug:/i', '', $entry);
                unset($entries[$index]);
                continue;
            }

            // Look for called from squizd()
            if ($entry === '__TERMINATED__') {
                $terminated = true;
                unset($entries[$index]);
            }
        }

        // Get backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        // Check if this was called from squizd() function
        if ($terminated) {
            // Determine file and line calling this
            $file = $backtrace[1]['file'] ?? 'unknown';
            $line = $backtrace[1]['line'] ?? 'unknown';
        } else {
            // Determine file and line calling this
            $file = $backtrace[0]['file'] ?? 'unknown';
            $line = $backtrace[0]['line'] ?? 'unknown';
        }

        // Get class and function calling this
        $class = $backtraceEntry = $backtrace[1]['class'] ?? null;
        $function = $backtraceEntry = $backtrace[1]['function'] ?? null;

        // Get trace string by throwing an exception
        $traceString = (new \Exception())->getTraceAsString();

        // Determine user and project
        $fileArray = explode('/', trim($file, '/'));
        $user = $fileArray[3];
        $project = $fileArray[4];

        // For home Docker container
        if (gethostname() === 'debug') {
            $user = 'app';
        }

        // Determine log directory
        $logDirectory = "/rlg/web/log/$user/debug/entries";

        // Create log directory
        if ( ! is_dir($logDirectory)) {
            mkdir($logDirectory);
            chmod($logDirectory, 0770);
        }

        // Determine log file
        $id = (int) (microtime(true) * 1_000_000);
        $logFile = "$logDirectory/$id.log";

        // Process entries
        foreach ($entries as $index => $entry) {
            // Add spacer for 2nd and subsequent entries
            if ($index > 0) {
                $htmlDump .= '<div class="spacer"></div>';
            }

            // Convert entry to Symfony VarDumper HTML
            $cloner = new VarCloner;
            $dumper = new HtmlDumper;
            $cloned = $cloner->cloneVar($entry);
            ob_start();
            $dumper->dump($cloned);
            $htmlDump .= ob_get_clean();
        }

        // Get request data
        $requestPath = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $requestContentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $requestGet = $_GET ?? [];
        $requestPost = $_POST ?? [];

        // Detect Ajax POST request
        if ($requestMethod === 'POST' && stripos($requestContentType, 'application/json') === 0) {
            $requestPost = json_decode(file_get_contents('php://input'), true);
        }

        // Set output
        $output = [
            'id' => $id,
            'path' => $logFile,
            'datetime' => date('Y-m-d H:i:s'),
            'file' => $file,
            'line' => $line,
            'class' => $class,
            'function' => $function,
            'comment' => $comment,
            'xdebug' => $xdebug,
            'terminated' => $terminated,
            'trace' => $traceString,
            'dump' => base64_encode($htmlDump),
            'request' => [
                'path' => $requestPath,
                'method' => $requestMethod,
                'get' => $requestGet,
                'post' => $requestPost,
            ],
        ];

        // Output file
        file_put_contents($logFile, serialize($output));

        return true;
    }
}

if (! function_exists('squizd')) {
    /**
     * @param mixed ...$entries
     * @return bool
     */
    function squizd(...$entries): bool
    {
        // Add entry
        squiz('__TERMINATED__', ...$entries);

        // Terminate
        exit;
    }
}

if (! function_exists('trace')) {
    /**
     * @return void
     */
    function trace(int $level = 20): void
    {
        // Check if trace is enabled in the cookie
        $traceEnabled = isset($_COOKIE['XDEBUG_TRACE']) && $_COOKIE['XDEBUG_TRACE'] === '1';

        if (! $traceEnabled) {
            return;
        }

        // Check level
        if ($level < 1) {
            $level = 1;
        }

        // Get file calling this
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $file = $backtrace[0]['file'] ?? 'unknown';

        // Determine user
        $fileArray = explode('/', trim($file, '/'));
        $user = $fileArray[3];

        // For home Docker container
        if (gethostname() === 'debug') {
            $user = 'app';
        }

        // Determine trace directory
        $traceDirectory = "/rlg/web/log/$user/debug/xdebug";

        // Create trace directory
        if ( ! is_dir($traceDirectory)) {
            mkdir($traceDirectory);
            chmod($traceDirectory, 0770);
        }

        // Determine trace file name and path
        $id = (int) (microtime(true) * 1_000_000);
        $traceFile = "trace.$id";
        $tracePath = "$traceDirectory/$traceFile.xt";

        // Determine Xdebug version
        // The ini setting are different between version 2 and 3
        $xdebugVersion = phpversion('xdebug');

        // Set xdebug ini settings
        if (version_compare($xdebugVersion, '3.0.0', '>=')) {
            // Version 3
            ini_set('xdebug.output_dir', $traceDirectory);
            ini_set('xdebug.trace_output_name', $traceFile);
        } else {
            // Version 2
            ini_set('xdebug.collect_params', '0');
            ini_set('xdebug.collect_return', '0');
            ini_set('xdebug.collect_assignments', '0');
            ini_set('xdebug.show_mem_delta', '0');
            ini_set('xdebug.trace_format', '0');   // compact format
            ini_set('xdebug.trace_output_dir', $traceDirectory);
            ini_set('xdebug.trace_output_name', $traceFile);
        }

        // Determine the grep string exclude based on the trace level
        $traceGrepString = str_repeat(' ', $level * 2) . '     -> ';

        // Exclude array
        $exclude = [
            $traceGrepString,
            'AutoLoader::',
            'AutoLoader->',
            '/rlg/web/app/lchisnall/debug-lchisnall',
            'abs()',
            'array_fill()',
            'array_key_exists()',
            'array_keys()',
            'array_search()',
            'assert_options()',
            'auto_include_once()',
            'basename()',
            'ceil()',
            'chr()',
            'class_exists()',
            'constant()',
            'date()',
            'date_default_timezone_set()',
            'define()',
            'defined()',
            'dirname()',
            'error_reporting()',
            'explode()',
            'file_exists()',
            'get_include_path()',
            'getmypid()',
            'gmdate()',
            'gzcompress()',
            'gzuncompress()',
            'header()',
            'htmlentities()',
            'htmlspecialchars()',
            'implode()',
            'in_array()',
            'ini_get()',
            'ini_set()',
            'is_array()',
            'is_dir()',
            'is_null()',
            'is_numeric()',
            'is_readable()',
            'is_string()',
            'is_subclass_of()',
            'ltrim()',
            'mb_internal_encoding()',
            'md5()',
            'memory_get_peak_usage()',
            'number_format()',
            'parse_ini_file()',
            'preg_match()',
            'preg_match_all()',
            'preg_quote()',
            'preg_replace()',
            'rand()',
            'removeNastyChars()',
            'reset()',
            'realpath()',
            'rtrim()',
            'serialize()',
            'session_id()',
            'set_time_limit()',
            'str_replace()',
            'stripos()',
            'strpos()',
            'strrpos()',
            'strtolower()',
            'strtoupper()',
            'strtr()',
            'substr()',
            'time()',
            'trim()',
            'unserialize()',
        ];

        // Exclude string for grep
        $excludeString = implode('\|', $exclude);

        // Run trace
        xdebug_start_trace();

        // Run on shutdown
        register_shutdown_function(function () use ($excludeString, $tracePath) {
            // Stop trace
            xdebug_stop_trace();

            // Reduce size of trace file by removing various lines
            shell_exec("/usr/bin/grep -v '$excludeString' $tracePath | /usr/bin/sed 's|/rlg/web/app/lchisnall/integro-lchisnall||g' > $tracePath.tmp && /usr/bin/mv $tracePath.tmp $tracePath");

            // Create a squiz entry with the trace file path
            squiz("xdebug:$tracePath");
        });
    }
}
