<?php

namespace App\Services;

class LogEntries
{
    public static function entries(?array $ids = null)
    {
        // Init
        $logEntries = [];

        // Get entries path
        $entriesDirectory = getConfig('directories/entries');

        // Get files
        $files = array_diff(scandir($entriesDirectory), ['.', '..']);

        foreach ($files as $file) {
            // Read file contents
            $contents = file_get_contents("$entriesDirectory/$file");

            // Unserialize file contents
            $entry = unserialize($contents);

            // Check if specific IDs have been provided to this method
            if (!$ids || ($ids && in_array($entry['id'], $ids))) {
                //  Add to entries
                $logEntries[] = $entry;
            }
        }

        return $logEntries;
    }

    public static function entryById($id)
    {
        // Get entries path
        $entriesDirectory = getConfig('directories/entries');

        // Set entry file path
        $entryPath = "$entriesDirectory/$id.log";

        if (!file_exists($entryPath)) {
            return null;
        }

        // Read file contents
        $contents = file_get_contents($entryPath);

        // Unserialize file contents
        $entry = unserialize($contents);

        return $entry;
    }

    public static function ids(array $entries)
    {
        // Init log IDs
        $logIds = [];

        // Process log files
        foreach ($entries as $entry) {
            $logIds[] = $entry['id'];
        }

        // Return log IDs
        return $logIds;
    }
}
