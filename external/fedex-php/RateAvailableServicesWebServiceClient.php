<?php

// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 9.0.0

//require_once(BASE.'external/fedex-php/fedex-common.php');
require_once('fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "RateService_v9.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");
 
$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                            array(  'Key' => getProperty('key'), 
                                                    'Password' => getProperty('password')
                                                    )
                                            ); 
$request['ClientDetail'] = array('AccountNumber' => getProperty('shipaccount'), 'MeterNumber' => getProperty('meter'));
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***');
$request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
$request['ReturnTransitAndCommit'] = true;
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
$request['RequestedShipment']['ShipTimestamp'] = date('c');
// Service Type and Packaging Type are not passed in the request
$request['RequestedShipment']['Shipper'] = array('Address'=>getProperty('address1'));
$request['RequestedShipment']['Recipient'] = array('Address'=>getProperty('address2'));
$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                        'Payor' => array('AccountNumber' => getProperty('billaccount'), // Replace 'XXX' with payor's account number
                                                                     'CountryCode' => 'US'));
$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
$request['RequestedShipment']['PackageCount'] = '2';
$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
$request['RequestedShipment']['RequestedPackageLineItems'] = array('0' => array('Weight' => array('Value' => 2.0,
                                                                                    'Units' => 'LB'),
                                                                                    'Dimensions' => array('Length' => 10,
                                                                                        'Width' => 10,
                                                                                        'Height' => 3,
                                                                                        'Units' => 'IN')),
                                                                   '1' => array('Weight' => array('Value' => 5.0,
                                                                                    'Units' => 'LB'),
                                                                                    'Dimensions' => array('Length' => 20,
                                                                                        'Width' => 20,
                                                                                        'Height' => 10,
                                                                                        'Units' => 'IN')));

try 
{
	if(setEndpoint('changeEndpoint'))
	{
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client ->getRates($request);
        
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
        echo 'Rates for following service type(s) were returned.'. Newline. Newline;
        echo '<table border="1">';
        echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td>';
        foreach ($response -> RateReplyDetails as $rateReply)
        {           
        	echo '<tr>';
        	$serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
        	$amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
        	if(array_key_exists('DeliveryTimestamp',$rateReply)){
        		$deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
        	}else{
        		$deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
        	}
        	echo $serviceType . $amount. $deliveryDate;
        	echo '</tr>';
        }
        echo '</table>'. Newline;
    	printSuccess($client, $response);
    }
    else
    {
        printError($client, $response); 
    } 
    
    writeToLog($client);    // Write to log file   

} catch (SoapFault $exception) {
   printFault($exception, $client);        
}

?>