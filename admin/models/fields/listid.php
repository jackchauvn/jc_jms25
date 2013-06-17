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

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class JFormFieldListid extends JFormFieldList {
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Listid';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions() {
		// Initialize variables.
		$options = array();
		
		$app = JFactory::getApplication();
		require_once JPATH_SITE.'/components/com_jms/helpers/MCAPI.class.php';
		require_once JPATH_SITE.'/components/com_jms/helpers/MCauth.php';
			
		$MCauth = new MCauth();
		
		$params = JComponentHelper::getParams('com_jms');	
		$api_key =  $params->get( 'mc_api' );
		
		$api = new MCAPI($api_key);
								
		$retval = $api->lists();
					
		if (!$api->errorMessage) {
													
			if (is_array($retval['data'])) {
				foreach ($retval['data'] as $list) {
					$options[] = JHTML::_('select.option', $list['id'], JText::_($list['name']));
				}
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
