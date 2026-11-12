<?php

declare(strict_types=1);
?>
<!doctype html>
<html id="html" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/icon-light.png" sizes="192x192">
    <title>{{title}}</title>
</head>

<style>
    body { overflow-y: scroll; margin: 15px; font-family: "Roboto", sans-serif; }
    pre { display: block; margin-bottom: 10px; font-family: "JetBrains Mono", monospace; font-size: 14px; font-weight: normal; }
    h1 { font-weight: bold; font-size: 22px; }

    html[data-theme="light"] {
        .php-error { padding: 10px; background: rgb(255 0 0 / 0.15); color: black; border: 2px solid red; border-radius: 10px; }
    }

    /* Dark theme */
    html[data-theme="dark"] {
        .php-error { padding: 10px; background: rgb(255 0 0 / 0.50); color: white; border: 2px solid red; border-radius: 10px; }
    }
</style>

<body class="body-text">

<section id="content">
    {{content}}
</section>

</body>
</html>
