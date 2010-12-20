<?php
class LoggerLoggingEventBsonifier {
	
	public function bsonify(LoggerLoggingEvent $event) {
		$timestampSec  = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);
        
		$document = new ArrayObject(array(
			'timestamp'  => new MongoDate($timestampSec, $timestampUsec),
			'level'      => $event->getLevel()->toString(),
			'thread'     => $event->getThreadName(),
			'message'    => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		));	

		$this->addLocationInformation($document, $event->getLocationInformation());
		$this->addThrowableInformation($document, $event->getThrowableInformation());
	}
	
	protected function addLocationInformation(ArrayObject $document, LoggerLocationInfo $locationInfo = null) {
		if ($locationInfo != null) {
			$document['fileName']   = $locationInfo->getFileName();
			$document['method']     = $locationInfo->getMethodName();
			$document['lineNumber'] = $$locationInfo->getLineNumber();
			$document['className']  = $$locationInfo->getClassName();
		}		
	}
	
	protected function addThrowableInformation(ArrayObject $document, LoggerThrowableInformation $throwableInfo = null) {
		if ($throwableInfo != null) {
			$document['exception'] = $this->bsonifyThrowable($throwableInfo->getThrowable());
		}
	}
	
	protected function bsonifyThrowable(Exception $ex) {
		
		$bsonException = array(				
			'message'    => $ex->getMessage(),
			'code'       => $ex->getCode(),
			'stackTrace' => $ex->getTraceAsString(),
		);
                        
		if (method_exists($ex, 'getPrevious') && $ex->getPrevious() !== null) {
			$bsonException['innerException'] = $this->bsonifyThrowable($ex->getPrevious());
		}
			
		return $bsonException;
	}	
}	 
?>