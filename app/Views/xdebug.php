<?php

declare(strict_types=1);

/** @var array $params */
$xdebug = $params['xdebug'];
$meta = $params['meta'];
$trace = $params['trace'];
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
    <div class="xdebug-meta">
        <pre><?= $meta['method'] ?></pre>
        <pre><?= parse_url($meta['path'], PHP_URL_PATH) ?></pre>
        <?php if($meta['get']): ?>
            <pre><?= json_encode($meta['get'], JSON_PRETTY_PRINT) ?></pre>
        <?php elseif($meta['post']): ?>
            <pre><?= json_encode($meta['post'], JSON_PRETTY_PRINT) ?></pre>
        <?php endif; ?>
    </div>

    <pre><?= $trace ?></pre>
</div>
