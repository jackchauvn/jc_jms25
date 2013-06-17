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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
jimport('joomla.plugin.helper');
jimport('joomla.application.component.helper');

/**
 * Model
 */
class JmsModelForm extends JModelForm
{
	/**
	 * @var		object	The user registration data.
	 * @since	1.6
	 */
	protected $data;

	/**
	 * Method to get the registration form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_jms');

			// Override the base user data with any data in the session.
			$temp = (array)$app->getUserState('com_jms.form.data', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

			// Get the groups the user should be added to after registration.
			$this->data->groups = isset($this->data->groups) ? array_unique($this->data->groups) : array();

			// Get the default new user group, Registered if not specified.
			$system	= $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_jms.form', $this->data));
			
			// Trigger the data preparation event.
			JPluginHelper::importPlugin('content');
			JPluginHelper::importPlugin('jmspayment');

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the registration form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jms.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return $this->getData();
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app	= JFactory::getApplication();
		$params	= $app->getParams('com_jms');

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function register($temp)
	{
		$config = JFactory::getConfig();
		$params = JComponentHelper::getParams('com_users');
		$app = JFactory::getApplication();
		$db	= $this->getDbo();
		$post = JRequest::get('post');
		$Itemid = JRequest::getInt('Itemid');
		$siteUrl = JURI::root();

		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array)$this->getData();
		
		// Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}
		
		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];
		$useractivation = $params->get('useractivation');

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			jimport('joomla.user.helper');
			$data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
			$token = $data['activation'];
			$password_clear = $data['password'];
		} else {
			$password_clear = $data['password'];
		}

		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}
		
		// Send registration confirmation mail
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		if (($useractivation == 1) || ($useractivation == 2)) {
			JmsModelForm::_sendMail($user, $password_clear, $token);
		} else {
			JmsModelForm::_sendMail($user, $password_clear, '');
		}
		
		$jmsparams = $app->getParams('com_jms');
		if ($jmsparams->get('mc_enable') == 1) {
			JmsModelForm::sendMailchimp($data['name'], $data['email'], $user->get('id'));
		}
                
		// Auto login users	if set
		if ($useractivation == 0) {
		
			if ($returnurl = JRequest::getVar('return', '', 'method', 'base64')) {
				$returnurl = base64_decode($returnurl);
				if (!JURI::isInternal($returnurl)) {
					$returnurl = '';
				}
			}
                        
                        $session = JFactory::getSession();
                        $session->set("return_url", $returnurl);
			
			$options = array();
			$options['remember'] = JRequest::getBool('remember', false);
			$options['return'] = $returnurl;
			
			$credentials = array();
			
			$credentials['username'] = $data['username'];
			$credentials['password'] = $data['password1'];
			
			$error = $app->login($credentials, $options);	
			
		}
		
		// one page registration
		$plan = $this->getPlan($data['sid']);
		$ccFields = JRequest::getVar('jform', array(), 'post', 'array');
                
		$session = JFactory::getSession();
		
		foreach ($ccFields as $key => $value) {
			$session->set($key, $value, 'cc_fields');
		}
                
		if ($plan->plan_type == 'R') {
			JController::setRedirect($siteUrl.'index.php?option=com_jms&task=jms.process_recurring_subscription&signup=signup&Itemid='.$Itemid);
			JController::redirect();
		} else {
			JController::setRedirect($siteUrl.'index.php?option=com_jms&task=jms.process_subscription&signup=signup&Itemid='.$Itemid);
			JController::redirect();
		}
				
	}
	
	protected function _sendMail($user, $password, $token) {
		
		$app  = JFactory::getApplication();
		$db	= JFactory::getDBO();
		$config = JFactory::getConfig();
		$uParams 	= JComponentHelper::getParams( 'com_users' );
		$useractivation = $uParams->get('useractivation');

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$sitename 		= $config->get('sitename');
		$mailfrom 		= $config->get('mailfrom');
		$fromname 		= $config->get('fromname');
		$siteURL		= JURI::base();

		if ($useractivation == 0) {
			
			$subject 	= JText::sprintf('COM_JMS_ACCOUNT_DETAILS_FOR', $name, $sitename);
			$subject 	= html_entity_decode($subject, ENT_QUOTES);
	
			$message =  JText::sprintf('COM_JMS_SEND_MSG_LOGIN', $name, $sitename, $siteURL, $username, $password);
			$message = html_entity_decode($message, ENT_QUOTES);
			
		} else {
			
			$activation = $siteURL.'index.php?option=com_users&task=registration.activate&token='.$token;
			
			$subject 	= JText::sprintf('COM_JMS_ACCOUNT_ACTIVATION_FOR', $name, $sitename);
			$subject 	= html_entity_decode($subject, ENT_QUOTES);
	
			$message =  JText::sprintf('COM_JMS_SEND_MSG_ACTIVATE', $name, $sitename, $activation, $siteURL, $username, $password);
			$message = html_entity_decode($message, ENT_QUOTES);
			
		}

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE sendMail = 1';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'COM_JMS_ACCOUNT_DETAILS_FOR' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		if (count($rows) > 0) {
			foreach ( $rows as $row )
			{
				if ($row->sendEmail)
				{
					$message2 = sprintf ( JText::_( 'COM_JMS_SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
					$message2 = html_entity_decode($message2, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
				}
			}
		}
		
	}
	
	/**
	 * Subscribe to Mailchimp
	**/
	protected function sendMailchimp($name, $email, $userid) {
		
		$app	= JFactory::getApplication();
		$config	= $app->getParams('com_jms');
		
		$name = explode(' ', "$name ");
		$fname = $name[0];
		$lname = $name[1];
		
		$merge_vars = array(
			'FNAME' => $fname,
			'LNAME' => $lname,
			'INTERESTS' => $config->get('mc_groupid')
			);
		
		require_once JPATH_COMPONENT.'/helpers/MCAPI.class.php';
				
		// Get the component config/params object.
		$params = JComponentHelper::getParams('com_jms');	
		$mc_api_key =  $params->get( 'mc_api' );
		
		$api = new MCAPI($mc_api_key);
		
		$api->listSubscribe($config->get('mc_listid'), $email, $merge_vars);
		
	}
	
	protected function getPlan($planId) {
		$query = 'SELECT *' .
			' FROM #__jms_plans' .
			' WHERE id = ' . (int) $planId
			;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
}