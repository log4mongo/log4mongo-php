<?php
/**
 * The New BSD License
 *
 * Copyright (c) 2010, Vladimir Gorej
 * All rights reserved.
 *	
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *	
 *		* Redistributions of source code must retain the above copyright notice,
 *			 this list of conditions and the following disclaimer.
 *		 
 *		 * Redistributions in binary form must reproduce the above copyright notice,
 *			 this list of conditions and the following disclaimer in the documentation
 *			 and/or other materials provided with the distribution.
 *			 
 *		 * The name of author may not be used to endorse or promote products derived from
 *			 this software without specific prior written permission.
 *	
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category tests
 * @license New BSD License
 * @author char0n (Vladimir Gorej) <gorej@mortality.sk>	 
 * @package log4php
 * @subpackage appenders
 * @version 1.5b1
*/

class LoggerAppenderMongoDBLayoutTest extends PHPUnit_Framework_TestCase {

	protected static $appender;
	protected static $layout;
	protected static $event;
	
	public static function setUpBeforeClass() {
		self::$appender         = new LoggerAppenderMongoDBLayout('mongo_appender');
		self::$layout           = new LoggerLayoutBson();
		self::$appender->setLayout(self::$layout);
		self::$event            = new LoggerLoggingEvent("LoggerAppenderMongoDBLayoutTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
	}
	
	public static function tearDownAfterClass() {
		self::$appender->close();
		self::$appender = null;
		self::$layout = null;
		self::$event = null;
	}	
	
	protected function setUp() {
		if (extension_loaded('mongo') == false) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		}
	}
	
	
	public function testMongoDB() {		
		self::$appender->activateOptions();
		$mongo  = self::$appender->getConnection();
		$db     = $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');		
		$collection = $db->selectCollection('logs');
				
		self::$appender->append(self::$event);		
	
		$this->assertNotEquals($collection->findOne(), null, 'Collection should return one record');
	}
    	
}
?>