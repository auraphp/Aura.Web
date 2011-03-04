<?php
/**
 * Dependency services.
 */
$di->set('csrf', function() use ($di) {
    return $di->newInstance('aura\web\Csrf', array(
        'secert'  => 'you must change me for each project',
        'user_id' => $di->get('auth')->get_id,
    ));
});

$di->set('web_context', function() use ($di) {
    return $di->newInstance('aura\web\Context', array(
        'get'    => $_GET,
        'post'   => $_POST,
        'server' => $_SERVER,
        'cookie' => $_COOKIE,
        'env'    => $_ENV,
        'files'  => $_FILES,
        'csrf'   => $di->get('csrf')
    ));
});