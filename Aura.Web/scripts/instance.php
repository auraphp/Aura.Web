<?php
namespace Aura\Web;

require dirname(__DIR__) . '/autoload.php';

return new Request(new Request\PropertyFactory($GLOBALS));
