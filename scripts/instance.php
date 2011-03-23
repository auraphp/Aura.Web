<?php
namespace aura\web;
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src.php';

// better way?
if (isset($csrf_secret_key) && isset($csrf_user_id)) {
    $csrf = new Csrf($csrf_secret_key, $csrf_user_id);
} else {
    $csrf = null;
}

if (! isset($agents)) {
    $agents = null;
}

return new Context($GLOBALS, $csrf, $agents);
