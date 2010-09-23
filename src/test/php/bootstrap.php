<?php
date_default_timezone_set('Europe/Bratislava');

$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require_once $basePath.'lib'.DIRECTORY_SEPARATOR.'log4php'.DIRECTORY_SEPARATOR.'Logger.php';
require_once $basePath.'src'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'appenders'.DIRECTORY_SEPARATOR.'LoggerAppenderMongoDB.php';

require_once 'PHPUnit/Framework.php';
?>