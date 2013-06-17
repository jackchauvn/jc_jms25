<?php
/// <summary>
/// Summary description for GatewayConnector.
/// Copyright Web Active Corporation Pty Ltd  - All rights reserved. 1998-2006
/// This code is for exclusive use with the eWAY payment gateway
/// </summary>
class GatewayConnector {
      /// <summary>
      /// The Uri of the Eway payment gateway
      /// </summary>

      /// <summary>
      /// Do the post to the gateway and retrieve the response
      /// </summary>
      /// <param name="GatewayRequest"></param>
      /// <returns></returns>
     
	var $response = "";
	var $uri = "";
	var $timeout = 36000;

	function GatewayConnector($ewayMode)
	{		
		if ($ewayMode) {
			$this->uri = 'https://www.eway.com.au/gateway/rebill/upload.aspx' ;
		} else {
			$this->uri = 'https://www.eway.com.au/gateway/rebill/test/Upload_test.aspx' ;
		} 					 	
	}
	/**
	 * Set the uri of the gateway	 
	 * @param string $value
	 */	
	function Uri($value) {
         $this->uri = $value;       
    }
    /**
     * Set titme out for the connector     
     * @param int $value
     */
    function ConnectionTimeout($value) {
        $this->timeout = $value; 
    }
	/**
	 * Get response from the payment gateway
	 * 
	 */
    function Response() {
        return $this->response;       
    }	
    /**
     * Send request to Eway payment gateway     
     * @param unknown_type $request
     */
	function ProcessRequest($request) {
		
		$requestxml = $request->ToXML();				
       	$ch = curl_init();
       	curl_setopt($ch, CURLOPT_URL,$this->uri);
       	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       	curl_setopt($ch, CURLOPT_TIMEOUT, 36000);      	
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestxml); 
       	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);		
		// Send the data out over the wire
		$data = curl_exec($ch);		       
		if (curl_errno($ch)) {
			// Net connection failed
			// try and get the error text
           	print curl_error($ch);
			return false;
       	} 
		else {
           	curl_close($ch);			
			$this->response = new RebillResponse($data);       			    	
			return true;
		}
	}	
}
