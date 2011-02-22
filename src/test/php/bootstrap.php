<?php
date_default_timezone_set('Europe/Bratislava');

$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require_once $basePath.'lib'.DIRECTORY_SEPARATOR.'log4php'.DIRECTORY_SEPARATOR.'Logger.php';
require_once $basePath.'src'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'appenders'.DIRECTORY_SEPARATOR.'LoggerAppenderMongoDB.php';
require_once $basePath.'src'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'appenders'.DIRECTORY_SEPARATOR.'LoggerAppenderMongoDBLayout.php';
require_once $basePath.'src'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'LoggerLoggingEventBsonifier.php';
require_once $basePath.'src'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'LoggerLayoutBson.php';

/**
 * Helper classes
 */
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	class TestingException extends Exception {}
} else {
	class TestingException extends Exception {
				
		protected $cause;
				
		public function __construct($message = '', $code = 0, Exception $ex = null) {
			
			parent::__construct($message, $code);
				$this->cause = $ex;
			}
				
			public function getPrevious() {
				return $this->cause;
			}
	}
}
?>