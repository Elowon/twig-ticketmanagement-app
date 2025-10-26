<?php
// If the request is for an existing file, let PHP serve it directly
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . '/public' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// Otherwise, always load index.php (your main router)
require_once __DIR__ . '/public/index.php';
