<?php
/**
 * @version		1.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
define( 'EWAY_DEFAULT_GATEWAY_URL', 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp' );
define( 'EWAY_DEFAULT_CUSTOMER_ID', '87654321' );
define( 'EWAY_CURL_ERROR_OFFSET', 1000 );
define( 'EWAY_XML_ERROR_OFFSET',  2000 );
define( 'EWAY_TRANSACTION_OK',       0 );
define( 'EWAY_TRANSACTION_FAILED',   1 );
define( 'EWAY_TRANSACTION_UNKNOWN',  2 );
/**
 * Eway payment class
 *
 */
class iwl_eway {
    /**
	 * Payment gateway mode : Test or Live	 
	 * @var int
	 */
	var $eway_mode = 0;
	
	/**
	 * XML parse
	 *
	 * @var object
	 */
    var $parser;
    /**
     * XML data passed to eway
     *
     * @var string
     */
    var $xmlData;
    /**
     * 
     *
     * @var string
     */
    var $currentTag;
    /**
     * Gateway URL to process payment
     *
     * @var string
     */
    var $myGatewayURL;
    /**
     * Eway customer ID
     *
     * @var string
     */
    var $myCustomerID;
    /**
     * Total amount
     *
     * @var Double
     */
    var $myTotalAmount;
    /**
     * Customer first name
     *
     * @var string
     */        
    var $myCustomerFirstname;
    /**
     * Customer last name
     *
     * @var string
     */
    var $myCustomerLastname;
    /**
     * Customer email
     *
     * @var string
     */
    var $myCustomerEmail;
    /**
     * Customer address
     *
     * @var string
     */
    var $myCustomerAddress;
    /**
     * Customer postcode
     *
     * @var string
     */
    var $myCustomerPostcode;
    /**
     * Invoice description
     *
     * @var string
     */
    var $myCustomerInvoiceDescription;
    /**
     * Invoice reference : Order ID in our system
     *
     * @var string
     */
    var $myCustomerInvoiceRef;
    /**
     * Cart holders name
     *
     * @var string
     */
    var $myCardHoldersName;
    /**
     * Card numbder
     *
     * @var string
     */
    var $myCardNumber;
    /**
     * Card expiration month
     *
     * @var string
     */
    var $myCardExpiryMonth;
    /**
     * Card Expiration Year
     *
     * @var string
     */
    var $myCardExpiryYear;
    /**
     * Card CVN
     *
     * @var string
     */
    var $myCardCVN;
    /**
     * Transaction Number
     *
     * @var string
     */
    var $myTrxnNumber;
    /**
     * Option 1
     *
     * @var string
     */
    var $myOption1;
    /**
     * Option 2
     *
     * @var string
     */
    var $myOption2;
    /**
     * Option 3
     *
     * @var string
     */
    var $myOption3;
    /**
     * Transaction Status
     *
     * @var string
     */    
    var $myResultTrxnStatus;
    /**
     * Trasnaction Number
     *
     * @var string
     */
    var $myResultTrxnNumber;
    /**
     * Result option 1
     *
     * @var string
     */
    var $myResultTrxnOption1;
    /**
     * Result option 2
     *
     * @var string
     */
    var $myResultTrxnOption2;
    /**
     * Result option 3
     *
     * @var string
     */
    var $myResultTrxnOption3;
    /**
     * Result Reference
     *
     * @var string
     */
    var $myResultTrxnReference;
    /**
     * 
     *
     * @var string
     */
    var $myResultTrxnError;
    /**
     * *
     *
     * @var String
     */
    var $myResultAuthCode;
    /**
     * Amount
     *
     * @var string
     */
    var $myResultReturnAmount;
    /**
     *
     *
     * @var String
     */
	var $myCardName;
	/**
	 * Error
	 *
	 * @var string
	 */	    
    var $myError;
    /**
     * Error message
     *
     * @var string
     */
    var $myErrorMessage;
    /***********************************************************************
     *** Class Constructor                                               ***
     ***********************************************************************/
    function iwl_eway($config) {
		
		$payment_settings = $config->get('payment_settings');
		
        $this->myCustomerID = $payment_settings->eway_customer_id;
        $ewayMode = $payment_settings->eway_mode;
        $this->eway_mode = $ewayMode;
		
        if ($ewayMode == 1) {
        	$gatewayURL = 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp';
        } else {
        	//Test mode
        	$gatewayURL = 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp' ;        		
        }
        $this->myGatewayURL = $gatewayURL;
    }
    /***********************************************************************
     *** XML Parser - Callback functions                                 ***
     ***********************************************************************/
    function epXmlElementStart ($parser, $tag, $attributes) {
        $this->currentTag = $tag;
    }
    
    function epXmlElementEnd ($parser, $tag) {
        $this->currentTag = "";
    }
    
    function epXmlData ($parser, $cdata) {
        $this->xmlData[$this->currentTag] = $cdata;
    }
    
    /***********************************************************************
     *** SET values to send to eWAY                                      ***
     ***********************************************************************/
    function setCustomerID( $customerID ) {
        $this->myCustomerID = $customerID;
    }
    
    function setTotalAmount( $totalAmount ) {
        $this->myTotalAmount = $totalAmount;
    }
    
    function setCustomerFirstname( $customerFirstname ) {
        $this->myCustomerFirstname = $customerFirstname;
    }
    
    function setCustomerLastname( $customerLastname ) {
        $this->myCustomerLastname = $customerLastname;
    }
    
    function setCustomerEmail( $customerEmail ) {
        $this->myCustomerEmail = $customerEmail;
    }
    
    function setCustomerAddress( $customerAddress ) {
        $this->myCustomerAddress = $customerAddress;
    }
    
    function setCustomerPostcode( $customerPostcode ) {
        $this->myCustomerPostcode = $customerPostcode;
    }
    
    function setCustomerInvoiceDescription( $customerInvoiceDescription ) {
        $this->myCustomerInvoiceDescription = $customerInvoiceDescription;
    }
    
    function setCustomerInvoiceRef( $customerInvoiceRef ) {
        $this->myCustomerInvoiceRef = $customerInvoiceRef;
    }
    
    function setCardHoldersName( $cardHoldersName ) {
        $this->myCardHoldersName = $cardHoldersName;
    }
    
    function setCardNumber( $cardNumber ) {
        $this->myCardNumber = $cardNumber;
    }
    
    function setCardExpiryMonth( $cardExpiryMonth ) {
        $this->myCardExpiryMonth = $cardExpiryMonth;
    }
    
    function setCardExpiryYear( $cardExpiryYear ) {
        $this->myCardExpiryYear = $cardExpiryYear;
    }
    
    function setCardCVN( $cardCVN ) {
        $this->myCardCVN = $cardCVN;
    }
    
    function setTrxnNumber( $trxnNumber ) {
        $this->myTrxnNumber = $trxnNumber;
    }
    
    function setOption1( $option1 ) {
        $this->myOption1 = $option1;
    }
    
    function setOption2( $option2 ) {
        $this->myOption2 = $option2;
    }
    
    function setOption3( $option3 ) {
        $this->myOption3 = $option3;
    }

    /***********************************************************************
     *** GET values returned by eWAY                                     ***
     ***********************************************************************/
    function getTrxnStatus() {
        return $this->myResultTrxnStatus;
    }
    
    function getTrxnNumber() {
        return $this->myResultTrxnNumber;
    }
    
    function getTrxnOption1() {
        return $this->myResultTrxnOption1;
    }
    
    function getTrxnOption2() {
        return $this->myResultTrxnOption2;
    }
    
    function getTrxnOption3() {
        return $this->myResultTrxnOption3;
    }
    
    function getTrxnReference() {
        return $this->myResultTrxnReference;
    }
    
    function getTrxnError() {
        return $this->myResultTrxnError;
    }
    
    function getAuthCode() {
        return $this->myResultAuthCode;
    }
    
    function getReturnAmount() { 
        return $this->myResultReturnAmount;
    }

    function getError() {
		
        if( $this->myError != 0 ) {
            // Internal Error
            return $this->myError;
        } else {
            // eWAY Error
            if( $this->getTrxnStatus() == 'True' ) {
                return EWAY_TRANSACTION_OK;
            } elseif ( $this->getTrxnStatus() == 'False' ) {
                return EWAY_TRANSACTION_FAILED;
            } else {
                return EWAY_TRANSACTION_UNKNOWN;
            }
        }
    }

    function getErrorMessage()
    {
        if( $this->myError != 0 ) {
            // Internal Error
            return $this->myErrorMessage;
        } else {
            // eWAY Error
            return $this->getTrxnError();
        }
    }        
    /***********************************************************************
     *** Business Logic                                                  ***
     ***********************************************************************/
    function processPayment($data) {
  
		$this->setCustomerEmail($data['email']);		
		$this->setCustomerInvoiceDescription($data['item_name']);
		$this->setCustomerInvoiceRef($data['id']);
		$this->setCardHoldersName($data['eway_card_holder_name']);
		$this->setCardNumber($data['eway_card_num']);
		$expDate = $data['eway_exp_date'];
		$expDateArr = explode('/', $expDate);
		$this->setCardExpiryMonth($expDateArr[0]);
		$this->setCardExpiryYear($expDate[1]);
		$this->setCardCVN($data['eway_card_code']);
		$this->setTrxnNumber($data['transaction_id']);
		$this->setTotalAmount($data['price']*100);
        $xmlRequest = "<ewaygateway>".
                "<ewayCustomerID>".htmlentities( $this->myCustomerID )."</ewayCustomerID>".
                "<ewayTotalAmount>".htmlentities( $this->myTotalAmount)."</ewayTotalAmount>".
                "<ewayCustomerFirstName>".htmlentities( $this->myCustomerFirstname )."</ewayCustomerFirstName>".
                "<ewayCustomerLastName>".htmlentities( $this->myCustomerLastname )."</ewayCustomerLastName>".
                "<ewayCustomerEmail>".htmlentities( $this->myCustomerEmail )."</ewayCustomerEmail>".
                "<ewayCustomerAddress>".htmlentities( $this->myCustomerAddress )."</ewayCustomerAddress>".
                "<ewayCustomerPostcode>".htmlentities( $this->myCustomerPostcode )."</ewayCustomerPostcode>".
                "<ewayCustomerInvoiceDescription>".htmlentities( $this->myCustomerInvoiceDescription )."</ewayCustomerInvoiceDescription>".
                "<ewayCustomerInvoiceRef>".htmlentities( $this->myCustomerInvoiceRef )."</ewayCustomerInvoiceRef>".
                "<ewayCardHoldersName>".htmlentities( $this->myCardName )."</ewayCardHoldersName>".
                "<ewayCardNumber>".htmlentities( $this->myCardNumber )."</ewayCardNumber>".
                "<ewayCardExpiryMonth>".htmlentities( $this->myCardExpiryMonth )."</ewayCardExpiryMonth>".
                "<ewayCardExpiryYear>".htmlentities( $this->myCardExpiryYear )."</ewayCardExpiryYear>".
                "<ewayTrxnNumber>".htmlentities( $this->myTrxnNumber )."</ewayTrxnNumber>".
                "<ewayOption1>".htmlentities( $this->myOption1 )."</ewayOption1>".
                "<ewayOption2>".htmlentities( $this->myOption2 )."</ewayOption2>".
                "<ewayOption3>".htmlentities( $this->myOption3 )."</ewayOption3>".
                "<ewayCVN>".htmlentities( $this->myCardCVN )."</ewayCVN>".
                "</ewaygateway>";

        /* Use CURL to execute XML POST and write output into a string */
        $ch = curl_init( $this->myGatewayURL );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xmlRequest );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 240 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $xmlResponse = curl_exec( $ch );
		//exit;
        
        // Check whether the curl_exec worked.
        if( curl_errno( $ch ) == CURLE_OK ) {
            // It worked, so setup an XML parser for the result.
            $this->parser = xml_parser_create();
            
            // Disable XML tag capitalisation (Case Folding)
            xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
            
            // Define Callback functions for XML Parsing
            xml_set_object($this->parser, &$this);
            xml_set_element_handler ($this->parser, "epXmlElementStart", "epXmlElementEnd");
            xml_set_character_data_handler ($this->parser, "epXmlData");
            
            // Parse the XML response
            xml_parse($this->parser, $xmlResponse, TRUE);
            
            if( xml_get_error_code( $this->parser ) == XML_ERROR_NONE ) {
				
				// Get the result into local variables.
                $this->myResultTrxnStatus = isset($this->xmlData['ewayTrxnStatus']) ? $this->xmlData['ewayTrxnStatus']:"";
                $this->myResultTrxnNumber = isset($this->xmlData['ewayTrxnNumber']) ? $this->xmlData['ewayTrxnNumber']:"";
                $this->myResultTrxnOption1 = isset($this->xmlData['ewayTrxnOption1']) ? $this->xmlData['ewayTrxnOption1']:"";
                $this->myResultTrxnOption2 = isset($this->xmlData['ewayTrxnOption2']) ? $this->xmlData['ewayTrxnOption2']:"";
                $this->myResultTrxnOption3 = isset($this->xmlData['ewayTrxnOption3']) ? $this->xmlData['ewayTrxnOption3']:"";
                $this->myResultTrxnReference = isset($this->xmlData['ewayTrxnReference']) ? $this->xmlData['ewayTrxnReference']:"";
                $this->myResultAuthCode = isset($this->xmlData['ewayAuthCode']) ? $this->xmlData['ewayAuthCode']:"";
                $this->myResultReturnAmount = isset($this->xmlData['ewayReturnAmount']) ? $this->xmlData['ewayReturnAmount']:"";
                $this->myResultTrxnError = isset($this->xmlData['ewayTrxnError']) ? $this->xmlData['ewayTrxnError']:"";
                $this->myError = 0;
                $this->myErrorMessage = '';
				
				
            } else {
                // An XML error occured. Return the error message and number.
                $this->myError = xml_get_error_code( $this->parser ) + EWAY_XML_ERROR_OFFSET;
                $this->myErrorMessage = xml_error_string( $myError );
            }
            // Clean up our XML parser
            xml_parser_free( $this->parser );
        } else {
            // A CURL Error occured. Return the error message and number. (offset so we can pick the error apart)
            $this->myError = curl_errno( $ch ) + EWAY_CURL_ERROR_OFFSET;
            $this->myErrorMessage = curl_error( $ch );
        }
        // Clean up CURL, and return any error.
        curl_close( $ch );
        $result = $this->getError();
		
        if($result == EWAY_TRANSACTION_OK){
        	JRequest::setVar('layout', 'complete');
        	JRequest::setVar('plan_id', $data['plan_id']);
			return true;
        } else {
        	JRequest::setVar('layout', 'failure');
        	JRequest::setVar('reason', $this->getErrorMessage());
			return false;
        }
    }
    
/**
     * 
     * Connecting to payment gateway to process recurring payment
     * @param object $row the donation record onject
     * @param array $data posted data from confirmation page
     */    
    function processRecurringPayment($data, $plan) {

		require_once JPATH_PLUGINS . '/jmspayment/eway/eway/RebillPayment.php';
		require_once JPATH_PLUGINS . '/jmspayment/eway/eway/RebillResponse.php';
		require_once JPATH_PLUGINS . '/jmspayment/eway/eway/GatewayConnector.php';
		
		$initialAmount = 0;
		$initialDate = date('d/m/Y');
		switch ($plan->period_type) {
			case '1':
				$interval = $plan->period;
				$intervalType = 1; //Days
				$unit = 'days';
				break;
			case '2':
				$interval = $plan->period;
				$intervalType = 2; //Weeks
				$unit = 'weeks';
				break;
			case '3':
				$interval = $plan->period;
				$intervalType = 3; //Months
				$unit = 'months';
				break;
			case '4':
				$interval = $plan->period;
				$intervalType = '4';
				$unit = 'years';
				break;
		}
    	if (isset($row->r_times) && $row->r_times > 2) {
			$totalOccurences = $row->r_times;
		} else {
			$totalOccurences = 100;
		}
		
		//Calculate end date
		$numberUnit = $totalOccurences * $interval;
		$endDate = date('d/m/Y', strtotime('+'."$numberUnit $unit"));							
		$objRebill = new RebillPayment();
		$objRebill->eWAYCustomerID($this->myCustomerID);
        $objRebill->CustomerFirstName($data['first_name']);
        $objRebill->CustomerLastName($data['last_name']);                              
        //$objRebill->CustomerState($row->state);
        //$objRebill->CustomerPostCode($row->zip);
        //$objRebill->CustomerCountry($row->country);
        $objRebill->RebillCCName($data['eway_card_holder_name']);
        $objRebill->RebillCCNumber($data['eway_card_num']);        
        $expDate = $data['eway_exp_date'];
		$expDateArr = explode('/', $expDate);        
        $objRebill->RebillCCExpMonth($expDateArr[0]);
        $objRebill->RebillCCExpYear($expDateArr[1]);
        $objRebill->RebillInitAmt($initialAmount);
        $objRebill->RebillInitDate($initialDate);
        $objRebill->RebillRecurAmt($data['price']*100);
        $objRebill->RebillStartDate($initialDate);
        $objRebill->RebillInterval($interval);
        $objRebill->RebillIntervalType($intervalType);
        $objRebill->RebillEndDate($endDate);
        $objConnector = new GatewayConnector($this->eway_mode);        
        $approved = false;
		
    	if ($objConnector->ProcessRequest($objRebill)) {
            $objResponse = $objConnector->Response();  
			                     
            if ($objResponse != null) {
                $lblResult = $objResponse->Result();
                if ($lblResult == 'Success') {
                	$approved = true;
                } else {                	
                	$errorDetails = $objResponse->ErrorDetails();
                }                                
            }
        }        
		if ($approved) {
			
			JRequest::setVar('layout', 'complete');
        	JRequest::setVar('plan_id', $data['plan_id']);
			
			return true;
		} else {
			
			JRequest::setVar('layout', 'failure');
        	JRequest::setVar('reason', "Error occurred (".$this->getError()."): " . $this->getErrorMessage());
			
			return false;						
		}		
    }
} 
?>