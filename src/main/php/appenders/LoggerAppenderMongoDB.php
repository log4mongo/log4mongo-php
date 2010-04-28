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
 * @subpackage appenders
 * @version 0.9a
*/

class LoggerAppenderMongoDB extends LoggerAppender {
		
	protected static $DEFAULT_MONGO_HOST       = 'localhost';
	protected static $DEFAULT_MONGO_PORT       = 27017;
	protected static $DEFAULT_DB_NAME          = 'log4php_mongodb';
	protected static $DEFAULT_COLLECTION_NAME  = 'logs';		 
	
	protected $hostname;
	protected $port;
	protected $dbName;
	protected $collectionName;
	
	protected $connection;
	protected $collection;
		
	protected $userName;
	protected $password;
		
	public function __construct($name = '') {
		parent::__construct($name);
		$this->hostname         = self::$DEFAULT_MONGO_HOST;
		$this->port             = self::$DEFAULT_MONGO_PORT;
		$this->dbName           = self::$DEFAULT_DB_NAME;
		$this->collectionName   = self::$DEFAULT_COLLECTION_NAME;
		
		$this->requiresLayout = false;
	}
		
	public function setHost($hostname) {			
		$this->hostname = $hostname;				
	}
		
	public function getHost() {
		return $this->hostname;
	}
		
	public function setPort($port) {
			$this->port = $port;
	}
		
	public function getPort() {
			return $this->port;
	}
		
	public function setDatabaseName($dbName) {
		$this->dbName = $dbName;
	}
		
	public function getDatabaseName() {
		return $this->dbName;
	}
		
	public function setCollectionName($collectionName) {
		$this->collectionName = $collectionName;
	}
		
	public function getCollectionName() {
		return $this->collectionName;
	}
		
	public function setUserName($userName) {
		$this->userName = $userName;
	}
		
	public function getUserName() {
		return $this->userName;
	}
		
	public function setPassword($password) {
		$this->password = $password;
	}
		
	public function getPassword() {
		return $this->password;
	}
		
	/**
	 * Setup db connection.
	 * Based on defined options, this method connects to db defined in {@link $dbNmae}
	 * and creates a {@link $collection} 
	 * @return boolean true if all ok.
	 * @throws an Exception if the attempt to connect to the requested database fails.
	 */
	public function activateOptions() {
		try {
			$this->connection = new Mongo(sprintf('%s:%d', $this->hostname, $this->port));
			$db	= $this->connection->selectDB($this->dbName);
			if ($this->userName !== null && $this->password !== null) {
				$authResult = $db->authenticate($this->userName, $this->password);
				if ($authResult['ok'] == floatval(0)) {
					throw new Exception($authResult['errmsg'], $authResult['ok']);
				}
			}
			$this->collection = $db->selectCollection($this->collectionName);												 
		} catch (Exception $ex) {
			$this->canAppend = false;
			throw new LoggerException($ex);
		} 
			
		$this->canAppend = true;
		return true;
	}		 
		
	/**
	 * Appends a new event to the mongo database.
	 * 
	 * @throws LoggerException	If the pattern conversion or the INSERT statement fails.
	 */
	public function append(LoggerLoggingEvent $event) {		 
		if ($this->collection != null) {
			$document = $this->loggingEventToArray($event);
			$this->collection->insert($document);
		}				 
	}
		
	/**
		 * Closes the connection to the logging database
		 */
	public function close() {
		if($this->closed != true) {
			$this->collection = null;
			if ($this->connection !== null) {
				$this->connection->close();
				$this->connection = null;	
			}					
			$this->closed = true;
		}
	}		 
		
	public function __destruct() {
		$this->close();
	}
		
	protected function loggingEventToArray(LoggerLoggingEvent $event) {
		$document = array(
				'timestamp' => $event->getTimestamp(),
				'level'     => $event->getLevel()->toString(),
				'thread'    => $event->getThreadName(),
				'message'   => $event->getMessage()
		);
		
		if ($event->getLocationInformation() !== null) {
				$document['fileName']   = $event->getLocationInformation()->getFileName();
				$document['method']     = $event->getLocationInformation()->getMethodName();
				$document['lineNumber'] = $event->getLocationInformation()->getLineNumber();
				$document['className']  = $event->getLocationInformation()->getClassName();
		}
        
        if ($event->getThrowableInformation() !== null) {
            $document['exception'] = $this->exceptionToArray($event->getThrowableInformation()->getThrowable());                    
        }
		
		return $document;
	}
    
    protected function exceptionToArray(Exception $ex) {
        $document = array(        
            'message'    => $ex->getMessage(),
            'code'       => $ex->getCode(),
            'stackTrace' => $ex->getTraceAsString(),
        );
        
        if (method_exists($ex, 'getPrevious') && $ex->getPrevious() !== null) {
            $document['innerException'] = $this->exceptionToArray($ex->getPrevious());
        }
        
        return $document;
    }
}
?>