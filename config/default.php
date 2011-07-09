<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
/**
 * Constructor params.
 */
$di->params['Aura\Web\Context']['globals'] = $GLOBALS;

$di->params['Aura\Web\Context']['globals']['server'] = $_SERVER;

$di->params['Aura\Web\Page'] = array(
    'signal'   => $di->lazyGet('signal_manager'),
    'context'  => $di->lazyGet('web_context'),
    'response' => $di->lazyGet('web_response_transfer'),
);

$di->params['Aura\Web\ControllerFactory'] = array(
    'forge' => $di->getForge(),
);

/**
 * Dependency services.
 */
$di->set('web_csrf', function() use ($di) {
    return $di->newInstance('Aura\Web\Csrf');
});

$di->set('web_context', function() use ($di) {
    return $di->newInstance('Aura\Web\Context');
});

$di->set('web_response_transfer', function() use ($di) {
    return $di->newInstance('Aura\Web\ResponseTransfer');
});

$di->set('web_controller_factory', function() use ($di) {
    return $di->newInstance('Aura\Web\ControllerFactory');
});
