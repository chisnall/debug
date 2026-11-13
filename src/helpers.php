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

        // Get trace string by throwing an exception
        $traceString = (new \Exception())->getTraceAsString();

        // Determine user and project
        $fileArray = explode('/', trim($file, '/'));
        $user = $fileArray[3];
        $project = $fileArray[4];

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

        // Set output
        $output = [
            'id' => $id,
            'datetime' => date('Y-m-d H:i:s'),
            'file' => $file,
            'line' => $line,
            'comment' => $comment,
            'xdebug' => $xdebug,
            'terminated' => $terminated,
            'trace' => $traceString,
            'entry' => base64_encode($htmlDump),
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
    function trace(int $level = 1): void
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

        // Determine meta
        $meta = [
            'path' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'get' => $_GET,
            'post' => $_POST,
        ];

        // Get file calling this
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $file = $backtrace[0]['file'] ?? 'unknown';

        // Determine user
        $fileArray = explode('/', trim($file, '/'));
        $user = $fileArray[3];

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
        $metaPath = "$tracePath.meta";

        // Set xdebug ini settings
        ini_set('xdebug.collect_params', '0');
        ini_set('xdebug.collect_return', '0');
        ini_set('xdebug.collect_assignments', '0');
        ini_set('xdebug.show_mem_delta', '0');
        ini_set('xdebug.trace_format', '0');   // compact format
        ini_set('xdebug.trace_output_dir', $traceDirectory);
        ini_set('xdebug.trace_output_name', $traceFile);

        // Run trace
        xdebug_start_trace();

        // Create a squiz entry with the trace file path
        squiz("xdebug:$tracePath");

        // Write meta
        file_put_contents($metaPath, serialize($meta));

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
            'array_keys()',
            'array_search()',
            'assert_options()',
            'auto_include_once()',
            'basename()',
            'ceil()',
            'chr()',
            'constant()',
            'date()',
            'date_default_timezone_set()',
            'define()',
            'defined()',
            'dirname()',
            'error_reporting()',
            'explode()',
            'file_exists()',
            'gmdate()',
            'gzcompress()',
            'gzuncompress()',
            'header()',
            'implode()',
            'in_array()',
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
            'number_format()',
            'preg_match()',
            'preg_match_all()',
            'preg_quote()',
            'preg_replace()',
            'reset()',
            'realpath()',
            'rtrim()',
            'serialize()',
            'session_id()',
            'set_time_limit()',
            'str_replace()',
            'stripos()',
            'strpos()',
            'strtolower()',
            'strtoupper()',
            'substr()',
            'time()',
            'trim()',
            'unserialize()',
        ];

        // Exclude string for grep
        $excludeString = implode('\|', $exclude);

        // Reduce size of trace file by removing various lines
        register_shutdown_function(function () use ($excludeString, $tracePath) {
            shell_exec("/usr/bin/grep -v '$excludeString' $tracePath | /usr/bin/sed 's|/rlg/web/app/lchisnall/integro-lchisnall||g' > $tracePath.tmp && /usr/bin/mv $tracePath.tmp $tracePath");
        });
    }
}
