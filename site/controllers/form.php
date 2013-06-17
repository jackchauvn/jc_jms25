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

require_once JPATH_COMPONENT.'/controller.php';

jimport('joomla.application.component.controller');

class JmsControllerForm extends JmsController {
	
	/**
	 * Method to register a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function register() {
            
		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('form', 'JmsModel');
		$uParams = JComponentHelper::getParams('com_users');
		$useractivation = $uParams->get('useractivation');
		$config = JComponentHelper::getParams('com_jms');
		
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if($uParams->get('allowUserRegistration') == 0) {
			if ($config->get('custom_registration_page')) {
				$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_jms&view=jms&Itemid=' . JRequest::getInt('Itemid') , false));
			}
			return false;
		}

		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
                
		$data = $model->validate($form, $requestData);
                
		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_jms.form.data', $requestData);

			// Redirect back to the registration screen.
			if ($config->get('custom_registration_page')) {
				$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_jms&view=jms&Itemid=' . JRequest::getInt('Itemid') , false));
			}
			return false;
		}
                
		// Attempt to save the data.
		$return	= $model->register($requestData);
                
		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_jms.form.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_JMS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			
			if ($config->get('custom_registration_page')) {
				$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false ));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_jms&view=jms&Itemid=' . JRequest::getInt('Itemid') , false));
			}
			
			return false;
		}
                
		// Flush the data from the session.
		$app->setUserState('com_jms.registration.data', null);
		
		if ($useractivation == 0) {
			$this->setMessage(JText::_('COM_JMS_REGISTRATION_SAVE_SUCCESS_LOGIN'));
		} else {
			$this->setMessage(JText::_('COM_JMS_REGISTRATION_SAVE_SUCCESS_ACTIVATE'));
		}

		if ($config->get('custom_registration_page')) {
			$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false ));
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_jms&view=jms&Itemid=' . JRequest::getInt('Itemid') , false));
		}

		return true;
	}
}