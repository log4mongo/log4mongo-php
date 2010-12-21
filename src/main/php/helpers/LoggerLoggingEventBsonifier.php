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
 * @license New BSD License 
 * @author char0n (Vladimir Gorej) <gorej@mortality.sk>	 
 * @package log4php
 * @subpackage helpers
 * @version 1.5rcb1
*/

class LoggerLoggingEventBsonifier {
	
	/**
	 * Bson-ify logging event into mongo bson
	 * 
	 * @param LoggerLoggingEvent $event
	 * @return ArrayObject
	 */
	public function bsonify(LoggerLoggingEvent $event) {
		$timestampSec  = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);

		$document = new ArrayObject(array(
			'timestamp'  => new MongoDate($timestampSec, $timestampUsec),
			'level'      => $event->getLevel()->toString(),
			'thread'     => (int) $event->getThreadName(),
			'message'    => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		));	

		$this->addLocationInformation($document, $event->getLocationInformation());
		$this->addThrowableInformation($document, $event->getThrowableInformation());
		
		return $document;
	}
	
	/**
	 * Adding, if exists, location information into bson document
	 * 
	 * @param ArrayObject $document
	 * @param LoggerLocationInfo $locationInfo	 * 
	 */
	protected function addLocationInformation(ArrayObject $document, LoggerLocationInfo $locationInfo = null) {
		if ($locationInfo != null) {
			$document['fileName']   = $locationInfo->getFileName();
			$document['method']     = $locationInfo->getMethodName();
			$document['lineNumber'] = ($locationInfo->getLineNumber() == 'NA') ? 'NA' : (int) $locationInfo->getLineNumber();
			$document['className']  = $locationInfo->getClassName();
		}		
	}
	
	/**
	 * Adding, if exists, throwable information into bson document
	 * 
	 * @param ArrayObject $document
	 * @param LoggerThrowableInformation $throwableInfo
	 */
	protected function addThrowableInformation(ArrayObject $document, LoggerThrowableInformation $throwableInfo = null) {
		if ($throwableInfo != null) {
			$document['exception'] = $this->bsonifyThrowable($throwableInfo->getThrowable());
		}
	}
	
	/**
	 * Bson-ifying php native Exception object
	 * Support for innner exceptions
	 * 
	 * @param Exception $ex
	 * @return array
	 */
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
