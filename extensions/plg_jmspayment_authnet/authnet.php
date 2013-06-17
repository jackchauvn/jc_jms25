<?php

defined('_JEXEC') or die('Restricted access');

/*-------------------------------------------------------------------------------
# plg_jmspayment_authnet - JMS Membership Sites Plugin
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/
 
jimport( 'joomla.plugin.plugin' );

class plgJmsPaymentAuthnet extends JPlugin {
	
	
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		
		// Add style css		
		$document = JFactory::getDocument();
		JHTML::stylesheet( 'components/com_jms/assets/jms.css' );
		
		$document->addScriptDeclaration("
		
			//jQuery.noConflict();
			jQuery(document).ready(function($) {

				var pm = $('input[name=\"jform[payment_method]\"]');
				var def = $('input[name=\"jform[payment_method]\"]:checked', '#subscrform').val();
				
				$('#div_iwl_authnet input').prop('disabled', true);
				$('#div_iwl_authnet').hide();
				
				$(pm).change(function() {
					if ($(this).val() == 'iwl_authnet') {
						$('#div_iwl_authnet').slideDown(300);
						$('#div_iwl_authnet input').prop('disabled', false);
					} else {
						$('#div_iwl_authnet').slideUp(300);
						$('#div_iwl_authnet input').prop('disabled', true);
					}	
				});
				
			});
		");
		
		// Load the translation
		$this->loadLanguage();
		
	}
	
	public function onAfterRender() {
		
	}
	
	function onJmsProcessPayment ($row, $data, $plan, $config) {
		
		// check payment method
		if ($data['payment_method'] != 'iwl_authnet') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/authnet/authnet/iwl_authnet.php';
		
		$gateway =  new iwl_authnet($config);
		$data['x_description'] = JText::_('COM_JMS_JOOMLA_RESOURCE_SUBSCRIPTION') . ' - ' . $plan->name;
		$data['email'] = $user->get('email');
		$ret = $gateway->processPayment($data);
		
		if ($ret) {
			
			// if payment is successful update subscription
			$temp = JTable::getInstance('subscr', 'JmsTable');
   			$temp->load($row->id);
			$temp->transaction_id = $gateway->getTransactionID();
			
			// Get maximum of expired date
			$temp->created = date('Y-m-d H:i:s');
			$maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);
			$temp->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $maxExpDate);
			$temp->state = 1;
			$temp->store();
			
			return true;
			
		} else {
			
			return false;
			
		}

	}
	
	function onJmsProcessRecurringPayment ($row, $data, $plan, $config) {
		
		if ($data['payment_method'] != 'iwl_authnet') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/authnet/authnet/iwl_authnetrb.php';
		
		$gateway =  new iwl_AuthnetARB($config);
		switch ($plan->period_type) {
			// Day type
			case '1':
				$length = $plan->period;
				$unit = 'days';
				break;
			// Week type	
			case '2':
				$length = 7 * $plan->period;
				$unit = 'days';
				break;
			// Month type	
			case '3':
				$length = $plan->period;
				$unit = 'months';
				break;
			// Year type					
			case '4':
				$length = 12 * $plan->period;
				$unit = 'months';
				break;					
		}
		
		$gatewayData = array();
		$gatewayData['refID'] = $row->id;
		$gatewayData['subscrName'] = $user->get('name');
		$gatewayData['interval_length'] = $length;
		$gatewayData['interval_unit'] = $unit;
		$gatewayData['expirationDate'] = $data['x_exp_date'];
		$gatewayData['cardNumber'] = $data['x_card_num'];
		$gatewayData['firstName'] = $data['x_first_name'];
		$gatewayData['lastName'] = $data['x_last_name'];
		$gatewayData['address'] = $data['x_address1'] . ' ' .$data['x_address2'];
		$gatewayData['zip'] = $data['x_zip'];
		$gatewayData['amount'] = $row->price;
		$gatewayData['x_description'] = JText::_('COM_JMS_JOOMLA_RESOURCE_SUBSCRIPTION') . $plan->name;
		$gatewayData['email'] = $user->get('email');
		$gatewayData['plan_id'] = $row->plan_id;
		$ret = $gateway->processPayment($gatewayData);
		
		//return $ret;
		if ($ret) {
			
			// if payment is successful update subscription
			$temp = JTable::getInstance('subscr', 'JmsTable');
   			$temp->load($row->id);
			
			$temp->transaction_id = $gateway->getSubscriberID();
			$temp->created = date('Y-m-d H:i:s');
			
			// Get maximum of expired date
			$maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);
			$temp->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $maxExpDate);
			$temp->state = 1;
			$temp->payment_made = 1;
			$temp->store();
			
			return true;
			
		} else {
			
			return false;
			
		}
		
	}
	
	function onJmsProcessPaymentConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {
		
		// check payment method
		if ($paymentMethod != 'iwl_authnet') {
			return false;
		}

	}
	
	function onJmsProcessRecurringPaymentConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {
		
		// check payment method
		if ($paymentMethod != 'iwl_authnet') {
			return false;
		}

	}
	
	/**
	 * Private method to get maximum of expired date
	 *
	 * @param int $planId
	 * @param int $userId
	 * @return datetime
	 */
	protected function _getMaxExpDate($planId, $userId) {
		
		$db	= JFactory::getDbo();
		$query = 'SELECT MAX(expired)' .
			' FROM #__jms_plan_subscrs' .
			' WHERE plan_id = ' . $planId .
			' AND user_id = ' . $userId .
			' AND expired > NOW()' .
			' AND state = 1'
			;
		$db->setQuery($query);
		$maxExpDate = $db->loadResult();
		
		// If max expired date is not set, then assign it to current date
		if (!isset($maxExpDate)) {
			$maxExpDate = date('Y-m-d H:i:s');
		}
		
		return $maxExpDate;
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

// No closing tag
