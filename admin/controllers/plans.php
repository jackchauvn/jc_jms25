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

jimport('joomla.application.component.controlleradmin');

/**
 * Plans list controller class.
 */
class JmsControllerPlans extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'plan', $prefix = 'JmsModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}