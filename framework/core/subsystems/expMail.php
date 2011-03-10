<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file thats holds the expJavascript class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2006 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * exponentMail is an integrator class, bringing the flexibility of SwiftMail into Exponent gracefully.
 * More docs to follow as I become more familiar with SwiftMail.
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

if (!defined('EXPONENT')) exit('');

if ( version_compare ( phpversion(), "5.2.0", ">=" ) ) {
	require_once(BASE.'external/Swift/Swift.php');

// Create the class.
class expMail extends Swift {
	
	private $log = null;
	private $errStack = null;
	public $to = null;
	public $from = SMTP_FROMADDRESS;
	private $message = null;
	private $precallfunction = null;
	private $precalldata = null;
	private $postcallfunction = null;
	private $postcalldata = null;
	
	function __construct($params = array()) {
		Swift_CacheFactory::setClassName("Swift_Cache_Disk");
		Swift_Cache_Disk::setSavePath(BASE."/tmp");
		// Set up the mailer method.  Won't be using this anytime soon but its nice to have.
		if (array_key_exists('method',$params)) {
			switch ($params['method']) {
				case "multi":
					require_once(BASE.'external/Swift/Connection/Multi.php');
					require_once(BASE.'external/Swift/Connection/SMTP.php');
					require_once(BASE.'external/Swift/Connection/NativeMail.php');
					require_once(BASE.'external/Swift/Connection/Sendmail.php');
				break;
				case "smtp":
					require_once(BASE.'external/Swift/Connection/SMTP.php');
					if (array_key_exists('connections', $params)) {
						if (is_array($params['connections'])) {
							$conn = new Swift_Connection_SMTP($params['connections']['host'], $params['connections']['port'], $params['connections']['option']);
						} else {
							$conn = new Swift_Connection_SMTP($params['connections']);
						}
					} else {
						$conn = new Swift_Connection_SMTP(SMTP_SERVER, SMTP_PORT);
						$conn->setUsername(SMTP_USERNAME);
						$conn->setpassword(SMTP_PASSWORD);
					}
				break;
				case "native":
					require_once(BASE.'external/Swift/Connection/NativeMail.php');
					if ( isset($params['connections']) && !is_array($params['connections']) && $params['connections'] != '' ) {
						// Allow custom mail parameters.
						$conn = new Swift_Connection_NativeMail($params['connections']);
					} else {
						// Use default Mail parameters.
						$conn = new Swift_Connection_NativeMail();
					}
				break;
				case "sendmail":
					require_once(BASE.'external/Swift/Connection/Sendmail.php');
					if ( isset($params['connections']) && !is_array($params['connections']) && $params['connections'] != '' ) {
						// Allow a custom sendmail command to be run.
						$conn = new Swift_Connection_Sendmail($params['connections']);
					} else {
						// Attempt to auto-detect.
						$conn = new Swift_Connection_Sendmail(Swift_Conection_Sendmail::AUTO_DETECT);
					}
				break;
				case "rotator":
					require_once(BASE.'external/Swift/Connection/Rotator.php');
					require_once(BASE.'external/Swift/Connection/SMTP.php');
					require_once(BASE.'external/Swift/Connection/NativeMail.php');
					require_once(BASE.'external/Swift/Connection/Sendmail.php');
					if ( is_array ($params['connections']) ) {
						$conn = new Swift_Connection_Rotator($params['connections']);
					} else {
						$this->errStack['Connection'] = '$params[\'connections\'] must be an array to use the connection rotator.';
						$this->__destruct();
					}
					
				break;
			}
			// Use our current config vars 
		} else if (SMTP_USE_PHP_MAIL) {
			require_once(BASE.'external/Swift/Connection/NativeMail.php');
			if ( isset($params['connections']) && !is_array($params['connections']) && $params['connections'] != '' ) {
				// Allow custom mail parameters.
				$conn = new Swift_Connection_NativeMail($params['connections']);
			} else {
				// Use default Mail parameters.
				$conn = new Swift_Connection_NativeMail();
			}
		} else {
			require_once(BASE.'external/Swift/Connection/SMTP.php');
			if (array_key_exists('connections', $params)) {
				if (is_array($params['connections'])) {
					$conn = new Swift_Connection_SMTP($params['connections']['host'], $params['connections']['port'], $params['connections']['option']);
				} else {
					$conn = new Swift_Connection_SMTP($params['connections']);
				}
			} else {
				$conn = new Swift_Connection_SMTP(SMTP_SERVER, SMTP_PORT);
				$conn->setUsername(SMTP_USERNAME);
				$conn->setpassword(SMTP_PASSWORD);
			}
		}
		parent::__construct($conn);
		$this->message = new Swift_Message();
		
		switch (DEVELOPMENT) {
			case 1:
				$this->log = Swift_LogContainer::getLog();
				$this->log->setLogLevel(1);
			break;
			case 2:
				$this->log = Swift_LogContainer::getLog();
				$this->log->setLogLevel(5);
			break;
		}

	}
	// End Constructor

	// quick send method
	public function quickSend($params=array()) {
		if (empty($params['to'])) return false;
		
		// set the mailto address for this email
    	$this->addTo($params['to']);
    	
    	// set up the from address
    	if ( !empty($params['from'])) {
        	$from = $params['from'];
        	$fromname = isset($params['from_name']) ? $params['from_name'] : null;
        	$this->addFrom($from, $fromname);
    	}
    	
        // set the rest of the email fields
		if (!empty($params['subject'])) $this->subject($params['subject']);
        if (!empty($params['headers'])) $this->addHeaders($params['headers']);
    	if (!empty($params['html_message'])) $this->addHTML($params['html_message']);
    	if (!empty($params['text_message'])) $this->addText($params['text_message']);
       	return $this->send();
	}	

	//Override the parent send function so we can set up the send to be cleaner.
	public function send () {	
		return parent::send($this->message, $this->to, $this->from);
	}
	
	// Does not seem to be working correctly.  Use at your own risk!
	public function batchSend() {
		require_once(BASE.'external/Swift/Plugin/AntiFlood.php');
		$this->attachPlugin(new Swift_Plugin_AntiFlood(200, 5),"anti-flood");
		$batch = new Swift_BatchMailer($this);
		$batch->setSleepTime(1);
		$batch->setMaxTries(1);
		$batch->send($this->message, $this->to, $this->from);
		return $batch->getFailedRecipients();
	}
	
	public function addHeaders($headers) {
		foreach ($headers as $header=>$value) {
			$this->message->headers->set($header, $value);
		}
	}
	
	public function addHTML($html) {
		$this->message->attach(new Swift_Message_Part($html, "text/html"));
	}
	
	public function addText($text) {
		$this->message->attach(new Swift_Message_Part($text, "text/plain"));
	}
	
	public function addRaw($body) {
		$this->message->setBody($body);
	}
	
	public function addTo ($a = '', $b = '') {
		if (!is_object($this->to)) {
			$this->to = new Swift_RecipientList();
		}
		if (is_array($a)) {
			foreach ($a as $addr) {
				$this->to->addTo($addr);
			}
		} else {
			if ($b != '') {
				$this->to->addTo($a, $b);
			} else {
				$this->to->addTo($a);
			}
		}	
	}
	
	public function addFrom($email=null, $name=null) {
	    if (!empty($email) && !empty($name)) {
	        $this->from = new Swift_Address($email, $name);
	    } else {
	        $this->from = $email;
	    }
	}
		
	public function messageId() {
		if (!is_object($this->message)) {
			$this->message = new Swift_Message();
		}
		return $this->message->generateId();
	}
	
	public function subject ($subj) { 
		if (!is_object($this->message)) {
			$this->message = new Swift_Message();
		}
		$this->message->headers->set("Subject", $subj);
	}
	
	public function clearBody () {
		$this->message->setBody("");
	}
	
	public function flushRecipients() {
	    $this->to->flush();
	}
	
	function __destruct() {
	/*
		if (DEVELOPMENT != 0) {
			eLog($this->log->dump(true));
		}
	*/
		if ($this->errStack != null) {
			eDebug($error);
		}
	}
}
// End Mail class.
// Pre-send processing class. (Incomplete)
/*
class preSend implements Swift_Events_BeforeSendListener {
	
	public function beforeSendPerformed(Swift_Events_SendEvent $e) {
		
	}
}
*/
} else {
	eDebug ("You must be using PHP 5.2 or greater to use the exponent mail subsystem.");
}
?>
