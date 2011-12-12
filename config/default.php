<?php
/**
 * Package prefix for autoloader.
 */
$loader->addPrefix('Aura\Web\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/Aura/Web');

/**
 * Constructor params.
 */
$di->params['Aura\Web\Context']['globals'] = $GLOBALS;

$di->params['Aura\Web\Context']['globals']['server'] = $_SERVER;

$di->params['Aura\Web\Page'] = array(
    'context'  => $di->lazyGet('web_context'),
    'response' => $di->lazyGet('web_response_transfer'),
);

/**
 * Dependency services.
 */
$di->set('web_context', function() use ($di) {
    return $di->newInstance('Aura\Web\Context');
});

$di->set('web_response_transfer', function() use ($di) {
    return $di->newInstance('Aura\Web\ResponseTransfer');
});
