<?php
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
} 
?>