<?php
/**
 * Dependency services.
 */
$di->set('csrf', function() use ($di) {
    return $di->newInstance('aura\web\Csrf', array(
        'secret'  => 'you must change me for each project',
        'user_id' => $di->get('auth')->get_id,
    ));
});

$di->set('web_context', function() use ($di) {
    return $di->newInstance('aura\web\Context', array(
        'globals' => $_GLOBALS,
        'csrf'    => $di->get('csrf')
    ));
});