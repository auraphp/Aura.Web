<?php

use aura\web\Csrf    as Csrf;
use aura\web\Context as WebContext;

// better way?
if (isset($csrf_secret_key) && isset($csrf_user_id)) {
    $csrf = new Csrf($csrf_secret_key, $csrf_user_id);
} else {
    $csrf = null;
}

$dir = dirname(__DIR__);

require $dir . "/src/Csrf.php";
require $dir . "/src/Context.php";
require $dir . "/src/Exception/InvalidTokenFormat.php";
require $dir . "/src/Exception/Context.php";
return new WebContext($_GET, $_POST, $_SERVER, $_COOKIE, $_ENV, $_FILES, $csrf);