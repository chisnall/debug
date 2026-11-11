<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\EntriesController;
use App\Controllers\SiteController;

// Home controller
router()->get('/', [HomeController::class, 'home']);
router()->post('/', [HomeController::class, 'home']);

// Entries controller
router()->post('/api/clear', [EntriesController::class, 'clearLog']);
router()->post('/api/delete', [EntriesController::class, 'deleteEntry']);
router()->post('/api/entries', [EntriesController::class, 'logEntries']);
router()->get('/api/ids', [EntriesController::class, 'logIds']);

// Test page
router()->get('/test', [SiteController::class, 'test']);

// Flash test
router()->get('/flash-test', [SiteController::class, 'flashTest']);
