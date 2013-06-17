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
class JFormFieldPaymentMethod extends JFormFieldList {
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'PaymentMethod';

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
		$payments = JPluginHelper::getPlugin('jmspayment');
		$language = JFactory::getLanguage();

		if (count($payments)) {
		
			foreach ($payments as $payment) {
				
				// load each plugin language file
				$language->load('plg_jmspayment_' . $payment->name  , JPATH_ADMINISTRATOR, 'en-GB', true);
				
				$options[] = JHTML::_('select.option', 'iwl_' . $payment->name, JText::_('IWL_' . strtoupper($payment->name)));
		
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
