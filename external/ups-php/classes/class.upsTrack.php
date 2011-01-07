<?php
class upsTrack {
	var $xmlSent;
	var $trackResponse;
	
	function upsTrack($upsObj){
		// Must pass the UPS object to this class for it to work
		$this->ups = $upsObj;
	}
	
	function track($trackingNumber){
		$xml = $this->ups->access();
		$xml .= $this->ups->sandwich($this->ups->templatePath.'Tracking/TrackRequest.xml', array('{TRACKING_NUMBER}'), array($trackingNumber));

		// Put the xml that is sent do UPS into a variable so we can call it later for debugging.
		$this->xmlSent = $xml;

		$responseXML = $this->ups->request('Track', $xml);

		$xmlParser = new upsxmlParser();
		$fromUPS = $xmlParser->xmlparser($responseXML);
		$fromUPS = $xmlParser->getData();

		$this->trackResponse = $fromUPS;
	return $fromUPS;
	}

	// Output the entire array of XML returned by UPS
	function returnResponseArray() {
		$trackResponse = $this->trackResponse;
		return $trackResponse;
	}

	function isResponseError() {
		$rateResponse = $this->rateResponse;
		$responseStatusCode = $rateResponse['TrackResponse']['Response']['ResponseStatusCode']['VALUE'];

		if ($responseStatusCode < 1) {
			return true;
		} else {
			return false;
		}
	}

}
?>
