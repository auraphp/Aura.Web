<?php
/**
 * Constructor params.
 */
$di->params['aura\web\Context']['globals'] = $GLOBALS;

$di->params['aura\web\Context']['globals']['server'] = $_SERVER;

$di->params['aura\web\Page'] = array(
    'signal'   => $di->lazyGet('signal_manager'),
    'context'  => $di->lazyGet('web_context'),
    'response' => $di->lazyGet('web_response_transfer'),
);

$di->params['aura\web\ControllerFactory'] = array(
    'forge' => $di->getForge(),
);

/**
 * Dependency services.
 */
$di->set('web_csrf', function() use ($di) {
    return $di->newInstance('aura\web\Csrf');
});

$di->set('web_context', function() use ($di) {
    return $di->newInstance('aura\web\Context');
});

$di->set('web_response_transfer', function() use ($di) {
    return $di->newInstance('aura\web\ResponseTransfer');
});

$di->set('web_controller_factory', function() use ($di) {
    return $di->newInstance('aura\web\ControllerFactory');
});
