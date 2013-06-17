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
jimport('joomla.html.parameter');
jimport( 'joomla.form.form' );

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class JFormFieldPaymentSettings extends JFormField {
 
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'PaymentSettings';
	
	public function __construct($form = null){
        parent::__construct($form);
    }
	 
	function getInput() {
		
		$plugins = JPluginHelper::getPlugin('jmspayment');
		$component = JComponentHelper::getComponent('com_jms');
		$params = JComponentHelper::getParams('com_jms');
		$html = array();
		
		if(count($plugins)) {
		 
			foreach ($plugins as $key => $plugin) {
				
				$pluginpath = JPATH_PLUGINS . DS . 'jmspayment';
				$subpath = $pluginpath . DS . $plugin->name . DS . 'forms';		
				$files = JFolder::files($subpath, $plugin->name . '.xml', true, true);
				
				// load each plugin language file
				$language = JFactory::getLanguage();
				$language->load('plg_jmspayment_' . $plugin->name , JPATH_ADMINISTRATOR, 'en-GB', true);
				
				foreach ($files as $file) {
					
					$form = JForm::getInstance($plugin->name, $file, array('control' => 'jform'));
					$form->bind($params);	
					$fields = $form->getFieldset($plugin->name);
					
					foreach ($fields as $key => $field) {
                    	$html[] = '<li>';
                        $html[] = $field->getLabel();
                        $html[] = $field->getInput();
                    	$html[] = '</li>';
                	}				
				}
            }
			
        }

		return implode($html);
	
	}
}
