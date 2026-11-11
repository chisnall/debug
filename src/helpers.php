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
