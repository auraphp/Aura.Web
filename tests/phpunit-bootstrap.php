<?php
spl_autoload_register(function($class) {
    $dir   = dirname(__DIR__);
    $ns    = str_replace('.', '\\', basename($dir)) . '\\';
    
    if (substr($class, 0, strlen($ns)) !== $ns) {
        return false;
    }
    
    $class = str_replace($ns, '', $class);
    $file  = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    
    $src = $dir . DIRECTORY_SEPARATOR . 'src'. DIRECTORY_SEPARATOR . $file;
    if (file_exists($src)) {
        require $src;
        return true;
    }
    
    $tests = $dir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $file;
    if (file_exists($tests)) {
        require $tests;
        return true;
    }
    
    return false;
});
