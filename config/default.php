<?php
/**
 * Package prefix for autoloader.
 */
$loader->add('Aura\Web\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Constructor params.
 */
$di->params['Aura\Web\Context']['globals'] = $GLOBALS;
$di->params['Aura\Web\Accept']['server'] = $_SERVER;

$di->params['Aura\Web\Controller\AbstractPage']['context']  = $di->lazyGet('web_context');
$di->params['Aura\Web\Controller\AbstractPage']['accept']  = $di->lazyGet('web_accept');
$di->params['Aura\Web\Controller\AbstractPage']['response'] = $di->lazyGet('web_response');
$di->params['Aura\Web\Controller\AbstractPage']['signal']   = $di->lazyGet('signal_manager');
$di->params['Aura\Web\Controller\AbstractPage']['renderer'] = $di->lazyNew('Aura\Framework\Web\Renderer\AuraViewTwoStep');

/**
 * Dependency services.
 */
$di->set('web_context', $di->lazyNew('Aura\Web\Context'));
$di->set('web_accept', $di->lazyNew('Aura\Web\Accept'));
$di->set('web_response', $di->lazyNew('Aura\Web\Response'));
