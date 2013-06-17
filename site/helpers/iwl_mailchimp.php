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

class iwl_mailchimp {
	
	/**
	 * Constructor functions, init some parameter
	 * @param object $config
	 */
	function __construct() {
		// Do nothing	
	}
	
	function autoresponder($plan, $user) {
		
		$plan_mc_listid = $plan->plan_mc_listid;
		$plan_mc_groupid = $plan->plan_mc_groupid;
		$name = $user->get('name');
		$email = $user->get('email');
		
		$explodename = explode(' ', "$name ");
		$fname = $explodename[0];
		$lname = $explodename[1];
				
		$merge_vars = array('FNAME'=>$fname,'LNAME'=>$lname, 'INTERESTS'=>$plan_mc_groupid);
				
		require_once JPATH_COMPONENT.'/helpers/MCAPI.class.php';
						
		// Get the component config/params object.
		$params = JComponentHelper::getParams('com_jms');	
		$api_key =  $params->get( 'mc_api' );
				
		$api = new MCAPI($api_key);

		$api->listSubscribe($plan_mc_listid, $email, $merge_vars);
		
	}
}
?>