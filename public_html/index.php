<?php
$environment = getenv('LH_ENV');

switch ($environment)
{
    case 'development':
        error_reporting(E_ALL);ini_set('display_errors', '1');
        break;
    case 'production':
        error_reporting(0);ini_set('display_errors', '0');
        break;
}

// For ZendDeveloperTools
define('REQUEST_MICROTIME', microtime(true));

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
