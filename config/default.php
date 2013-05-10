<?php
/**
 * Loader
 */
$loader->add('Aura\Web\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Services
 */
$di->set('web_accept',   $di->lazyNew('Aura\Web\Accept'));
$di->set('web_context',  $di->lazyNew('Aura\Web\Context'));
$di->set('web_response', $di->lazyNew('Aura\Web\Response'));

/**
 * Aura\Web\Accept
 */
$di->params['Aura\Web\Accept']['server'] = $_SERVER;

/**
 * Aura\Web\Context
 */
$di->params['Aura\Web\Context']['globals'] = $GLOBALS;

/**
 * Aura\Web\Controller\AbstractPage
 */
$di->params['Aura\Web\Controller\AbstractPage'] = [
    'context'  => $di->lazyGet('web_context'),
    'accept'   => $di->lazyGet('web_accept'),
    'response' => $di->lazyGet('web_response'),
    'signal'   => $di->lazyGet('signal_manager'),
    'renderer' => $di->lazyNew('Aura\Framework\Web\Renderer\AuraViewTwoStep'),
];
