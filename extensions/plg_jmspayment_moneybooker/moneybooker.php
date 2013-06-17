<?php

defined('_JEXEC') or die('Restricted access');

/*-------------------------------------------------------------------------------
# plg_jmspayment_moneybooker - JMS Membership Sites Plugin
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/
 
jimport( 'joomla.plugin.plugin' );

class plgJmsPaymentMoneybooker extends JPlugin {
	
	
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		
		// Add style css		
		$document = JFactory::getDocument();
		JHTML::stylesheet( 'components/com_jms/assets/jms.css' );
		
		$this->loadLanguage();
		
	}
	
	public function onAfterRender() {
		
	}
	
	function onJmsProcessPayment ($row, $data, $plan, $config) {
		
		// check payment method
		if ($data['payment_method'] != 'iwl_moneybooker') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/moneybooker/moneybooker/iwl_moneybooker.php';
		
		$gatewayData = array();	
		$gatewayData['pay_to_email'] = $payment_settings->mb_merchant_email;
		$gatewayData['transaction_id'] = $data['transaction_id'];
		$gatewayData['currency'] = $payment_settings->mb_currency;
		$gatewayData['amount'] = $row->price;
		$gatewayData['language'] = 'EN';
		$gatewayData['merchant_fields'] = 'id';
		$gatewayData['id'] = $row->id;
		$gatewayData['payment_method'] = 'iwl_moneybooker';
		$gatewayData['return_url'] = $siteUrl.'index.php?option=com_jms&task=jms.complete&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['cancel_url'] = $siteUrl.'index.php?option=com_jms&task=jms.cancel&id='.$row->id.'&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['status_url'] = $siteUrl.'index.php?option=com_jms&task=jms.subscription_confirm&payment_method=iwl_moneybooker';
		$gateway =  new iwl_moneybooker();
		$gateway->setParams($gatewayData);
		$gateway->submitPost();

	}
	
	function onJmsProcessRecurringPayment ($row, $data, $plan, $config) {
		
		if ($data['payment_method'] != 'iwl_moneybooker') {
			return false;
		}
		
		$user = &JFactory::getUser();
		$siteUrl = JURI::root();
		$Itemid = JRequest::getVar('Itemid');
		$payment_settings = $config->get('payment_settings');
		
		require_once JPATH_PLUGINS . '/jmspayment/moneybooker/moneybooker/iwl_moneybooker.php';
		
		$gatewayData = array();
		$gatewayData['pay_to_email'] = $payment_settings->mb_merchant_email;
		$gatewayData['transaction_id'] = $data['transaction_id'];
		$gatewayData['currency'] = $payment_settings->mb_currency;
		$gatewayData['amount'] = $row->price;
		$gatewayData['language'] = 'EN';
		$gatewayData['merchant_fields'] = 'id';
		$gatewayData['id'] = $row->id;
		$gatewayData['payment_method'] = 'iwl_moneybooker';
		$gatewayData['return_url'] = $siteUrl.'index.php?option=com_jms&task=jms.complete&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['cancel_url'] = $siteUrl.'index.php?option=com_jms&task=jms.cancel&id='.$row->id.'&plan_id='.$row->plan_id.'&Itemid='.$Itemid;
		$gatewayData['status_url'] = $siteUrl.'index.php?option=com_jms&task=jms.recurring_subscription_confirm&payment_method=iwl_moneybooker';
		$gateway =  new iwl_moneybooker();
		$gateway->setParams($gatewayData);
		$gateway->submitPost();
		
	}
	
	function onJmsProcessPaymentConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {
		
		// check payment method
		if ($paymentMethod != 'iwl_paypal') {
			return false;
		}
		
		require_once JPATH_PLUGINS . '/jmspayment/moneybooker/moneybooker/iwl_moneybooker.php';
				
		$gateWay = new iwl_moneybooker();
		$ret =  $gateWay->processPayment($data, $config, $plan->period, $plan->period_type, $maxExpDate);
		
		if ($ret) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function onJmsProcessRecurringPaymentConfirm ($data, $plan, $config, $maxExpDate, $paymentMethod) {
		
		// check payment method
		if ($paymentMethod != 'iwl_paypal') {
			return false;
		}
		
		require_once JPATH_PLUGINS . '/jmspayment/moneybooker/moneybooker/iwl_moneybooker.php';
				
		$gateWay = new iwl_moneybooker();
		$ret =  $gateWay->processPayment($config, $plan->period, $plan->period_type, $maxExpDate);
		
		if ($ret) {
			return true;
		} else {
			return false;
		}
		
	}

}

// No closing tag
