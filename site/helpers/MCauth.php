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

class MCauth {
	
    function MCauth(){
	if(!isset($_SESSION['MCping'])){

		$params = JComponentHelper::getParams('com_jms');	
		$MCapi =  $params->get( 'mc_api' );
		
	    $MC = new MCAPI($MCapi);
	    $ping = $MC->ping();
	    $_SESSION['MCping'] = $ping;
		
	} else {
		
	    $ping = $_SESSION['MCping'];
		
	}

	return $ping;
    }
}
