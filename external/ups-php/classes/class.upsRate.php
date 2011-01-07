<?php

class upsRate {
	var $requestXML;
	var $shipmentXML;
	var $rateInformationXML;
	var $shipperXML;
	var $shipToXML;	
	var $packageXML;	
	var $packageDimensionsXML;	
	var $packageWeightXML;	

	var $xmlSent;
	var $rateResponse;
	
	function upsRate($upsObj) {
		// Must pass the UPS object to this class for it to work
		$this->ups = $upsObj;
	}
	
	// Main function that puts together all the XML builder function variables.  Builds the final XML for Rate calculation
	function sendRateRequest() {
		// First part of XML is the access part,
		$xml = $this->ups->access();
		$content = $this->requestXML;


		$content .= $this->shipmentXML;
		
		$xml .= $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Main.xml', array('{CONTENT}'), array($content));

		# Put the xml send to UPS into a variable so we can call it later for debugging purposes
		$this->xmlSent = $xml;

		$responseXML = $this->ups->request('Rate', $xml);
		#$xmlParser = new XML2Array();
		#$fromUPS = $xmlParser->parse($responseXML);
		$xmlParser = new upsxmlParser();
		$fromUPS = $xmlParser->xmlparser($responseXML);
		$fromUPS = $xmlParser->getData();

		$this->rateResponse = $fromUPS;
		return $fromUPS;
	}

	// Build Request XML
	function request($params) {
		if ($params['Shop']) {
			$request = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Request.xml', array('{RATE_OPTION}'), array('Shop')); 
		} else {
			$request = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Request.xml', array('{RATE_OPTION}'), array('Rate')); 
		}
		$this->requestXML = $request;
		return $request;
	}

	// Build the shipment XML
	function shipment($params) {
		$shipment = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Shipment.xml', array('{SHIPMENT_DESCRIPTION}','{SHIPPING_CODE}','{SHIPMENT_CONTENT}'), array($params['description'],$params['serviceType'],$this->shipperXML. $this->shipToXML. $this->packageXML. $this->rateInformationXML));
		
		$this->shipmentXML = $shipment;
		return $shipment;
	}

	// Build Rate Information and Negotiated Rate XML
	function rateInformation($params) {
		$rateInformation = '';
		if ($params['NegotiatedRatesIndicator'] == 'yes'){
			$rateInformation = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_RateInformation.xml', array('{NEGOTIATED_RATES_INDICATOR}'), array(''));
		}
		$this->rateInformationXML = $rateInformation;
		return $rateInformation;
	}

	// Build the shipper XML
	function shipper($params) {
		$shipper = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Shipper.xml', array('{SHIPPER_NAME}',
			'{SHIPPER_PHONE}',
			'{SHIPPER_NUMBER}',
			'{SHIPPER_ADDRESS_1}',
			'{SHIPPER_ADDRESS_2}',
			'{SHIPPER_ADDRESS_3}',
			'{SHIPPER_CITY}',
			'{SHIPPER_STATE}',
			'{SHIPPER_POSTAL_CODE}',
			'{SHIPPER_COUNTRY_CODE}'), array($params['name'],$params['phone'],
										$params['shipperNumber'],$params['address1'],
										$params['address2'],$params['address3'],
										$params['city'],$params['state'],
										$params['postalCode'],$params['country']));
		$this->shipperXML = $shipper;
		return $shipper;
	}

	// Build the shipTo XML
	function shipTo($params) {
		$shipTo = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_ShipTo.xml', array('{SHIPTO_COMPANY_NAME}',
			'{SHIPTO_ATTN_NAME}',
			'{SHIPTO_PHONE}',
			'{SHIPTO_ADDRESS_1}',
			'{SHIPTO_ADDRESS_2}',
			'{SHIPTO_ADDRESS_3}',
			'{SHIPTO_CITY}',
			'{SHIPTO_STATE}',
			'{SHIPTO_POSTAL_CODE}',
			'{SHIPTO_COUNTRY_CODE}'), array($params['companyName'],$params['attentionName'],
										$params['phone'],$params['address1'],$params['address2'],
										$params['address3'],$params['city'],$params['state'],
										$params['postalCode'],$params['countryCode']));
		$this->shipToXML = $shipTo;
		return $shipTo;
	}

	// Build the package XML
	function package($params) {
		$package = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_Package.xml', array('{PACKAGE_DESCRIPTION}',
			'{PACKAGING_CODE}','{PACKAGE_SIZE}','{PACKAGE_EXTRAS}'), array($params['description'],$params['code'],$this->packageDimensions(array('length' => $params['length'], 'width' => $params['width'], 'height' => $params['height'])). $this->packageWeight(array('weight' => $params['weight'])),''));

		$this->packageXML .= $package;
		return $package;
	}

	// Build the packageDimensions XML
	function packageDimensions($params) {
		$packageDimensions = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_PackageDimensions.xml', array('{PACKAGE_LENGTH}',
			'{PACKAGE_WIDTH}',
			'{PACKAGE_HEIGHT}'), array($params['length'],$params['width'],$params['height']));

		$this->packageDimensionsXML = $packageDimensions;
		return $packageDimensions;
	}

	// Build packageWeight XML
	function packageWeight($params) {
		$packageWeight = $this->ups->sandwich($this->ups->templatePath.'Rates/RatingServiceSelection_PackageWeight.xml', array('{PACKAGE_WEIGHT}'), array($params['weight'])); 

		$this->packageWeightXML = $packageWeight;
		return $packageWeight;
	}

	

	// Output the entire array of XML returned by UPS
	function returnResponseArray() {
		$rateResponse = $this->rateResponse;
		return $rateResponse;
	}

	function isResponseError() {
		$rateResponse = $this->rateResponse;
		$responseStatusCode = $rateResponse['RatingServiceSelectionResponse']['Response']['ResponseStatusCode']['VALUE'];

		if ($responseStatusCode < 1) {
			return true;
		} else {
			return false;
		}
	}

	// Return the total monitary value of the service
	function returnRate() {
		$rateResponse = $this->rateResponse;
		$error = $rateResponse['RatingServiceSelectionResponse']['Response']['Error']['ErrorDescription']['VALUE'];
		if ($this->isResponseError()) {
			$this->ups->throwError("There was an error and UPS said, '$error'.");
		} else {
			return $rateResponse['RatingServiceSelectionResponse']['RatedShipment']['TotalCharges']['MonetaryValue']['VALUE'];
		}
	}

	// Find out if there are multiple rates in a response (user is shoping for rates)
	function isMultipleRates() {
		$rateResponse = $this->rateResponse;
		
		if ($rateResponse['RatingServiceSelectionResponse']['RatedShipment'][0] == null) { //If there is a value here there are multiple rates
			return false;
		} else {
			return true;
		}
	}

		
}
?>
