<?php
class LoggerMongoDbBsonLayoutTest extends PHPUnit_Framework_TestCase {
	
	protected static $logger;
	protected static $layout;
	
	public static function setUpBeforeClass() {
		self::$logger    = Logger::getLogger('test.Logger');
		self::$layout    = new LoggerMongoDbBsonLayout();
	}	
	
	public static function tearDownAfterClass() {
		self::$logger  = null;
		self::$layout  = null;
	}
	
	public function testActivateOptions() {
		$result = self::$layout->activateOptions();
		$this->assertTrue($result);
	}
	
	public function testgetContentType() {
		$result = self::$layout->getContentType();
		$this->assertEquals('application/bson', $result);
	}
	
	public function testFormatSimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$layout->format($event);
		
		$this->assertEquals('WARN', $bsonifiedEvent['level']);
		$this->assertEquals('test message', $bsonifiedEvent['message']);
		$this->assertEquals('test.Logger', $bsonifiedEvent['loggerName']);
	}
	
	public function testFormatLocationInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$layout->format($event);
		
		$this->assertEquals('NA', $bsonifiedEvent['fileName']);		
		$this->assertEquals('getLocationInformation', $bsonifiedEvent['method']);
		$this->assertEquals('NA', $bsonifiedEvent['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $bsonifiedEvent['className']);
	}
	
	public function testFormatThrowableInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		$bsonifiedEvent = self::$layout->format($event);
		
		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);
	}
	
	public function testFormatThrowableInfoWithInnerException() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new TestingException('test exeption', 1, new Exception('test exception inner', 2))
		);
		$bsonifiedEvent = self::$layout->format($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);		
	}	
}
?>