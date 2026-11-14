<?php

declare(strict_types=1);

/** @var array $params */
$xdebug = $params['xdebug'];
$trace = $params['trace'];
$request = $params['request'];
?>

<div class="heading">
    <div class="upper">
        <div class="text">
            <img class="theme-icon theme-icon-light" src="/images/icon-light.png" alt="Debug icon">
            <img class="theme-icon theme-icon-dark" src="/images/icon-dark.png" alt="Debug icon" aria-hidden="true">
            <h1>Xdebug Trace</h1>
        </div>
    </div>
</div>

<div id="xdebug-container">
    <div class="xdebug-request">
        <pre><?= $request['method'] ?></pre>
        <pre><?= parse_url($request['path'], PHP_URL_PATH) ?></pre>
        <?php if($request['get']): ?>
            <pre><?= json_encode($request['get'], JSON_PRETTY_PRINT) ?></pre>
        <?php elseif($request['post']): ?>
            <pre><?= json_encode($request['post'], JSON_PRETTY_PRINT) ?></pre>
        <?php endif; ?>
    </div>

    <pre><?= htmlspecialchars($trace, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre>
</div>
