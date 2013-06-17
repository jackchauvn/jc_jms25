<?php

/*-------------------------------------------------------------------------------
# plg_jmspayment_paypal - JMS Membership Sites Plugin
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class plgJmsPaymentPaypal extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    function onJmsProcessPayment ($row, $data, $plan, $config) {
		
		// check payment method
		if ($data['payment_method'] != 'iwl_paypal') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/paypal/paypal/iwl_paypal.php';
		
		$gatewayData = array();
		$gatewayData['business']  = $payment_settings->paypal_id;
		$gatewayData['item_name'] = $plan->name;
		$gatewayData['amount'] = $row->price;
		$gatewayData['currency_code'] = $payment_settings->paypal_currency;
		$gatewayData['custom'] = $row->id;
		$gatewayData['return'] = $siteUrl.'index.php?option=com_jms&task=jms.complete&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['cancel_return'] = $siteUrl.'index.php?option=com_jms&task=jms.cancel&id='.$row->id.'&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['notify_url'] = $siteUrl.'index.php?option=com_jms&task=jms.subscription_confirm&payment_method=iwl_paypal';
//		&payment_method=iwl_paypal
		
		$gateway = new iwl_paypal($config);
		$gateway->setParams($gatewayData);
		$gateway->submitPost();

	}
	
	function onJmsProcessRecurringPayment ($row, $data, $plan, $config) {
		
		if ($data['payment_method'] != 'iwl_paypal') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/paypal/paypal/iwl_paypal.php';
		
		$gatewayData = array();		
		$gatewayData['business']  = $payment_settings->paypal_id;
		$gatewayData['item_name'] = $plan->name;
		$gatewayData['currency_code'] = $payment_settings->paypal_currency;
		$gatewayData['custom'] = $row->id;
		$gatewayData['return'] = $siteUrl.'index.php?option=com_jms&task=jms.complete&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['cancel_return'] = $siteUrl.'index.php?option=com_jms&task=jms.cancel&id='.$row->id.'&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['notify_url'] = $siteUrl.'index.php?option=com_jms&task=jms.subscription_confirm&payment_method=iwl_paypal';
//                &payment_method=iwl_paypal
		$gatewayData['cmd'] = '_xclick-subscriptions';
		$gatewayData['src'] = 1;
		$gatewayData['sra'] = 1;
		$gatewayData['a3'] = $row->price;
		
		switch ($plan->period_type) {
			// Day type
			case '1':
				$p3 = $plan->period;
				$t3 = 'D';
				break;
			// Week type	
			case '2':
				$p3 = $plan->period;
				$t3 = 'W';
				break;
			// Month type	
			case '3':
				$p3 = $plan->period;
				$t3 = 'M';
				break;
			// Year type		
			case '4':
				$p3 = $plan->period;
				$t3 = 'Y';
				break;
		}
		
		$gatewayData['p3'] = $p3;
		$gatewayData['t3'] = $t3;
		$gatewayData['lc'] = 'US';
		
		if ($row->r_times > 1) {
			$gatewayData['srt'] = $row->r_times;
		}		
				
		$gateway = new iwl_paypal($config);
		$gateway->setParams($gatewayData);
		$gateway->submitPost();
		
	}
	
	function onJmsSubscriptionConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {

		// check payment method
		if ($paymentMethod != 'iwl_paypal') {
			return false;
		}
		
		require_once JPATH_PLUGINS . '/jmspayment/paypal/paypal/iwl_paypal.php';
				
		$gateWay =  new iwl_paypal($config);
		$ret = $gateWay->processPayment($data, $plan->period, $plan->period_type, $maxExpDate);
		
		if ($ret) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function onJmsRecurringSubscriptionConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {
		
		// check payment method
		if ($paymentMethod != 'iwl_paypal') {
			return false;
		}
		
		require_once JPATH_PLUGINS . '/jmspayment/paypal/paypal/iwl_paypal.php';
				
		$gateWay =  new iwl_paypal($config);
		$gateWay->ipn_log = true;
		$gateWay->ipn_log_file = JPATH_COMPONENT.DS.'log.txt';
		$ret = $gateWay->processRecurringPayment($data, $plan->period, $plan->period_type, $plan->access_limit, $maxExpDate);
		
		if ($ret) {
			return true;
		} else {
			return false;
		}
		
	}

}

// No closing tag
