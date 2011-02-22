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

class LoggerAppenderMongoDBTest extends PHPUnit_Framework_TestCase {
		
	protected static $appender;
	protected static $event;
	
	public static function setUpBeforeClass() {
		self::$appender         = new LoggerAppenderMongoDB('mongo_appender');
		self::$event            = new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
	}
	
	public static function tearDownAfterClass() {
		self::$appender->close();
		self::$appender = null;
		self::$event = null;
	}
	
	protected function setUp() {
		if (extension_loaded('mongo') == false) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		}
	}	
	
	public function test__construct() {
		$appender = new LoggerAppenderMongoDB('mongo_appender');
		$this->assertTrue($appender instanceof LoggerAppenderMongoDB);
	}
	
	public function testSetGetHost() {
		$expected = 'mongodb://localhost';
		self::$appender->setHost($expected);		
		$result = self::$appender->getHost();
		$this->assertEquals($expected, $result, 'Host doesn\'t match expted value');
	}
	
	public function testSetGetHostMongoPrefix() {
		$expected = 'mongodb://localhost';
		self::$appender->setHost('localhost');		
		$result = self::$appender->getHost();
		$this->assertEquals($expected, $result, 'Host doesn\'t match expted value');
	}
	
	public function testSetPort() {
		$expected = 27017;
		self::$appender->setPort($expected);		
		$result = self::$appender->getPort();
		$this->assertEquals($expected, $result, 'Port doesn\'t match expted value');
	}

	public function testGetPort() {
		$expected = 27017;
		self::$appender->setPort($expected);		
		$result = self::$appender->getPort();
		$this->assertEquals($expected, $result, 'Port doesn\'t match expted value');
	}
	
	public function testSetDatabaseName() {
		$expected = 'log4php_mongodb';
		self::$appender->setDatabaseName($expected);		
		$result	= self::$appender->getDatabaseName();
		$this->assertEquals($expected, $result, 'Database name doesn\'t match expted value');
	}
	
	public function testGetDatabaseName() {
		$expected = 'log4php_mongodb';
		self::$appender->setDatabaseName($expected);		
		$result	= self::$appender->getDatabaseName();
		$this->assertEquals($expected, $result, 'Database name doesn\'t match expted value');
	}		 
	
	public function testSetCollectionName() {
		$expected = 'logs';
		self::$appender->setCollectionName($expected);		
		$result = self::$appender->getCollectionName();
		$this->assertEquals($expected, $result, 'Collection name doesn\'t match expted value');
	}
	
	public function testGetCollectionName() {
		$expected = 'logs';
		self::$appender->setCollectionName($expected);		
		$result = self::$appender->getCollectionName();
		$this->assertEquals($expected, $result, 'Collection name doesn\'t match expted value');
	}	 
	
	public function testSetUserName() {
		$expected = 'char0n';
		self::$appender->setUserName($expected);		
		$result = self::$appender->getUserName();
		$this->assertEquals($expected, $result, 'UserName doesn\'t match expted value');
	}
	
	public function testGetUserName() {
		$expected = 'char0n';
		self::$appender->setUserName($expected);		
		$result		= self::$appender->getUserName();
		$this->assertEquals($expected, $result, 'UserName doesn\'t match expted value');
	}					 
	
	public function testSetPassword() {
		$expected = 'secret pass';
		self::$appender->setPassword($expected);		
		$result		= self::$appender->getPassword();
		$this->assertEquals($expected, $result, 'Password doesn\'t match expted value');
	}
	
	public function testGetPassword() {
		$expected = 'secret pass';
		self::$appender->setPassword($expected);		
		$result		= self::$appender->getPassword();
		$this->assertEquals($expected, $result, 'Password doesn\'t match expted value');
	} 
	
	/**
	* @expectedException LoggerException 
	*/
	public function testActivateOptions() {
		self::$appender->activateOptions();		
		$this->fail('Appender options should not activate');
	}						
	
	public function testActivateOptionsNoCredentials() {
		try {
			self::$appender->setUserName(null);
			self::$appender->setPassword(null);
			self::$appender->activateOptions();	
		} catch (Exception $ex) {
			$this->fail('Activating appender options was not successful');
		}		
	}		
	
    public function testAppend() {
		self::$appender->append(self::$event);
	}
    
	public function testMongoDB() {		
		$mongo  = self::$appender->getConnection();
		$db     = $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');		
		$collection = $db->selectCollection('logs');
				
		self::$appender->append(self::$event);

		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	}     

	public function testMongoDBException() {				
		$mongo	= self::$appender->getConnection();
		$db			= $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');				
		$collection = $db->selectCollection('logs');
			
		$throwable = new TestingException('exception1');
								
		self::$appender->append(new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage", microtime(true), $throwable));				 
		
		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	}		
		
	public function testMongoDBInnerException() {		
		$mongo	= self::$appender->getConnection();
		$db			= $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');				
		$collection = $db->selectCollection('logs');
				
		$throwable1 = new TestingException('exception1');
		$throwable2 = new TestingException('exception2', 0, $throwable1);
								
		self::$appender->append(new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage", microtime(true), $throwable2));				
		
		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	}
    
	public function testClose() {
		self::$appender->close();
	}
}
?>