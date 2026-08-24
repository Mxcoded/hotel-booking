<?php

// Stale application caches (config / routes / events) freeze values from
// .env and override the environment variables set in phpunit*.xml, which
// silently breaks tests (e.g. CSRF 419s because APP_ENV stops being
// "testing"). Remove them before the app boots so phpunit.xml wins.
foreach (['config.php', 'routes-v7.php', 'routes-v8.php', 'routes.php', 'events.php', 'app.php'] as $cacheFile) {
    $path = __DIR__ . '/../bootstrap/cache/' . $cacheFile;

    if (is_file($path)) {
        @unlink($path);
    }
}

require __DIR__ . '/../vendor/autoload.php';
