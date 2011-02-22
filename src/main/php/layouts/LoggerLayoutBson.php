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
 * @subpackage layouts
 * @version 1.5rcb1
*/

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
class LoggerLayoutBson extends LoggerLayout {
	
	/*
	 * Bsonifier instance
	 * 
	 * @var LoggerLoggingEventBsonifier
	 */
	protected $bsonifier;
	
	public function __construct() {
		$this->bsonifier = new LoggerLoggingEventBsonifier();
	}
	
	public function format(LoggerLoggingEvent $event) {
		$document = $this->bsonifier->bsonify($event);
		return json_encode($document);
	} 	
	
	public function getContentType() {
		return "application/bson";
	}
} 
?>