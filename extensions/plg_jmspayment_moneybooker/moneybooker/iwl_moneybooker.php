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

class iwl_moneybooker {
	/**
	 * Url to submit post to
	 *
	 * @var string
	 */
	var $_url = null;
	
	/**
	 * Containing all parameters will be submitted to moneybooker server
	 * 
	 * @var array
	 */		
	var $_params = array();
	
	function __construct() {
		$this->_url = 'https://www.moneybookers.com/app/payment.pl';
	}
	
	/**
	 * Set the parameter 
	 *
	 * @param string $name
	 * @param string $value
	 */
	function _setParam($name, $val) {
		$this->_params[$name] = $val;
	}

	/**
	 * Setup an array of parameter
	 *
	 * @param array $params
	 */
	function setParams($params) {
		foreach ($params as $key=>$val) {
			$this->_params[$key] = $val;
		}
	}
	
	/**
	 * Submit post to moneybooker server
	 *
	 */
	function submitPost() {
	?>
		<div class="contentheading"><?php echo  JText::_('COM_JMS_WAIT_MONEYBOOKER'); ?></div>
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
				setTimeout('redirect()',5000);
			</script>
		</form>
	<?php
	}

	/**
	 * Validate the data submited from moneybooker server to our server
	 *
	 * @param array $data
	 * @param object $config
	 */
	function _validate($data, $config) {
		
		$payment_settings = $config->get('payment_settings');
		$val =  $data['mb_merchant_id'].
				$data['transaction_id'].
				strtoupper(md5($payment_settings->mb_secret_word)).
				$data['mb_amount'].
				$data['mb_currency'].
				$data['status']
				;
		$val = strtoupper(md5($val));
		if ($val != $data['md5sig'])
			return false;
		else
			return true;
	}

	/**
	 * Confirm payment process
	 * @return boolean : true if success, otherwise return false
	 */
	function processPayment($data, $config, $period, $periodType, $maxExpDate) {
		$ret = $this->_validate($data, $config);
		$id = $data['id'];
		if ($ret) {
			$row =  JTable::getInstance('subscr', 'Table');			
			$row->load($id);
   			$row->transaction_id = $data['mb_transaction_id'];
   			if (isset($maxExpDate)) {
				$row->created = $maxExpDate;
			} else {
				$row->created = date('Y-m-d H:i:s');
			}
			$row->expired = $this->_getExpiredDate($period, $periodType, $row->created);
   			$row->state = 1;
			$row->payment_made = $row->payment_made + 1;
			if ($row->payment_made > 1) {
   				$row->price = $row->price + $data['amount'];
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
	 * @param datetime $created
	 * @return datetime
	 */
	function _getExpiredDate($period, $periodType, $created) {
		if ( $periodType == 1 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($created, '%H') + $period, JHTML::date($created, '%M'), JHTML::date($created, '%S'), JHTML::date($created, '%m'), JHTML::date($created, '%d'), JHTML::date($created, '%Y')));
		} else if ( $periodType == 2 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($created, '%H'), JHTML::date($created, '%M'), JHTML::date($created, '%S'), JHTML::date($created, '%m'), JHTML::date($created, '%d') + $period, JHTML::date($created, '%Y')));
		} else if ( $periodType == 3 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($created, '%H'), JHTML::date($created, '%M'), JHTML::date($created, '%S'), JHTML::date($created, '%m'), JHTML::date($created, '%d') + $period * 7, JHTML::date($created, '%Y')));
		} else if ( $periodType == 4 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($created, '%H'), JHTML::date($created, '%M'), JHTML::date($created, '%S'), JHTML::date($created, '%m') + $period, JHTML::date($created, '%d'), JHTML::date($created, '%Y')));
		} else if ( $periodType == 5 ) {
			$expired = date('Y-m-d H:i:s', mktime(JHTML::date($created, '%H'), JHTML::date($created, '%M'), JHTML::date($created, '%S'), JHTML::date($created, '%m'), JHTML::date($created, '%d'), JHTML::date($created, '%Y') + $period));
		} else if ( $periodType == 5 ) {
			$expired = '3009-12-31 23:59:59';
		}
		return $expired;
	}
}

