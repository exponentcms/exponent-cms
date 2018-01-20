<?php
class ezic_class extends transactionBase {
        private $accountID;
		private $securityCode;	
		protected $description;
        protected $transactionID;
        protected $postUrl;
        protected $postParams;
        protected $postResponse;
        protected $allowedTransactionTypes = array("A", "S", "C", "D", "B", "R");       //A - auth  S - sale  R - refund  C - credit  D - capture  B - batch settle
        protected $allowedPaymentTypes = array("C", "K", "S");  //C - credit card   K - check/ACH   S - stored value card

        public function __construct($accountNumber, $securityCode, $testMode, $ordernum = null, $transactionType = "S") {
		
        	$this->paymentType = "C";
        	$this->transactionType = $transactionType;
			$this->accountID    = $accountNumber;
			$this->securityCode = $securityCode;
			$this->description = "store.germanbliss.com Web Sale (Order # {$ordernum})";
			
			if($testMode) {
				$this->postUrl = "http://secure.ezic.com:1401/gw/sas/direct3.1";
			} else {
				$this->postUrl = "https://secure.ezic.com:1402/gw/sas/direct3.1";
			}
        }

		public function getResponse() {
				
			$respArr = explode("\n\r", $this->postResponse);
            $valid = preg_match('/^HTTP\/1\.[01] 200 OK/', $this->postResponse);
            if(!$valid) {
                    preg_match('/^HTTP\/1\.[01] (\d{3})\s*\d*:? (.+)\n/', $respArr[0], $matches);
                    if(strpos($matches[2], "Invalid credit card number:") === false) {
                            $this->errorMessage = trim($matches[2]) . ".";
                    }       
                    else
                            $this->errorMessage = "The credit card number provided was invalid.";
                    return false;   
            }

            $keyValPairs = explode("&", urldecode(trim($respArr[1])));
            $keyValSize = sizeof($keyValPairs);
            for($i = 0; $i < $keyValSize; ++$i) {
                    list($key, $val) = explode("=", $keyValPairs[$i]);
                    $$key = $val;
            }
			
			$obj = new stdClass();
			
			$obj->avs_code     		= $avs_code;
			$obj->cvv2_code   		= $cvv2_code;
			$obj->status_code 		= $status_code;
			$obj->processor 		= $processor;
			$obj->auth_code 		= $auth_code;
			$obj->settle_amount 	= $settle_amount;
			$obj->settle_currency 	= $settle_currency;
			$obj->trans_id 			= $trans_id;
			$obj->auth_msg 			= $auth_msg;
			$obj->auth_date			= $auth_date;
			
			
			return $obj;
			
		}

        public function getPostString() {
                $str = "";
                $str .= "account_id=" . urlencode($this->accountID);
				$str .= "&dynip_sec_code=" . urlencode($this->securityCode);
                $str .= "&tran_type=" . urlencode($this->transactionType);
                $str .= "&pay_type=" . urlencode($this->paymentType);
                if(!empty($this->description))
                        $str .= "&description=" . urlencode($this->description);
                $str .= "&amount=" . urlencode($this->amount);  
                $str .= "&card_number=" . urlencode($this->cardnumber);
                $str .= "&card_expire=". urlencode($this->expiration);
                $str .= "&card_cvv2=" . urlencode($this->cvv);
				$str .= "&disable_avs=" . urlencode("1");
                $str .= "&bill_name1=" . urlencode($this->fname);
                $str .= "&bill_name2=" . urlencode($this->lname);
                $str .= "&bill_street=" . urlencode($this->address);
                $str .= "&bill_city=" . urlencode($this->city);
                $str .= "&bill_state=" . urlencode($this->state);
                $str .= "&bill_zip=" . urlencode($this->zip);
                $str .= "&bill_country=" . urlencode($this->country);
                return $str;
        }

        private function parseResponse() {
                $respArr = explode("\n\r", $this->postResponse);
                $valid = preg_match('/^HTTP\/1\.[01] 200 OK/', $this->postResponse);
                if(!$valid) {
                        preg_match('/^HTTP\/1\.[01] (\d{3})\s*\d*:? (.+)\n/', $respArr[0], $matches);
                        if(strpos($matches[2], "Invalid credit card number:") === false) {
                                $this->errorMessage = trim($matches[2]) . ".";
                        }       
                        else
                                $this->errorMessage = "The credit card number provided was invalid.";
                        return false;   
                }

                $keyValPairs = explode("&", urldecode(trim($respArr[1])));
                $keyValSize = sizeof($keyValPairs);
                for($i = 0; $i < $keyValSize; ++$i) {
                        list($key, $val) = explode("=", $keyValPairs[$i]);
                        $$key = $val;
                }

                if($status_code == 1 || ($status_code == "T" && $this->transactionType == "A") || ($status_code == "I" && $this->paymentType == "K"))
                        return true;
                else {
                        if(preg_match("/[LWZ]/", $avs_code) || preg_match("/[ABOP]/", $avs_code) || in_array($avs_code, array("U", "R", "S", "E", "C", "I", "G")))
                $this->errorMessage = "An AVS (Address Verification Service) error was encountered.  Please check the address and zip/postal code provided and re-try.";
                        elseif(in_array($cvv2_code, array("N", "P", "U")))      
                $this->errorMessage = "A CVV2 (Card Verification Value) error was encountered.  Please check the secuity code provided and re-try.";
            elseif($auth_msg == "M/QUOTA EXCEEDED")
                $this->errorMessage = "Your payment has exceeded the maximum amount allowed to be submitted through an ACH transacion ($350.00).";
            elseif($auth_msg == "ACH/FAILED")
                $this->errorMessage = "The transaction was blocked through Fraud Barricade for Checks.";
            elseif($auth_msg == "ACH/RETURNED")
                $this->errorMessage = "The transaction has been returned from the bank for any number of reasons including: cannot locate account, non-sufficient funds, invalid account number, etc.";
            elseif($auth_msg == "ACH.CB/FAILED")
                $this->errorMessage = "The transaction is unable to be processed through the banking system.";
            elseif($auth_msg == "A/DECLINED")
                $this->errorMessage = "The transaction exceeded the traffic limit.";
            elseif($auth_msg == "B/DECLINED")
                $this->errorMessage = "The transaction contains information found in the blacklist.";
            elseif($auth_msg == "C/DECLINED")
                $this->errorMessage = "The country of origin is found in the high-risk country list.";
            elseif($auth_msg == "E/DECLINED")
                $this->errorMessage = "The email address in the transaction is not valid.";
            elseif($auth_msg == "I/DECLINED")
                $this->errorMessage = "The customer's stated billing address country does not match the country of the originating IP and/or the card issuer.";
            elseif($auth_msg == "J/DECLINED")
                $this->errorMessage = "The transaction contains profane or otherwise suspicious information.";
            elseif($auth_msg == "L/DECLINED")
                $this->errorMessage = "The transaction failed to pass the US Location Verification.";
            elseif($auth_msg == "R/DECLINED")
                $this->errorMessage = "The transaction contained a high risk country or came from an anonymous domain.";
            elseif($auth_msg == "A/QUOTA EXCEEDED")
                $this->errorMessage = "The transaction exceeded the maximum dollar volume limit per check. ";
            elseif($auth_msg == "C/QUOTA EXCEEDED")
                $this->errorMessage = "The transaction exceeded the maximum number of credits/refunds allowed within a time period. (contact your agent)";
            elseif($auth_msg == "R/QUOTA EXCEEDED")
                $this->errorMessage = "The transaction exceeded the maximum dollar volume of credits/refunds allowed within a time period. (contact your agent)";
            elseif($auth_msg == "S/QUOTA EXCEEDED")
                $this->errorMessage = "The transaction exceeded the maximum number of sales allowed within a time period. (contact your agent)";

                        if(empty($this->errorMessage))
                                $this->errorMessage = "The credit card transaction was declined.  Please check your billing informaiton and re-try.";

                        return false;
                }       
        }

        public function process() {
                $this->postParams = $this->getPostString();
                $request = curl_init();
                curl_setopt($request, CURLOPT_URL, $this->postUrl);
                curl_setopt($request, CURLOPT_POST, 1);
                curl_setopt($request, CURLOPT_POSTFIELDS, $this->postParams);
                curl_setopt($request, CURLOPT_HEADER, 1);
                curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($request, CURLOPT_TIMEOUT, 60);
                $this->postResponse = curl_exec($request);
                $curlError = curl_error($request);
                curl_close($request);

                if(!empty($curlError)) {
                        $this->errorMessage = $curlError;
                        return false;
                }       

                return $this->parseResponse();
        }
}
?>