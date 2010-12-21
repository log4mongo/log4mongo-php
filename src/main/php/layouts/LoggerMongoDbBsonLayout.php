<?php
/**
 * 
 * Base layout class for custom logging collections
 * 
 * example implementation:
 *  
 * class LoggerMongoDbBsonIpLayout extends LoggerMongoDbBsonLayout {
 *    
 *     public function format(LoggerLoggingEvent $event) {
 *         $document       = parent::format($event);
 *         $document['ip'] = $event->getMDC('ip');
 *         
 *         return $document;
 *     }
 * }
 */
class LoggerMongoDbBsonLayout extends LoggerLayout {
	
	protected $bsonifier;
	
	public function activateOptions() {
		$this->bsonifier = new LoggerLoggingEventBsonifier();
		return true;
	}	
	
	public function format(LoggerLoggingEvent $event) {
		$document = $this->bsonifier->bsonify($event);
		return $document;
	} 	
	
	public function getContentType() {
		return "application/bson";
	} 	
} 
?>