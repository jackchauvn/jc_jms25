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
JFormHelper::loadFieldClass('radio');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class JFormFieldPaymentMethod extends JFormFieldRadio {
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
	 
	protected function getInput() {
		
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_jms');
		$payment_method = $params->get('payment_method');
		$settings = $params->get('payment_settings');
		
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option) {

			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = ' class="inputbox required"';
			$payment_type = $option->value . '_type';
			$js = '';
			
			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			
			$html[] = '<div>';
			$html[] = '<input type="radio" id="' . $option->value . '_radio" name="' . $this->name . '"' . ' value="'. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"' . $checked . $class . $onclick . ' />';
			$html[] = ' ';
			$html[] = '<label for="' . $this->id . $i . '"' . $class . '>'.JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)).'</label>';
			$html[] = '</div>';

			if ($settings->$payment_type == 1) {
				
				// foldername = filename without extension
				// remove iwl_ from iwl_paymentname
				$foldername = substr($option->value, 4);
				$file = JPATH_PLUGINS . DS . 'jmspayment' . DS . $foldername . DS . 'forms' . DS . $foldername . '_cc_fields.xml';
				$form = JForm::getInstance($foldername . '_cc_fields', $file, array('control' => 'jform'));
				$fields = $form->getFieldset($foldername . '_cc_fields');

				$html[] = '<div id="div_' . $option->value . '" class="cc_fields_holder">';
				$html[] = '<table class="category" cellpadding="0" cellspacing="0" border="0" width="100%">';
				
				foreach ($fields as $key => $field) {
					
					$html[] = '<tr>';
					$html[] = '<td width="30%">';
					$html[] = $field->getLabel();
					$html[] = '</td>';
					$html[] = '<td>';
					$html[] = $field->getInput();
					$html[] = '</td>';
					$html[] = '</tr>';
					
                }
				
				$html[] = '</table>';
				$html[] = '</div>';
			}
			
		}

		return implode($html);
	}
	
	protected function getOptions() {
		
		// Initialize variables.
		$options = array();
		
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_jms');
		$payments = $params->get('payment_method');
		
		if (count($payments)) {
		
			foreach ($payments as $payment) {
		
				$options[] = JHTML::_('select.option', $payment, JText::_(strtoupper($payment)));
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
