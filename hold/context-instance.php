<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;
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
