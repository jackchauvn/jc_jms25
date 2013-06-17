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
class JFormFieldJmsPlans extends JFormFieldRadio {
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'JmsPlans';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	 
	protected function getInput() {
		
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_jms');
		$cmd = JRequest::getCmd('option');
		
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';
		
		$html[] = '<input type="hidden" name="' . $this->form->getFormControl() . '[r_times]" id="r_times" value="" />';
   	 	$html[] = '<input type="hidden" name="' . $this->form->getFormControl() . '[subscription_type]" id="subscription_type" value="" />';
		$html[] = '<table class="category" cellpadding="0" cellspacing="0" border="0" width="100%">';
		$html[] = '<tr>';
		$html[] = '<th width="20">#</th>';
		$html[] = '<th>' . JText::_('COM_JMS_SUBSCRIPTION_NAME_HEAD') . '</th>';
		$html[] = '<th width="20%" align="center">' . JText::_('COM_JMS_PRICE_HEAD') . '</th>';
		$html[] = '</tr>';
		
		$plans = $this->getPlan();

		// Build the radio field output.
		foreach ($plans as $i => $plan) {

			// Initialize some option attributes.
			$class = ' class="inputbox required"';
			//$js = ' onChange="changePlanParameters()"';
			$js = '';

			// Initialize some JavaScript option attributes.
			$onclick = '';
				
			if (!$plan->hidden_plan) {
				
				$html[] = '<tr>';
				$html[] = '<td>';
				$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'. htmlspecialchars($plan->id, ENT_COMPAT, 'UTF-8').'"' . $class . $js . '/>';
				$html[] = '</td><td>';
				$html[] = '<strong><label for="' . $this->id . $i . '"' . $class . '>'.JText::alt($plan->name, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)).'</label></strong>';
				
				if ($plan->description) {
					$html[] = '<p>';
					$html[] = $plan->description;
					$html[] = '</p>';
				}
				
				$html[] = '</td><td>';
				
				if ($plan->discount > 0) {
					$discountedPrice = round(($plan->price - ($plan->price * ($plan->discount / 100))), 2);
					$html[] = JText::_('COM_JMS_SETUP_PRICE') . ' <strong><span>' . $params->get('currency_sign') . '</span>' . $plan->price . '</strong><br />';
					$html[] = JText::_('COM_JMS_DISCOUNT_PRICE') . ' <strong><span>' . $params->get('currency_sign') . '</span>' . $discountedPrice . '</strong>';
				} else {
					$html[] = '<strong><span>' . $params->get('currency_sign') . '</span>' . $plan->price . '</strong>';
				}
				
				$html[] = '<input type="hidden" name="r_times' . $i . '" id="r_times' . $i . '" value="' . $plan->number_of_installments . '" />';
				$html[] = '<input type="hidden" name="subscription_type' . $i . '" id="subscription_type' . $i . '" value="' . $plan->plan_type . '" />';
				
				$html[] = '</td>';
				$html[] = '</tr>';
								
			}
			
		}
		
		$html[] = '</table>';

		return implode($html);
	}
	
	protected function getPlan() {
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from('#__jms_plans');
		$query->where('state = 1');
		$query->order('name');

		// Get the options.
		$db->setQuery($query);
		$plans = $db->loadObjectList();
		
		return $plans;
		
	}

}
