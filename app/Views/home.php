<?php

declare(strict_types=1);

/** @var array $params */
$logIds = $params['logIds'];
$logEntries = $params['logEntries'];
?>

<div class="heading">
    <div class="upper">
        <div class="text">
    <!--        <svg fill="currentColor" viewBox="2 5 20 14">-->
    <!--            <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"></path>-->
    <!--        </svg>-->
            <img class="theme-icon theme-icon-light" src="/images/icon-light.png" alt="Debug icon">
            <img class="theme-icon theme-icon-dark" src="/images/icon-dark.png" alt="Debug icon" aria-hidden="true">
            <h1>Debug</h1>
        </div>

        <div class="centre">
            <div id="notify-1" class="notify flip">
                🔥
            </div>

            <div class="icons">
                <div id="icon-system" class="icon">
                    <svg viewBox="0 0 28 28" fill="none">
                        <path d="M7.5 8.5C7.5 7.94772 7.94772 7.5 8.5 7.5H19.5C20.0523 7.5 20.5 7.94772 20.5 8.5V16.5C20.5 17.0523 20.0523 17.5 19.5 17.5H8.5C7.94772 17.5 7.5 17.0523 7.5 16.5V8.5Z" stroke="currentColor"></path>
                        <path d="M7.5 8.5C7.5 7.94772 7.94772 7.5 8.5 7.5H19.5C20.0523 7.5 20.5 7.94772 20.5 8.5V14.5C20.5 15.0523 20.0523 15.5 19.5 15.5H8.5C7.94772 15.5 7.5 15.0523 7.5 14.5V8.5Z" stroke="currentColor"></path>
                        <path d="M16.5 20.5V17.5H11.5V20.5M16.5 20.5H11.5M16.5 20.5H17.5M11.5 20.5H10.5" stroke="currentColor" stroke-linecap="round"></path>
                    </svg>
                </div>

                <div id="icon-light" class="icon">
                    <svg viewBox="0 0 28 28" fill="none">
                        <circle cx="14" cy="14" r="3.5" stroke="currentColor"></circle>
                        <path d="M14 8.5V6.5" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M17.889 10.1115L19.3032 8.69727" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M19.5 14L21.5 14" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M17.889 17.8885L19.3032 19.3027" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M14 21.5V19.5" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M8.69663 19.3029L10.1108 17.8887" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M6.5 14L8.5 14" stroke="currentColor" stroke-linecap="round"></path>
                        <path d="M8.69663 8.69711L10.1108 10.1113" stroke="currentColor" stroke-linecap="round"></path>
                    </svg>
                </div>

                <div id="icon-dark" class="icon">
                    <svg viewBox="0 0 28 28" fill="none">
                        <path d="M10.5 9.99914C10.5 14.1413 13.8579 17.4991 18 17.4991C19.0332 17.4991 20.0176 17.2902 20.9132 16.9123C19.7761 19.6075 17.109 21.4991 14 21.4991C9.85786 21.4991 6.5 18.1413 6.5 13.9991C6.5 10.8902 8.39167 8.22304 11.0868 7.08594C10.7089 7.98159 10.5 8.96597 10.5 9.99914Z" stroke="currentColor" stroke-linejoin="round"></path>
                        <path d="M16.3561 6.50754L16.5 5.5L16.6439 6.50754C16.7068 6.94752 17.0525 7.29321 17.4925 7.35607L18.5 7.5L17.4925 7.64393C17.0525 7.70679 16.7068 8.05248 16.6439 8.49246L16.5 9.5L16.3561 8.49246C16.2932 8.05248 15.9475 7.70679 15.5075 7.64393L14.5 7.5L15.5075 7.35607C15.9475 7.29321 16.2932 6.94752 16.3561 6.50754Z" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M20.3561 11.5075L20.5 10.5L20.6439 11.5075C20.7068 11.9475 21.0525 12.2932 21.4925 12.3561L22.5 12.5L21.4925 12.6439C21.0525 12.7068 20.7068 13.0525 20.6439 13.4925L20.5 14.5L20.3561 13.4925C20.2932 13.0525 19.9475 12.7068 19.5075 12.6439L18.5 12.5L19.5075 12.3561C19.9475 12.2932 20.2932 11.9475 20.3561 11.5075Z" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
            </div>

            <div id="notify-2" class="notify">
                🔥
            </div>
        </div>

        <div class="buttons">
            <button class="auto" type="button" id="auto-button" name="auto-button" title="Auto refresh">
                <svg width="14" height="14" fill="none" viewBox="3.97 3 16.06 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"></path>
                </svg>
            </button>

            <button class="clear" type="button" id="clear-button" name="clear-button" title="Clear entries">Clear</button>
        </div>
    </div>
</div>

<div id="entries-container">
    <?php if(count($logEntries) === 0): ?>
        <pre>No entries.</pre>
    <?php endif; ?>

    <?php foreach ($logEntries as $logEntry): ?>
        <div id="entry-<?= $logEntry['id'] ?>" class="entry">
            <?php if($logEntry['terminated']): ?>
                <div class="terminated">
                    <div class="datetime">
                        <pre class="datetime"><?= $logEntry['datetime'] ?></pre>

                        <!-- These icons are from Flowbite: https://flowbite.com/icons/ -->

                        <div class="trace-icon" data-id="<?= $logEntry['id'] ?>">
                            <svg fill="none" viewBox="4 2 16 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                            </svg>
                        </div>

                        <div class="delete-icon" data-id="<?= $logEntry['id'] ?>">
                            <svg fill="none" viewBox="4 2 16 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"></path>
                            </svg>
                        </div>
                    </div>

                    <pre>terminated</pre>
                </div>
            <?php else: ?>
                <div class="line">
                    <div class="datetime">
                        <pre class="datetime"><?= $logEntry['datetime'] ?></pre>

                        <!-- These icons are from Flowbite: https://flowbite.com/icons/ -->

                        <?php if($logEntry['xdebug']): ?>
                            <div class="disabled-icon">
                                <svg fill="none" viewBox="4 2 16 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="trace-icon" data-id="<?= $logEntry['id'] ?>">
                                <svg fill="none" viewBox="4 2 16 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <div class="delete-icon" data-id="<?= $logEntry['id'] ?>">
                            <svg fill="none" viewBox="4 2 16 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($logEntry['xdebug']): ?>
                <div class="line">
                    <pre>Xdebug trace</pre>
                </div>

                <div class="line">
                    <pre><a class="xdebug" href="/xdebug?id=<?= $logEntry['id'] ?>" target="_blank"><?= basename($logEntry['xdebug']) ?></a></pre>
                </div>
            <?php else: ?>
                <div class="line">
                    <pre><?= $logEntry['file'] . ':' . $logEntry['line'] ?></pre>
                </div>

                <?php if($logEntry['comment']): ?>
                    <div class="line">
                        <pre class="italic"><?= $logEntry['comment'] ?></pre>
                    </div>
                <?php endif; ?>

                <div id="trace-<?= $logEntry['id'] ?>" class="trace">
                    <pre><?= $logEntry['trace'] ?></pre>
                </div>

                <div class="vardump">
                    <?php echo base64_decode($logEntry['entry']); ?>
                </div>
            <?php endif; ?>

            <hr>
        </div>
    <?php endforeach; ?>
</div>

<form>
    <input type="hidden" id="logIds" name="logIds" value="<?php echo implode(',', $logIds) ?>">
    <input type="hidden" id="debugToken" name="debugToken" value="<?= getConfig('debug/token/value') ?>">
    <input type="hidden" id="debugPollingInterval" name="debugPollingInterval" value="<?= getConfig('debug/pollingInterval') ?>">
</form>
