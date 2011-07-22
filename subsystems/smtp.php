<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

/** exdoc
 * The definition of this constant lets other parts of the system know 
 * that the subsystem has been included for use.
 */
define("SYS_SMTP",1);

/** exdoc
 * Sends mail through a raw SMTP socket connection, to one or
 * more recipients.  This function is optimized for multiple recipients.
 * Returns true if all mail messages were sent.  Returns false if anything failed.
 *
 * @param array $to_r An array of recipient email address, or a single email address string.
 * @param string $from The from header string, usually an email address.
 * @param string $subject The subject of the message to send.  The same subject is used for all messages.
 *	 The subject cannot have newlines or carriage returns in it. (currently not checked)
 * @param string $message The message to send.  The same message is used for all messages.  Newlines
 *	 will be converted as needed.
 * @param array $headers An associative array of header fields for the email.
 * @param string $precallback
 * @param null $preUdata
 * @param string $postcallback
 * @param null $postUdata
 * @return bool
 *
 * @internal param string $callback The name of a callback function for processing each message
 * @udata object $callback The name of a callback function for processing each message
 * @node Subsystems:SMTP
 */
function exponent_smtp_mail($to_r,$from,$subject,$message,$headers=array(), $precallback="", $preUdata=null, $postcallback="", $postUdata=null) {

	require_once(BASE."/subsystems/mail.php");
	$mail = new exponentMail();
	$mail->addTo($to_r);
	if ( $from != '' ) {
//		if (ereg('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $from)) {
		if (preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $from)) {
			$mail->from = $from;
		}
	}
	$mail->subject($subject);
	$mail->addHeaders($headers);
	$mail->addHTML($message);
	try {
		if ($mail->send() > 0) {
			return true;
		} else {
			return false;
		}
	} catch (Exception $error) {
		//eDebug($error->getMessage());
		return false;
	}
	
/*
	//eDebug('Current revision of file is $Id: smtp.php 2008 2007-12-14 18:37:38Z kessler44 $');

	// Ugly kluge
	if (!isset($from) || $from == "") $from = SMTP_FROMADDRESS; // For shared hosters

	if (!is_array($to_r)) $to_r = array($to_r);
	if (!is_array($headers)) {
		$headers = explode("\r\n",trim($headers));
	}
	
	if (!isset($headers["From"])) $headers["From"] = $from;
	if (!isset($headers["Date"])) $headers["Date"] = date('r',time());
	if (!isset($headers["Reply-to"])) $headers["Reply-to"] = $from;
	//if (!isset($headers["Return-Path"])) $headers["Return-Path"] = $from;

	
	
	if (SMTP_USE_PHP_MAIL == 0) {
		
				
		// If we are not using PHP's mail() function (as per site config), go with raw SMTP
		
		$headers["Subject"] = $subject;
		
		$m = str_replace("\n","\r\n",str_replace("\r\n","\n",$message));
		
		$errno = 0;
		$error = "";
		$socket = @fsockopen(SMTP_SERVER, SMTP_PORT, $errno, $error, 1);
		if ($socket === false) {
			//eDebug("Unable to open a socket to communicate with the server.");
			//eDebug("Error was : $errno : $error");
			return false;
		}
		
		if (!exponent_smtp_checkResponse($socket,"220")) {
			return false;
		}
	
		// Try EHLO (Extendend HELO) first
		exponent_smtp_sendServerMessage($socket,"EHLO ".HOSTNAME);
		
		if (!exponent_smtp_checkResponse($socket,"250")) {
			// If EHLO failed, try to fallback to HELO, according to RFC2821
			exponent_smtp_sendServerMessage($socket,"HELO ".HOSTNAME);
			if (!exponent_smtp_checkResponse($socket,"250")) {
				exponent_smtp_sendExit($socket);
				return false;
			}
		} else {
			// EHLO succeeded - try to figure out what we need for auth
			//GREP:NOTIMPLEMENTED
		}
		
		if (SMTP_AUTHTYPE != "NONE" && !exponent_smtp_authenticate($socket,SMTP_AUTHTYPE,SMTP_USERNAME,SMTP_PASSWORD)) {
			exponent_smtp_sendExit($socket);
			return false;
		}
		
		if (!function_exists($precallback)) $precallback = "exponent_smtp_blankMailCallback";
		
		//exponent_smtp_blankMailCallback();
		
		//eDebug(count($to_r));
		$i = 0;
		foreach ($to_r as $key=>$to) {
			$i++;
			//sleep for 1/2 second between each email.
			usleep(500000);
			//first check to see if we're still alive:
			exponent_smtp_sendServerMessage($socket,"NOOP");
			if (exponent_smtp_checkResponse($socket,"250")) {											
				$message = $m.'';						
				$headers["To"] = $to;
				$debugMsg = '';
				
				//do presend callback
				$precallback($key,$message,$subject,$headers,$preUdata[$key]);
				
				exponent_smtp_sendServerMessage($socket,"MAIL FROM: $from");
				if (!exponent_smtp_checkResponse($socket,"250")) {
					exponent_smtp_sendExit($socket);
					$debugMsg = 'Died in MAIL FROM on message: ' . $i . ', sent to: ' . $to . '<br/>';
					//eDebug($debugMsg);					
				}else{				
					exponent_smtp_sendServerMessage($socket,"RCPT TO: <$to>");
					if (!exponent_smtp_checkResponse($socket,"250")) {
						//exponent_smtp_sendExit($socket); - don't die, as we want to continue on RCPT TO: errors
						$debugMsg = 'Error in RCPT TO on message: ' . $i . ', sent to: ' . $to . '<br/>';
						//eDebug($debugMsg);						
					}else{
						exponent_smtp_sendServerMessage($socket,"DATA");
						if (!exponent_smtp_checkResponse($socket,"354")) {
							$debugMsg = 'Died in DATA on message: ' . $i . ', sent to: ' . $to . '<br/>';
							//eDebug($debugMsg);							
						}else{
							exponent_smtp_sendHeadersPart($socket,$headers);
							exponent_smtp_sendMessagePart($socket,"\r\n".wordwrap($message)."\r\n");
							// Xavier Basty - 2005/02/07 - Fix for Lotus Notes SMTP
							exponent_smtp_sendServerMessage($socket,"\r\n.\r\n");
							if (!exponent_smtp_checkResponse($socket,"250")) {
								exponent_smtp_sendExit($socket);
								$debugMsg = 'Died after sending headers, message, and/or end lines on message: ' . $i . ', sent to: ' . $to . '<br/>';
								//eDebug($debugMsg);								
							}			
						}
					}
				}
								
				//if we got this far and debugMsg is not set, then the message was sent succesfully 
				//and we can update the maillog object/table
				if ($postcallback != ''){
					if ($debugMsg == ''){
						$postcallback($key, NL_NEWSLETTER_SENT);
					}else{
						$postcallback($key, NL_NEWSLETTER_ERROR, $debugMsg);
					}				
				}
			}else{			
				exponent_smtp_sendExit($socket);
				$debugMsg = 'NOOP failed on message: ' . $i . ', sent to: ' . $to . '<br/>';
				//eDebug($debugMsg);
				return false;						
			}
		}
		
		exponent_smtp_sendExit($socket);
		return true;
	} else {
		// If we are using PHP's mail() function, we need to set up to call mail
		
		$return = true;
		
		if (!@function_exists($callback)) { // No valid callback.
			$to = join(', ',$to_r);
			$real_headers = '';
			foreach ($headers as $key=>$value) {
				$real_headers .= $key.': '.$value."\r\n";
			}
			
			$message = str_replace("\r\n","\n",$message);
			
			
			if (mail($to,$subject,$message,$real_headers) == false) {
				$return = false;
			}
		} else { // need to do callbacks
			for ($i = 0; $i < count($to_r); $i++) {
				$to = $to_r[$i];
				
				$callback($i,$message,$subject,$headers,$preUdata);
				
				$real_headers = '';
				foreach ($headers as $key=>$value) {
					$real_headers .= $key.': '.$value."\r\n";
				}
				
				// Call the mail function -- for sending mass emails, this is potentially dangerous
				if (mail($to,$subject,$message,$real_headers) == false) {
					$return = false;
				}
			}
		}
		return $return;
	}
*/	
}

/** exdoc
 * Checks the server response to a message sent previously.  Returns
 * rue if the expected response was the actual response, and false if there was a discrepency.
 *
 * @param Socket $socket The socket object connected to the mail server
 * @param string $expected_response The response code (3-digit number)
 *	expected from the server
 * @return bool
 * @node Subsystems:SMTP
 */
function exponent_smtp_checkResponse($socket,$expected_response) {
	//eDebug("Checking response from server.");
	//eDebug("Expecting to get back $expected_response");
	$response = fgets($socket,256);
	//eDebug("Received response: ".substr($response,0,3));
	$line = $response;
	$count = 20;
	while ($count && substr($line,3,1) == "-") {
		$line = fgets($socket,256); // Clear the buffer, EHLO
		//eDebug("LINE: $line");
		$count--;
	}
	return (substr($response,0,3) == $expected_response);
}

/** exdoc
 * Sends an SMTP message to the server through the given socket.
 *
 * @param Socket $socket The socket object connected to the mail server
 * @param string $message The message to send
 * @node Subsystems:SMTP
 */
function exponent_smtp_sendServerMessage($socket,$message) {
	// Xavier Basty - 2005/02/07 - Fix for Lotus Notes SMTP
	//eDebug("Sending Server Message");
	//eDebug("VARDUMP of message sent:");
	//eDebug($message);
	if (substr($message,-2,2) != "\r\n") $message .="\r\n";
	if ($message != null) fwrite($socket,$message);
}

/** exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 * @param $socket
 */
function exponent_smtp_sendExit($socket) {
	//eDebug("Sending RSET to server");
	exponent_smtp_sendServerMessage($socket,"RSET");
	//eDebug("Sending QUIT to server");
	exponent_smtp_sendServerMessage($socket,"QUIT");
}

/** exdoc
 * Sends the header part of an email message to the server.  This takes
 * care of properly escaping newlines as \r\n escape characters
 *
 * @param Socket $socket The socket object connected to the mail server
 * @param Array $headers An associative array of header keys to header values
 * @node Subsystems:SMTP
 */
function exponent_smtp_sendHeadersPart($socket,$headers) {
	$headerstr = "";
	foreach ($headers as $key=>$value) {
		$headerstr .= $key.": ". $value . "\r\n";
	}
	//eDebug("Sending email headers to server:");
	//eDebug("VARDUMP of headers sent:");
	//eDebug($headerstr);
	exponent_smtp_sendServerMessage($socket,$headerstr);
}

/** exdoc
 * Sends the message part of an email message to the server.  This takes
 * care of properly (and intelligently) escaping newlines as \r\n escape characters.
 *
 * @param Socket $socket The socket object connected to the server
 * @param string $message The body of the email.
 * @node Subsystems:SMTP
 */
function exponent_smtp_sendMessagePart($socket,$message) {
	// Replace lonely \n characters with \r\n, but not replacing the \n component of a valid \r\n
	$message = str_replace("\n","\r\n",str_replace("\r\n","\n",$message));
	// Replace lines with a single period with double periods, to prevent premature end of message situations.
	$message = preg_replace("/\n\.\r\n/","\n..\r\n",$message);
	
	//eDebug("Sending email message to server:");
	//eDebug("VARDUMP of message body sent:");
	//eDebug($message);
	exponent_smtp_sendServerMessage($socket,$message);
}

/** exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 * @param $socket
 */
function exponent_smtp_parseEHLO($socket) {
	
}

/** exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 * @param $socket
 * @param $type
 * @param $username
 * @param $password
 * @return bool
 */
function exponent_smtp_authenticate($socket,$type,$username,$password) {
	exponent_smtp_sendServerMessage($socket,"AUTH $type");
	if (exponent_smtp_checkResponse($socket,"334")) {
		switch ($type) {
			case "LOGIN":
				// Code shamelessly ripped from PEAR
				exponent_smtp_sendServerMessage($socket,base64_encode($username));
				if (!exponent_smtp_checkResponse($socket,"334")) {
					return false;
				}
				exponent_smtp_sendServerMessage($socket,base64_encode($password));
				break;
			case "PLAIN":
				exponent_smtp_sendServerMessage($socket,base64_encode(chr(0).$username.chr(0).$password));
				break;
		}
		
		return exponent_smtp_checkResponse($socket,"235");
	} else {
		return false;
	}
}

/** exdoc
 * A blank callback function that shows external argument list
 *
 * @param integer $email_index The numerical index of the email address
 * @param string $msg The text of the message.  This is a modifiable referenced variable
 * @param string $subject The subject of the message.  This is a modifiable referenced variable
 * @param array $headers The headers array.  This is a modifiable referenced variable.
 * @node Subsystems:SMTP
 */
function exponent_smtp_blankMailCallback($email_index,&$msg,&$subject,&$headers) {
	//do general stuff
	
			
}

?>
