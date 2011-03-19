<?php
/**
 * Constructor params.
 */
$di->params['aura\web\Context'] = array(
    'globals' => $GLOBALS,
    'csrf'    => $di->lazyGet('web_csrf'),
);

$di->params['aura\web\Csrf'] = array(
    'secret'  => 'you must change me for each project',
    // 'user_id' => $di->lazyGet('auth')->get_id,
);

$di->params['aura\web\Page'] = array(
    'signal'   => $di->lazyGet('signal_manager'),
    'context'  => $di->lazyGet('web_context'),
    'response' => $di->lazyGet('web_response'),
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

$di->set('web_response', function() use ($di) {
    return $di->newInstance('aura\web\Response');
});

$di->set('web_page_factory', function() use ($di) {
    return $di->newInstance('aura\web\PageFactory');
});
