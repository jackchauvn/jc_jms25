<?php

/*-------------------------------------------------------------------------------
# com_jms - JMS Membership Sites
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class iwl_paypal {
	/**
	 * Paypal mode
	 * @var boolean live mode : true, test mode : false
	 */
	var $_mode = 0;
	
	/**
	 * Paypal url
	 * @var string
	 */
	var $_url = null;

	/**
	 * Array of params will be posted to server
	 * @var string
	 */
	var $_params = array();
	
	/**
	 * Array containing data posted from paypal to our server
	 * @var array
	 */
	var $_data = array();
	
	/**
	 * Constructor functions, init some parameter
	 * @param object $config
	 */
	function __construct($config) {
		
		$payment_settings = $config->get('payment_settings');
		
		$this->_mode = $payment_settings->paypal_mode;
		
		if ($this->_mode) {
			$this->_url = 'https://www.paypal.com/cgi-bin/webscr';
		} else {
			$this->_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		$this->setParam('rm', 2);
		$this->setParam('cmd', '_xclick');
	}
	
	/**
	 * Set param value
	 * @param string $name
	 * @param string $val
	 */
	function setParam($name, $val) {
		$this->_params[$name] = $val;
	}

	/**
	 * Setup payment parameter
	 * @param array $params
	 */
	function setParams($params) {
		foreach ($params as $key => $value) {
			$this->_params[$key] = $value;
		}
	}

	/**
	 * Submit post to paypal server
	 *
	 */	
	function submitPost() {
		
	?>
		<div class="contentheading"><?php echo  JText::_('COM_JMS_WAIT_PAYPAL'); ?></div>
		<form method="post" action="<?php echo $this->_url; ?>" name="formsubscr" id="formsubscr">
			<?php
				foreach ($this->_params as $key=>$val) {
					echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
					echo "\n";
				}
			?>
			<script type="text/javascript">
				function redirect() {
					document.formsubscr.submit();
				}
				setTimeout('redirect()', 5000);
			</script>
		</form>
	<?php
	}

	/**
	 * Validate the post data from paypal to our server
	 *
	 * @return string
	 */
	function _validate($data) {
		
		//$post = JRequest::get('post');
		$req = 'cmd=' . urlencode('_notify-validate');
 
		foreach ($data as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$res = curl_exec($ch);
		curl_close($ch);
		
		if (strcmp ($res, "VERIFIED") == 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to process payment
	 *
	 * @param datetime $maxExpDate
	 * @return boolean
	 */
	function processPayment($data, $period, $periodType, $maxExpDate) {
		//$post = JRequest::get('post');
		$ret = $this->_validate($data);
		if ($ret) {
			$id = $data['custom'];
   			$transactionId = $data['txn_id'];
   			$amount = $data['mc_gross'];
   			if ($amount < 0)
   				return false;
   			$row =  JTable::getInstance('subscr', 'JmsTable');
   			$row->load($id);
   			$row->transaction_id = $transactionId;
   			$row->created = date('Y-m-d H:i:s');
			$row->expired = $this->_getExpiredDate($period, $periodType, $maxExpDate);
   			$row->state = 1;
   			$row->store();
   			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Method to process recurring payment
	 *
	 * @return
	 */
	function processRecurringPayment($data, $period, $periodType, $accessLimit, $maxExpDate) {
		//$post = JRequest::get('post');
		$ret = $this->_validate($data);				
		if ($ret) {
			$id = $data['custom'];						
   			$transactionId = $data['txn_id'];
   			$amount = $data['mc_gross'];
   			$txnType = $data['txn_type'];
   			$subscrId = $data['subscr_id'];
   			JRequest::setVar('txn_type', $txnType);   			
   			if ($amount < 0)
   				return false;
   			$row =  JTable::getInstance('subscr', 'JmsTable');
   			$row->load($id);   			
   			switch ($txnType) {
   				case 'subscr_signup':
   					$row->created = date('Y-m-d H:i:s');
   					$row->expired = $this->_getExpiredDate($period, $periodType, $maxExpDate);
		   			$row->transaction_id = $transactionId;		   			
		   			$row->state = 1;
		   			$row->payment_made = 1;
		   			$row->subscr_id = $subscrId;
		   			break;
   				case 'subscr_payment':
   					$row->expired = $this->_getExpiredDate($period, $periodType, $row->expired);
   					$row->access_limit = $row->access_limit + $accessLimit;
   					$row->payment_made = $row->payment_made + 1;
   					if ($row->payment_made > 1) {
   						$row->price = $row->price + $amount;
   					}
   					break;   				 
   			}			
			$row->store();		
   			return true;
		} else {
			return false;
		}		     
	}
	
	/**
	 * Private method to get expired date
	 *
	 * @param int $periodType
	 * @param datetime $maxExpDate
	 * @return datetime
	 */
	function _getExpiredDate($period, $periodType, $maxExpDate) {
		if ( $periodType == 1 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int)JHTML::date($maxExpDate, 'M'), (int)JHTML::date($maxExpDate, 'S'), (int)JHTML::date($maxExpDate, 'm'), (int)JHTML::date($maxExpDate, 'd') + $period, (int)JHTML::date($maxExpDate, 'Y')));
		} else if ( $periodType == 2 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int)JHTML::date($maxExpDate, 'M'), (int)JHTML::date($maxExpDate, 'S'), (int)JHTML::date($maxExpDate, 'm'), (int)JHTML::date($maxExpDate, 'd') + $period * 7, (int)JHTML::date($maxExpDate, 'Y')));
		} else if ( $periodType == 3 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int)JHTML::date($maxExpDate, 'M'), (int)JHTML::date($maxExpDate, 'S'), (int)JHTML::date($maxExpDate, 'm') + $period, (int)JHTML::date($maxExpDate, 'd'), (int)JHTML::date($maxExpDate, 'Y')));
		} else if ( $periodType == 4 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int)JHTML::date($maxExpDate, 'M'), (int)JHTML::date($maxExpDate, 'S'), (int)JHTML::date($maxExpDate, 'm'), (int)JHTML::date($maxExpDate, 'd'), (int)JHTML::date($maxExpDate, 'Y') + $period));
		} else if ( $periodType == 5 ) {
			$expired = '3009-12-31 23:59:59';
		}
		return $expired;
	}
}