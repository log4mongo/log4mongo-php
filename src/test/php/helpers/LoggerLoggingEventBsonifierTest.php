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
 * @subpackage helpers
 * @version 1.5b1
*/

class LoggerLoggingEventBsonifierTest extends PHPUnit_Framework_TestCase {
	
	protected static $logger;
	protected static $bsonifier;
	
	public static function setUpBeforeClass() {
		self::$logger    = Logger::getLogger('test.Logger');
		self::$bsonifier = new LoggerLoggingEventBsonifier();
	}	
	
	public static function tearDownAfterClass() {
		self::$logger     = null;
		self::$bsonifier = null;
	}
	
	public function testBsonifySimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('WARN', $bsonifiedEvent['level']);
		$this->assertEquals('test message', $bsonifiedEvent['message']);
		$this->assertEquals('test.Logger', $bsonifiedEvent['loggerName']);
	}
	
	public function testBsonifyLocationInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('NA', $bsonifiedEvent['fileName']);		
		$this->assertEquals('getLocationInformation', $bsonifiedEvent['method']);
		$this->assertEquals('NA', $bsonifiedEvent['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $bsonifiedEvent['className']);
	}
	
	public function testBsonifyThrowableInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);
	}
	
	public function testBsonifyThrowableInfoWithInnerException() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new TestingException('test exeption', 1, new Exception('test exception inner', 2))
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);		
	}

	public function testIsThreadInteger() {
                $event = new LoggerLoggingEvent(
                        'testFqcn',
                        self::$logger,
                        LoggerLevel::getLevelWarn(),
                        'test message'
                );
                $bsonifiedEvent = self::$bsonifier->bsonify($event);
		$this->assertTrue(is_int($bsonifiedEvent['thread']));
	}

        public function testIsLocationInfoLineNumberIntegerOrNA() {
                $event = new LoggerLoggingEvent(
                        'testFqcn',
                        self::$logger,
                        LoggerLevel::getLevelWarn(),
                        'test message'
                );
                $bsonifiedEvent = self::$bsonifier->bsonify($event);
                $this->assertTrue(is_int($bsonifiedEvent['lineNumber']) || $bsonifiedEvent['lineNumber'] == 'NA');
        }

        public function testIsThrowableInfoExceptionCodeInteger() {
                $event = new LoggerLoggingEvent(
                        'testFqcn',
                        self::$logger,
                        LoggerLevel::getLevelWarn(),
                        'test message',
                        microtime(true),
                        new TestingException('test exeption', 1, new Exception('test exception inner', 2))
                );
                $bsonifiedEvent = self::$bsonifier->bsonify($event);
                $this->assertTrue(is_int($bsonifiedEvent['exception']['code']));
        }
} 
?>
