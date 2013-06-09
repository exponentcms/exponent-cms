<?php

// Copyright 2009, FedEx Corporation. All rights reserved.

//define('TRANSACTIONS_LOG_FILE', '../fedextransactions.log');  // Transactions log file
define('TRANSACTIONS_LOG_FILE', '../../tmp/fedextransactions.log');

/**
 *  Print SOAP request and response
 */
define('Newline',"<br />");

function printSuccess($client, $response) {
    echo '<h2>Transaction Successful</h2>';  
    echo "\n";
    printRequestResponse($client);
}
function printRequestResponse($client){
	echo '<h2>Request</h2>' . "\n";
	echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
	echo "\n";
   
	echo '<h2>Response</h2>'. "\n";
	echo '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
	echo "\n";
}

/**
 *  Print SOAP Fault
 */  
function printFault($exception, $client) {
    echo '<h2>Fault</h2>' . "<br>\n";                        
    echo "<b>Code:</b>{$exception->faultcode}<br>\n";
    echo "<b>String:</b>{$exception->faultstring}<br>\n";
    writeToLog($client);
}

/**
 * SOAP request/response logging to a file
 */                                  
function writeToLog($client){  
if (!$logfile = fopen(TRANSACTIONS_LOG_FILE, "a"))
{
   error_func("Cannot open " . TRANSACTIONS_LOG_FILE . " file.\n", 0);
   exit(1);
}

fwrite($logfile, sprintf("\r%s:- %s",date("D M j G:i:s T Y"), $client->__getLastRequest(). "\n\n" . $client->__getLastResponse()));
}

/**
 * This section provides a convenient place to setup many commonly used variables
 * needed for the php sample code to function.
 */
function getProperty($var){
	if($var == 'check') Return false;
	if($var == 'shipaccount') Return 'XXX';
	if($var == 'billaccount') Return 'XXX';
	if($var == 'dutyaccount') Return 'XXX';
	if($var == 'accounttovalidate') Return 'XXX';
	if($var == 'meter') Return 'XXX';
	if($var == 'key') Return 'XXX';
	if($var == 'password') Return 'XXX';
	if($var == 'shippingChargesPayment') Return 'SENDER';
	if($var == 'internationalPaymentType') Return 'SENDER';
	if($var == 'readydate') Return '2010-05-31T08:44:07';
	if($var == 'readytime') Return '12:00:00-05:00';
	if($var == 'closetime') Return '20:00:00-05:00';
	if($var == 'closedate') Return date("Y-m-d");
	if($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
	if($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
	if($var == 'pickuplocationid') Return 'XXX';
	if($var == 'pickupconfirmationnumber') Return '1';
	if($var == 'dispatchdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
	if($var == 'dispatchtimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
	if($var == 'dispatchlocationid') Return 'XXX';
	if($var == 'dispatchconfirmationnumber') Return '1';	
	if($var == 'shiptimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
	if($var == 'tag_readytimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
	if($var == 'tag_latesttimestamp') Return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));	
	if($var == 'trackingnumber') Return 'XXX';
	if($var == 'trackaccount') Return 'XXX';
	if($var == 'shipdate') Return '2012-02-07';
	if($var == 'account') Return 'XXX';
	if($var == 'phonenumber') Return '1234567890';
	if($var == 'closedate') Return '2012-04-21';
	if($var == 'expirationdate') Return '2012-06-30';
	if($var == 'hubid') Return '5531';
	if($var == 'begindate') Return '2010-12-27';
	if($var == 'enddate') Return '2010-12-29';
	if($var == 'address1') Return array('StreetLines' => array('10 Fed Ex Pkwy'),
                                          'City' => 'Memphis',
                                          'StateOrProvinceCode' => 'TN',
                                          'PostalCode' => '38115',
                                          'CountryCode' => 'US');
	if($var == 'address2') Return array('StreetLines' => array('13450 Farmcrest Ct'),
                                          'City' => 'Herndon',
                                          'StateOrProvinceCode' => 'VA',
                                          'PostalCode' => '20171',
                                          'CountryCode' => 'US');
	if($var == 'locatoraddress') Return array(array('StreetLines'=>'240 Central Park S'),
										  'City'=>'Austin',
										  'StateOrProvinceCode'=>'TX',
										  'PostalCode'=>'78701',
										  'CountryCode'=>'US');
	if($var == 'searchlocationsaddress') Return array(array('StreetLines'=>'240 Central Park S'),
										  'City'=>'Austin',
										  'StateOrProvinceCode'=>'TX',
										  'PostalCode'=>'78701',
										  'CountryCode'=>'US');
	if($var == 'searchlocationphonenumber') Return '5555555555';
	if($var == 'recipientcontact') Return array('ContactId' => 'arnet',
										'PersonName' => 'Recipient Contact',
										'PhoneNumber' => '1234567890');
	if($var == 'freightaccount') Return '510087020';
	if($var == 'freightbilling') Return array(
		'Contact'=>array(
			'ContactId' => 'freight1',
			'PersonName' => 'Big Shipper',
			'Title' => 'Manager',
			'CompanyName' => 'Freight Shipper Co',
			'PhoneNumber' => '1234567890'
		),
		'Address'=>array(
			'StreetLines'=>array('1202 Chalet Ln', 'Do Not Delete - Test Account'),
			'City' =>'Harrison',
			'StateOrProvinceCode' => 'AR',
			'PostalCode' => '72601-6353',
			'CountryCode' => 'US'
		)
	);
}

function setEndpoint($var){
	if($var == 'changeEndpoint') Return false;
	if($var == 'endpoint') Return '';
}

function printNotifications($notes){
	foreach($notes as $noteKey => $note){
		if(is_string($note)){    
            echo $noteKey . ': ' . $note . Newline;
        }
        else{
        	printNotifications($note);
        }
	}
	echo Newline;
}

function printError($client, $response){
    echo '<h2>Error returned in processing transaction</h2>';
	echo "\n";
	printNotifications($response -> Notifications);
    printRequestResponse($client, $response);
}
function trackDetails($details, $spacer){
	foreach($details as $key => $value){
		if(is_array($value) || is_object($value)){
        	$newSpacer = $spacer. '&nbsp;&nbsp;&nbsp;&nbsp;';
    		echo '<tr><td>'. $spacer . $key.'</td><td>&nbsp;</td></tr>';
    		trackDetails($value, $newSpacer);
    	}elseif(empty($value)){
    		echo '<tr><td>'.$spacer. $key .'</td><td>&nbsp;</td></tr>';
    	}else{
    		echo '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
    	}
    }
}

?>