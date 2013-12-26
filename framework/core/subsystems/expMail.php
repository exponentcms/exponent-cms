<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/**
 * This is the class expMail
 * expMail is an integrator class, bringing the flexibility of SwiftMail into Exponent gracefully.
 * More docs to follow as I become more familiar with SwiftMail.
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

/**
 * The expMail class
 */
class expMail {

	private $log = null;
	private $errStack = null;
	public $to = null;
	//public $from = SMTP_FROMADDRESS;
	public $from = NULL;
	private $message = null;

	//this is the mail transporter like exim, SMTP, whatever, that is setup in the constructor
	private $transport = null;
	private $mailer = null;

	private $precallfunction = null;
	private $precalldata = null;
	private $postcallfunction = null;
	private $postcalldata = null;

	/**
	 *  __construct() - This function constructs the message to be send in this object, filling it out with connection information based on the site config.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example You can specify how you would like to send mail, overriding the system default.
	 *
	 *	//Here you are using the system default, whether it is smtp, php mail, sendmail, exim, native, or rotator.
	 *
	 *	  $emailItem = new expMail();
	 *
	 *	  //Here you are trying different types of connections:
	 *	  $emailItem = new expMail(array('method'=>'smtp'));
	 *	  $emailItem = new expMail(array('method'=>'sendmail');
	 *	  $emailItem = new expMail(array('method'=>'exim');
	 *	  $emailItem = new expMail(array('method'=>'native');
	 *	  $emailItem = new expMail(array('method'=>'rotator');
	 *
	 * @param array $params You can specify which method by which you would like to send mail.
	 *
	 * @todo add support for telling the constructor where the system specific Mail Transit Authority is: i.e. where sendmail or exim is if not in the default /usr/sbin
	 * @todo drop support for passing in custom "connections", a Swift 3.x relic that we do not need.
	 * @todo add further documentation for using settings other than the system default
	 */
	function __construct($params = array()) {
//		require_once(BASE . 'external/Swift-4/lib/swift_required.php');
		require_once(SWIFT_PATH . 'swift_required.php');

		if (array_key_exists('method', $params)) {
			switch ($params['method']) {
				case "multi":
					break;
				case "smtp":
					//require_once(BASE.'external/Swift-4/Connection/SMTP.php');
					if (array_key_exists('connections', $params)) {
						if (is_array($params['connections'])) {
							//$this->transport = new Swift_Connection_SMTP($params['connections']['host'], $params['connections']['port'], $params['connections']['option']);
							$this->transport = Swift_SmtpTransport::newInstance($params['connections']['host'], $params['connections']['port']);
						} else {
							$this->transport = Swift_SmtpTransport::newInstance($params['connections']['host'], $params['connections']['port']);
						}
					} else {
						$this->transport = Swift_SmtpTransport::newInstance(SMTP_SERVER, SMTP_PORT, SMTP_PROTOCOL)
								->setUsername(SMTP_USERNAME)
								->setPassword(SMTP_PASSWORD);
					}
					break;
				case "native":

					if (isset($params['connections']) && !is_array($params['connections']) && $params['connections'] != '') {
						// Allow custom mail parameters.
						$this->transport = Swift_MailTransport::newInstance($params['connections']);
					} else {
						$this->transport = Swift_MailTransport::newInstance();
					}
					break;
				case "exim":
					$this->transport = Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs');
					break;
				case "sendmail":
					$this->transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
					break;
//				case "rotator":
//					if (is_array($params['connections'])) {
//						$this->transport = new Swift_Connection_Rotator($params['connections']);
//					} else {
//						$this->errStack['Connection'] = '$params[\'connections\'] must be an array to use the connection rotator.';
//						$this->__destruct();
//					}
//					break;
			}
		// Use our current config vars
		} else if (SMTP_USE_PHP_MAIL) {
			if (isset($params['connections']) && !is_array($params['connections']) && $params['connections'] != '') {
				// Allow custom mail parameters.
				$this->transport = Swift_MailTransport::newInstance($params['connections']);
			} else {
				// Use default Mail parameters.
				$this->transport = Swift_MailTransport::newInstance();
			}
		} else {
			/*
				If the user specifies what kind they want to use, use that transport, otherwise here we are going to default to SMTP.
				You will notice that this code is identical to the above SMTP code
			*/
			if (array_key_exists('connections', $params)) {
				if (is_array($params['connections'])) {
					//$conn = new Swift_Connection_SMTP($params['connections']['host'], $params['connections']['port'], $params['connections']['option']);
					$this->transport = Swift_SmtpTransport::newInstance($params['connections']['host'], $params['connections']['port']);
				} else {
					$this->transport = Swift_SmtpTransport::newInstance($params['connections']['host'], $params['connections']['port']);
				}
			} else {
				$this->transport = Swift_SmtpTransport::newInstance(SMTP_SERVER, SMTP_PORT, SMTP_PROTOCOL)
						->setUsername(SMTP_USERNAME)
						->setPassword(SMTP_PASSWORD);
			}
		}

		//setup the transport authority for sending the email, whether it is SMTP, exim, sendmail, PHP, whatever....
		$this->mailer = Swift_Mailer::newInstance($this->transport);
		$this->message = new Swift_Message();

		switch (SMTP_DEBUGGING) {
			case 1:
				//To use the eDebugLogger for Exponent
				$this->log = new Swift_Plugins_Loggers_eDebugLogger();
				$this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->log));
				break;
			case 2:
				//To use the ArrayLogger
				$this->log = new Swift_Plugins_Loggers_ArrayLogger();
				$this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->log));
				break;
			case 3:
				//Or to use the Echo Logger
				$this->log = new Swift_Plugins_Loggers_EchoLogger();
				$this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->log));
				break;
		}
	}

	// End Constructor

	/**
	 * test() - Does the mail system seem to be working correctly?
	 *   This is useless for the Native mail system
	 *
	 * @todo Update this section to use more error checking
	 */
	public function test() {
		try {
			$this->transport->start();
			echo ("<h2>Mail Server Test Complete!</h2>We Connected to the Mail Server");
		} catch (Swift_TransportException $e) {
			echo ("<h2>Mail Server Test Failed!</h2>");
			eDebug($e->getMessage());
		}
	}
	/**
	 * quickSend() - This is a quick method for sending email messages.  It only requires a message value be passed in
	 * an associative array, (or else the message fails immediately).
	 *
	 * @todo May add allowing a string param to be passed as the message (text_message) using all defaults to mail it.

	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will send a quick message, showing you what basic fields are required.
	 *
	 *	 $body = "My body.";
	 *	 $tos = explode(',', str_replace(' ', '', COMMENTS_NOTIFICATION_EMAIL)); //pull list of CSV emails from the site config
	 *   $subject = "My subject";
	 *
	 *   $mail = new expMail();
	 *   $mail->quickSend(array(
	 *          'html_message'=>$body,
	 *		    'to'=>$tos,
	 *		    'from'=>trim(SMTP_FROMADDRESS), //pull EMAIL from site config
	 *		    'subject'=>$subject,
	 *   ));
	 *
	 *   (or even simpler)
	 *	 $mail = new expMail();
	 *   $mail->quickSend(array('html_message'=>'Hello'));
	 *
	 * @param array $params This is the associative array required to send the email. The minimum basics for sending an email are:
	 *   -html_message or -text_message
	 *  If a from or a to is not specified, the send method will pull the default SMTP_FROMADDRESS from the site config.
	 *  Usable parameters include:
	 *   -to
	 *   -from
	 *   -subject
	 *   -html_message
	 *   -text_message
	 *   -headers
	 *
	 * @return int number of recipients to which the email was successfully sent.
	 */
	public function quickSend($params = array()) {
		if (empty($params['html_message']) && empty($params['text_message'])) {
			return false;
		}

    	// set up the to address(es)
		if (is_array($params['to'])) {
			$params['to'] = array_filter($params['to']);
		} elseif (empty($params['to'])) {
			$params['to'] = SMTP_FROMADDRESS;
		} else {
			trim($params['to']);
		}
//		$this->message->setTo((array)$params['to']);
        $this->addTo((array)$params['to']);

    	// set up the from address(es)
		if (is_array($params['from'])) {
			$params['from'] = array_filter($params['from']);
		} elseif (empty($params['from'])) {
			$params['from'] = SMTP_FROMADDRESS;
		} else {
			trim($params['from']);
		}
//		$this->message->setFrom((array)$params['from']);  //FIXME we need to use this->addFrom() instead
        $this->addFrom($params['from']);

		$this->message->setSubject($params['subject'] = !empty($params['subject']) ? $params['subject'] : 'Message from '.SITE_TITLE);

		if (!empty($params['headers'])) $this->addHeaders($params['headers']);

		if (!empty($params['html_message'])) {
			$this->setHTMLBody($params['html_message']);
			if (!empty($params['text_message'])) $this->addText($params['text_message']);
		} elseif (!empty($params['text_message'])) {
			$this->setTextBody($params['text_message']);
		}

		$numsent = 0;
		try {
            $failed = array();
			$numsent = $this->mailer->send($this->message,$failed);
            if (!empty($failed)) {
                flash('error',gt('Unable to Send Mail to').' - '.implode(', ',$failed));
            }
		} catch (Swift_TransportException $e) {
			flash('error',gt('Sending Mail Failed!').' - '.$e->getMessage());
		}
		return $numsent;
	}

	/**
	 *  send() - This is the main method for sending email messages.If there is not a from address, it defaults to the one stored in the Exponent config.
	 *
	 * @todo This function needs some work, some error checking. It needs to check that all of the values necessary to send a basic message are present
	 *	If they are not, the system should respond in a friendly manner that something went wrong. The underlying swift errors are ugly, and so
	 *	no one should ever see them.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will send a basic message, using the setHTMLBody method to set the text
	 *
	 *	 $emailItem = new expMail();
	 *
	 *	 $emailItem->setHTMLBody('<h2>My Text</h2> ');
	 *	 $emailItem->setTextBody('My Text ');
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 if($emailItem->send() < 1) //the number of recipients
	 *	  {
	 *		  eDebug "There was an error sending your message."
	 *	 }
	 *
	 * @return int number of recipients to which the email was successfully sent.
	 */
	//Override the parent send function so we can set up the send to be cleaner.
	public function send() {
		//return parent::send($this->message, $this->to, $this->from);

		//the from address is usually set as the system default.
		//since I am keeping variables separate then the ones in the MESSAGE object
		//I need to set the message object up for a send

		//I keep separate variables so I can just easily tell if something has been set, and
		//it is kind of a hack as I am porting over from Swift 3 where these variables were necessary...
		if (!$this->from) {
			$this->addFrom(SMTP_FROMADDRESS);
		}
        $failed = array();
		$numsent = $this->mailer->send($this->message,$failed);
        if (!empty($failed)) {
            flash('error',gt('Unable to Send Mail to').' - '.implode(', ',$failed));
        }
        return $numsent;
	}

	/**
	 *  batchSend() - Does not seem to be working correctly.  Use at your own risk!
	 *
	 *  This should probably be taken out as it look like a failed attempt to use the old-school AntiFlood plugin
	 *  This is leftover code from Swift 3.x
	 *
	 * @todo Update this section to use batch processing properly
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @return array
	 */
	public function batchSend() {
//		require_once(BASE . 'external/Swift-4/lib/classes/Swift/Plugins/AntiFloodPlugin.php');
//		$this->attachPlugin(new Swift_Plugin_AntiFlood(200, 5), "anti-flood");
//		$this->mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100, 30));
//
//		$batch = new Swift_BatchMailer($this);
//		$batch->setSleepTime(1);
//		$batch->setMaxTries(1);
//		$batch->send($this->message, $this->to, $this->from);
//		return $batch->getFailedRecipients();
	}

	/**
	 *  quickBatchSend() - a quick way to send a batch of emails one at a time
	 * This function is similar to quickSend, but sends each email separately to protect others identity in the mailing
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @return array
	 */
	public function quickBatchSend() {
		if (empty($params['html_message']) && empty($params['text_message'])) {
			return false;
		}

    	// set up the to address(es)
		if (is_array($params['to'])) {
			$params['to'] = array_filter($params['to']);
		} elseif (empty($params['to'])) {
			$params['to'] = array(SMTP_FROMADDRESS);
		} else {
			$params['to'] = array(trim($params['to']));
		}
        $this->addTo($params['to']);

    	// set up the from address(es)
		if (is_array($params['from'])) {
			$params['from'] = array_filter($params['from']);
		} elseif (empty($params['from'])) {
			$params['from'] = SMTP_FROMADDRESS;
		} else {
			trim($params['from']);
		}
//		$this->message->setFrom($params['from']);  //FIXME we need to use this->addFrom() instead
        $this->addFrom($params['from']);

		$this->addSubject($params['subject'] = !empty($params['subject']) ? $params['subject'] : 'Message from '.SITE_TITLE);

		if (!empty($params['headers'])) $this->addHeaders($params['headers']);

		if (!empty($params['html_message'])) {
			$this->setHTMLBody($params['html_message']);
			if (!empty($params['text_message'])) $this->addText($params['text_message']);
		} elseif (!empty($params['text_message'])) {
			$this->setTextBody($params['text_message']);
		}

		$numsent = 0;
		foreach ($params['to'] as $address=>$name) {
			try {
				$this->message->setTo(array($address=>$name));
				$numsent += $this->send($this->message);
			} catch (Swift_TransportException $e) {
				flash('error',gt('Batch Send Mail Failed!').' - '.$address.' - '.$e->getMessage());
			}
		}
		return $numsent;
	}

	/**
	 *  addHeaders() - Add text headers to the message. Limited to text headers for now.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will include the passed HTML and will set it as an addition to the message body.
	 *
	 *	 $emailItem = new expMail();
	 *
	 *	 $emailItem->addHTML('<h2>My Text</h2> '); //This adds an alternate version if your email in HTML format
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $headersToAdd = array('Your-Header-Name' => 'the header value', 'SecondHeader-Name' => '2nd header value');
	 *	 $emailItem->addHeaders($headersToAdd);
	 *
	 *	 $emailItem->send();
	 *
	 * @param array $headers Array of text headers to add to the message
	 *
	 * @todo add support for other types of headers mentioned below, and a second pass in value/switch to specify what kind headers you are adding
	 *
	 *	  # Text Headers
	 *			Text headers are the simplest type of Header. They contain textual information with no special information included within it for example the Subject header in a message.
	 *	  # Parameterized Headers
	 *			Parameterized headers are text headers that contain key-value parameters following the textual content. The Content-Type header of a message is a parameterized header since it contains charset information after the content type.
	 *	  # Date Headers
	 *			Date headers contains an RFC 2822 formatted date (i.e. what PHP's date('r') returns). They are used anywhere a date or time is needed to be presented as a message header.
	 *	  # Mailbox (e-mail address) Headers
	 *			Mailbox headers contain one or more email addresses, possibly with personalized names attached to them. The data on which they are modeled is represented by an associative array of email addresses and names.
	 *	  # ID Headers
	 *			ID headers contain identifiers for the entity (or the message). The most notable ID header is the Message-ID header on the message itself.
	 *	  # Path Headers
	 *			Path headers are like very-restricted mailbox headers. They contain a single email address with no associated name. The Return-Path header of a message is a path header.
	 */
	public function addHeaders($headers) {
		$headers = $this->message->getHeaders();
		foreach ($headers as $header => $value) {
			//new SWIFT 4 way
            $headers->addTextHeader($header, $value);
		}
	}


	/**
	 *  addHTML() - This function is similar to setHTMLBody except that it includes the HTML in the message body rather than sets it as default. Many
	 *  mail clients will read the HTML over plain text as default behavior, so even if you use setTextBody and addHTML, they will default to reading the HTML.
	 *  setHTMLBody is to be preferred as the default for setting the body of the email.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will include the passed HTML and will set it as an addition to the message body.
	 *
	 *	 $emailItem = new expMail();
	 *
	 *	 $emailItem->addHTML('<h2>My Text</h2> '); //This adds an alternate version if your email in HTML format
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * You can also call the addHTML multiple times to append to the message:
	 *
	 *	 $emailItem = new expMail();
	 *
	 *   $emailItem->addHTML('My HTML <br>');
	 *	 $emailItem->addHTML('Line Two <br>');
	 *   $emailItem->addHTML('Line Three <br>');
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * @param string $html This is the html that you want added to the message.
	 */
	public function addHTML($html) {
		$this->message->addPart($html, "text/html");
	}

	/**
	 *  setHTMLBody() - This function sets the main version of the message to HTML.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will set the message body to the HTML that is passed in. This is the standard way to set the email message body.
	 *
	 *	 $emailItem = new expMail();
	 *
	 *	 $emailItem->setHTMLBody('<h2>My Text</h2> '); //This sets the body to be an HTML version
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * @param string $html This is the HTML that you want set as the body
	 */
	public function setHTMLBody($html) {
		$this->message->setBody($html, "text/html");
	}

	/**
	 *  setTextBody() - This function sets the main version of the message to plain text. No HTML
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will set the message body to plain text. Usually people do this because the recipient cannot read HTML email.
	 *
	 *	 $emailItem = new expMail();
	 *
	 *	 $emailItem->setTextBody('My Text '); //This adds a Text version in case they cannot read HTML
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * @param string $text This is the text version of the email you are sending
	 */
	public function setTextBody($text) {
		$this->message->setBody($text, "text/plain");
	}

	/**
	 *  addText() - This function is similar to the addHTML function above, it adds a plain text part to the message in case
	 *  your recipient cannot receive HTML email, if you have added a text version of the email, they will have this to fall back on.
	 *  Every email send out should have an HTML version and a TEXT version.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will set the message body in HTML and also include a text counterpart in case the recipient cannot view HTML.
	 *
	 *	 $emailItem = new expMail();
	 *
	 *   $emailItem->setHTMLBody('<b>My Text</b> '); //This is an HTML version of the message
	 *	 $emailItem->addText('My Text '); //This adds a Text version in case they cannot read HTML
	 *
	 *	 $emailItem->addTo('myemail@mysite.com');
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * @param string $text This is the text version of the email you are sending along with the HTML
	 */
	public function addText($text) {
		$this->message->addPart($text, "text/plain");
	}

	/**
	 *  addRaw() - This is a wrapper around setHTMLBody for backwards compatibility
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @param $body
	 */
	public function addRaw($body) {
		$this->setHTMLBody($body);
	}

    /**
     *  addTo() - This adds people to the Recipient List in the To Field.
     *  If the first variable passed is an array,
     *    it assumes you are sending messages to multiple people.
     *    If you want to add people with their names associated with the email, you must use an outside for loop.
     *  This function does not yet support the parsing of associative arrays for a quick add to the To recipient list.
     *
     * @author        Tyler Smart <tyleresmart@gmail.com>
     * @example       This will send a basic message, looping through an array of email addresses add adding them to the BCC list.
     *                $emailItem = new expMail();
     *                $emailItem->addText('My Text ');
     *                $emailItem->addText('Line Two ');
     *                $emailItem->addText('Line Three ');
     *                $to_array = array('a@website.com'=>'Mr A.', 'b@website.com'=>'Mr B.', 'c@website.com'=>'Mr C.', 'd@website.com'=>'Mr D.', 'e@website.com'=>'Mr E.', 'f@website.com'=>'Mr F.');
     *                //add multiple bcc recipients to the email
     *                foreach ($to_array as $email => $name)
     *                {
     *                $emailItem->addBcc($email, $name);
     *                }
     *                $emailItem->addFrom('from@sender.com');
     *                $emailItem->subject('Hello World!');
     *                $emailItem->send();
     *                //You can also just specify the email without the name, like so:
     *                $emailItem = new expMail();
     *                $emailItem->addText('My Text ');
     *                $emailItem->addText('Line Two ');
     *                $emailItem->addText('Line Three ');
     *                $emailItem->addTo('bob@smith.com');
     *                $emailItem->addFrom('from@sender.com');
     *                $emailItem->subject('Hello World!');
     *                $emailItem->send();
     *                //You can also send an array of email addresses as the first argument, like so:
     *                $emailItem = new expMail();
     *                $emailItem->addText('My Text ');
     *                $emailItem->addText('Line Two ');
     *                $emailItem->addText('Line Three ');
     *                $emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
     *                $emailItem->addFrom('from@sender.com');
     *                $emailItem->subject('Hello World!');
     *                $emailItem->send();
     *
     * @param  array|string $email This is the string or array to send email to
     * @todo          A nice future feature addition would be to allow the passing in of associative arrays like so:
     *                $emailsToSendTo = array('bob@smith.com'=>'Bob Smith', 'mary@smith.com'=>'Mary Smith');
     *                $emailItem->addTo($emailsToSendTo);
     *                OR
     *                $emailItem->addTo('array('myemail@mysite.com'=>'Website Owner', 'secondemail@website.com'=>'Frank Jones');
     *                Actually, cleanup should be done so that this function only takes associative arrays, and nothing else.
     */
	public function addTo($email = null) {
        // attempt to fix a bad to address
        if (is_array($email)) {
            foreach ($email as $address=>$name) {
                if (is_integer($address)) {
                    if (strstr($name,'.') === false) {
                        $email[$address] .= $name.'.net';
                    }
                }
            }
        } else {
            if (strstr($email,'.') === false) {
                $email .= '.net';
            }
        }
        $this->to = $email;
        if (!empty($email)) {
            $this->message->setTo($email);
        }
	}

		/**
	 *  addCc() - This adds Carbon Copy addresses to the message one at a time, so if you want to add multiple CC's it is best to run through a loop in order to put them in.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will send a basic message, looping through an array of email addresses add adding them to the CC list.
	 *
	 *	$emailItem = new expMail();
	 *
	 *  $emailItem->addText('My Text ');
	 *	$emailItem->addText('Line Two ');
	 *  $emailItem->addText('Line Three ');
	 *
	 *	$ccs = array('a@website.com'=>'Mr A.', 'b@website.com'=>'Mr B.', 'c@website.com'=>'Mr C.', 'd@website.com'=>'Mr D.', 'e@website.com'=>'Mr E.', 'f@website.com'=>'Mr F.');
	 *
	 *	//add multiple bcc recipients to the email
	 *	foreach ($ccs as $email => $name)
	 *	{
	 *		$emailItem->addCc($email, $name);
	 *	}
	 *
	 *	$emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
	 *	$emailItem->addFrom('from@sender.com'); //setting the From field using just the email.
	 *	$emailItem->subject('Hello World!');
	 *
	 *	$emailItem->send();
	 *
	 * @param string $email This is the email address for the BCC.
	 * @param string $name  This is the name associated with the above email address.
	 */
	public function addCc($email, $name = null) {
		$this->message->addCc($email, $name);
	}

	/**
	 *  addBcc() - This adds Blind Carbon Copy addresses to the message one at a time, so if you want to add multiple BCC's it is best to run through a loop in order to put them in.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will send a basic message, looping through an array of email addresses add adding them to the BCC list.
	 *
	 *	$emailItem = new expMail();
	 *
	 *  $emailItem->addText('My Text ');
	 *	$emailItem->addText('Line Two ');
	 *  $emailItem->addText('Line Three ');
	 *
	 *	$bccs = array('a@website.com'=>'Mr A.', 'b@website.com'=>'Mr B.', 'c@website.com'=>'Mr C.', 'd@website.com'=>'Mr D.', 'e@website.com'=>'Mr E.', 'f@website.com'=>'Mr F.');
	 *
	 *	//add multiple bcc recipients to the email
	 *	foreach ($bccs as $email => $name)
	 *	{
	 *		$emailItem->addBcc($email, $name);
	 *	}
	 *
	 *	$emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
	 *	$emailItem->addFrom('from@sender.com'); //setting the From field using just the email.
	 *	$emailItem->subject('Hello World!');
	 *
	 *	$emailItem->send();
	 *
	 * @param string $email This is the email address for the BCC.
	 * @param string $name  This is the name associated with the above email address.
	 */
	public function addBcc($email, $name = null) {
		$this->message->addBcc($email, $name);
	}

    /**
     *  addFrom() - This adds the singular From address, if you call it twice, it will replace the old from address.
     *    It also sets the private var from with whatever you desire. This is a relic from the Swift 3.x days and should be gutted out
     *    the next time someone upgrades the mailer.
     *
     * @author   Tyler Smart <tyleresmart@gmail.com>
     * @example  This will send a basic message, but you can see how the subject line works.
     *           $emailItem = new expMail();
     *           $emailItem->addText('My Text ');
     *           $emailItem->addText('Line Two ');
     *           $emailItem->addText('Line Three ');
     *           $emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
     *           $emailItem->addFrom('from@sender.com'); //setting the From field using just the email.
     *           $emailItem->addFrom('from@sender.com', 'Mr. Sender'); //resetting the From field using the email and the name.
     *           $emailItem->subject('Hello World!');
     *           $emailItem->send();
     *
     * @param string $email This is the email address you want to use as the sender.
     */
	public function addFrom($email = null) {
        // attempt to fix a bad from address
        if (is_array($email)) {
            foreach ($email as $address=>$name) {
                if (strstr($address,'.') === false) {
                    $email[$name] .= '.net';
                }
            }
        } else {
            if (strstr($email,'.') === false) {
                $email .= '.net';
            }
        }
        $this->from = $email;
        if (!empty($email)) {
            $this->message->setFrom($email);
        }
	}

	/**
	 * messageId() - This will return the unique ID of the mail message.
	 *
	 *	Courtesy of the underlying Swift mailer:
	 *
	 * ID headers contain identifiers for the entity (or the message). The most notable ID header is the Message-ID header on the message itself.
	 *
	 * An ID that exists inside an ID header looks more-or-less less like an email address. For example, <1234955437.499becad62ec2@example.org>. The part to the left of the @ sign is usually unique, based on the	      * current time and some random factor. The part on the right is usually a domain name.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This returns the Message ID on the mail
	 *
	 *	$emailItem = new expMail();
	 *
	 *  $emailItem->addText('My Text ');
	 *	$emailItem->addText('Line Two ');
	 *  $emailItem->addText('Line Three ');
	 *
	 *	$emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
	 *	$emailItem->addFrom('from@sender.com');
	 *	$emailItem->subject('Hello World!');
	 *
	 *  echo "$emailItem->messageID()";
	 *
	 *	$emailItem->send();
	 * @return array
	 */
	public function messageId() {
		if (!is_object($this->message)) {
			$this->message = new Swift_Message();
		}
		return $this->message->getHeaders()->getHeader('Message-ID');
	}

	/**
	 * subject() - This will set the subject of the message, ensuring first that message has been instantiated.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will send a basic message, but you can see how the subject line works.
	 *	 $emailItem = new expMail();
	 *
	 *   $emailItem->addText('My Text ');
	 *	 $emailItem->addText('Line Two ');
	 *   $emailItem->addText('Line Three ');
	 *
	 * 	 $emailItem->addTo(array('myemail@mysite.com', 'secondemail@website.com', 'third@emailsite.com'));
	 *	 $emailItem->addFrom('from@sender.com');
	 *	 $emailItem->subject('Hello World!');
	 *
	 *	 $emailItem->send();
	 *
	 * @param string  $subj This is the string that you want to be used as the subject for the message, it must be plain text.
	 */

	public function addSubject($subj) {
		if (!is_object($this->message)) {
			$this->message = new Swift_Message();
		}
		$this->message->setSubject($subj);
	}

	/**
	 *  clearBody() - This function will set the body of the message to empty, a blank text string
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 */
	public function clearBody() {
		$this->message->setBody('', 'text/plain');
	}

	/**
	 * flushRecipients() - this will clear all of the recipients in the To array, ignoring CC and BCC
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will set some recipients and then clear them
	 *	 $emailItem = new expMail();
	 *
	 *   $emailItem->addText('My Text ');
	 *	 $emailItem->addText('Line Two ');
	 *   $emailItem->addText('Line Three ');
	 *
	 *	 $emailItem->addTo('bob@smith.com', 'Bob Smith');
	 *	 $emailItem->addTo('bob2@smith.com', 'Bob2 Smith');
	 *	 $emailItem->addTo('bob3@smith.com', 'Bob3 Smith');
	 *
	 *   $emailItem->flushRecipients();
	 *
	 *	 $emailItem->addTo('whataboutbob@smith.com', 'Frank Smith');
	 *
	 *	 $emailItem->send();
	 */
	public function flushRecipients() {
		$this->message->setTo(array());
	}

	/**
	 * attach_file_not_on_disk() - This function will utilize the swift mailer's ability to attach files
	 *	that are not on disk, but are just variables.
	 *
	 *	This works well if you don't want to store it to disk, or if you are not encouraged
	 *	to do so because of HIPPA regulations or some such thing
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will generate some PDF data from an unknown PDF library without writing it to disk, then will pass that data off to Swift to attach.
	 *	//Create your file contents in the normal way, but don't write them to disk
	 *	$data = create_my_pdf_data();
	 *
	 * //Create the attachment with your data
	 *	$attachment = Swift_Attachment::newInstance($data, 'my-file.pdf', 'application/pdf');
	 *
	 *	//Attach it to the message
	 *	$message->attach($attachment);
	 *
	 *	//You can alternatively use method chaining to build the attachment
	 *	$attachment = Swift_Attachment::newInstance()
	 *	  ->setFilename('my-file.pdf')
	 *	  ->setContentType('application/pdf')
	 *	  ->setBody($data)
	 *	  ;
	 *
	 * @param $data_to_attach
	 * @param string  $file_name This is the name that you want to give the attached file, so for ex., if you have CSV data in the first param, you could call it "myfile.csv"
	 * @param string  $file_type This is the MIME type of the file that you are attaching
	 *
	 * @internal param mixed $file_to_attach This is the data for the file that you want to send, for example, the HTML, CSV, or PDF data
	 */
	public function attach_file_not_on_disk($data_to_attach, $file_name, $file_type) {
//		require_once(BASE . 'external/Swift-4/lib/classes/Swift/Attachment.php');

		//Create the attachment with your data
		$attachment = Swift_Attachment::newInstance($data_to_attach, $file_name, $file_type);

		//Attach it to the message
		$this->message->attach($attachment);
	}

	/**
	 * attach_file_on_disk() - This function will utilize the swift mailer's ability to attach files
	 *	that are on disk, unlike the one above, which just attached one that is available in memory.
	 *
	 * @author Tyler Smart <tyleresmart@gmail.com>
	 * @example This will show three different ways to attach files, two from a path on disk, and one from a url.
	 *      //Create the attachment
	 *		// * Note that you can technically leave the content-type parameter out
	 *		$attachment = Swift_Attachment::fromPath('/path/to/image.jpg', 'image/jpeg');
	 *
	 *		//Attach it to the message
	 *		$message->attach($attachment);
	 *
	 *		//The two statements above could be written in one line instead
	 *		$message->attach(Swift_Attachment::fromPath('/path/to/image.jpg'));
	 *
	 *		//You can attach files from a URL if allow_url_fopen is on in php.ini
	 *		$message->attach(Swift_Attachment::fromPath('http://site.tld/logo.png'));
	 *
	 * @param string $file_to_attach This is the path to the file that you want to attach to the message
	 * @param string  $file_type This is the MIME type of the file that you are attaching
	 */
	public function attach_file_on_disk($file_to_attach, $file_type) {
//		require_once(BASE . 'external/Swift-4/lib/classes/Swift/Attachment.php');

		//Create the attachment with your data
		$attachment = Swift_Attachment::fromPath($file_to_attach, $file_type);

		//Attach it to the message
		$this->message->attach($attachment);
	}

	/**
	 * expMail class destructor
	 */
	function __destruct() {
		if ($this->errStack != null) {
//			eDebug($error);
//			eDebug($this->errStack);
//			eDebug($this->log->dump());
		}
	}
}

?>