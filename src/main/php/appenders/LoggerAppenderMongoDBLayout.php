<?php
class LoggerAppenderMongoDBLayout extends LoggerAppenderMongoDB {
	
	public function __construct($name = '') {
		LoggerAppender::__construct($name);
		$this->hostname         = self::$DEFAULT_MONGO_URL_PREFIX.self::$DEFAULT_MONGO_HOST;
		$this->port             = self::$DEFAULT_MONGO_PORT;
		$this->dbName           = self::$DEFAULT_DB_NAME;
		$this->collectionName   = self::$DEFAULT_COLLECTION_NAME;		
		$this->requiresLayout   = true;
	}	
	
	/**
	 * Appends a new event to the mongo database.
	 * 
	 * @throws LoggerException	If the pattern conversion or the INSERT statement fails.
	 */
	public function append(LoggerLoggingEvent $event) {
		if ($this->canAppend == true && $this->collection != null) {
			$document = json_decode($this->getLayout()->format($event), true);
			$document['timestamp'] = new MongoDate($document['timestamp']['sec'], $document['timestamp']['usec']);
			$this->collection->insert($document);
		}				 
	}	
} 
?>