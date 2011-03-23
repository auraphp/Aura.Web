<?php
/**
 * Constructor params.
 */
$di->params['aura\web\Context'] = array(
    'globals' => $GLOBALS,
    'csrf'    => $di->lazyGet('web_csrf'),
    'agents'  => array(
        'mobile'=>array(
            'Android',
            'BlackBerry',
            'Blazer',
            'Brew',
            'IEMobile',
            'iPad',
            'iPhone',
            'iPod',
            'KDDI',
            'Kindle',
            'Maemo',
            'MOT-', // Motorola Internet Browser
            'Nokia',
            'SymbianOS',
            'UP\.Browser', // Openwave Mobile Browser
            'UP\.Link', 
            'Opera Mobi',
            'Opera Mini',        
            'webOS', // Palm devices
            'Playstation',
            'PS2',
            'Windows CE',
            'Polaris',
            'SEMC',
            'NetFront',
            'Fennec'
        ),
        'crawler'=>array(
            'Ask',
            'Baidu',
            'Google',        
            'Googlebot',
            'AdsBot',
            'gsa-crawler',
            'adidxbot', 
            'librabot',
            'llssbot',
            'bingbot',
            'Danger hiptop',
            'MSMOBOT',
            'MSNBot',
            'MSR-ISRCCrawler',
            'MSRBOT',
            'Vancouver',
            'Y!J',
            'Yahoo',
            'slurp',        
            'mp3Spider',
            'Scooter',
            'Y!OASIS',
            'YRL_ODP_CRAWLER',
            'Yandex',
            'Fast',
            'Lycos',
            'heritrix',
            'ia_archiver',
            'InternetArchive',
            'archive.org_bot',
            'Nutch',
            'WordPress',
            'Wget'
        )
    )
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
