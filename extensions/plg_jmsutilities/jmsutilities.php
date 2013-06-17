<?php

/*-------------------------------------------------------------------------------
# plg_jmsutilities - JMS Membership Sites Plugin
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

JHtml::_('behavior.keepalive');
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.form.form' );
JHtml::_('behavior.formvalidation');

/**
 * Content Load Subscription Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentJmsutilities extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	public function __construct(& $subject, $config) {
		
		parent::__construct($subject, $config);
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		// Load the translation
		$language = JFactory::getLanguage();
		$this->loadLanguage();
		
		if ($mainframe->isAdmin()) {
			return true;
		}
		
		// load each plugin language file
		$paymentMethodArr = JPluginHelper::getPlugin('jmspayment');
		if (count($paymentMethodArr)){
			foreach($paymentMethodArr as $paymentMethod) {
				$language->load('plg_jmspayment_' . $paymentMethod->name  , JPATH_ADMINISTRATOR, 'en-GB', true);
			}
		}
		
		//$language->load('com_jms', JPATH_SITE, 'en-GB', true);
		
		// Add style css		
		$params = JComponentHelper::getParams('com_jms');
		JHTML::stylesheet( 'components/com_jms/assets/jms.css' );
		
		
		$document->addScriptDeclaration("
		
			if (typeof jQuery == 'undefined') { 
			
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = 'components/com_jms/assets/jquery.js';
				document.getElementsByTagName('head')[0].appendChild(script);

			}
			
		");
		
	}
	
	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0) {
		
		$canProceed = $context == 'com_content.article';
		
		if (!$canProceed) {
			return;
		}
		
		$user = JFactory::getUser();
		$userid = $user->get('id');
		$db	= JFactory::getDbo();
		$subscr = jmsGetSubscr('article', $userid, $db);
		$view = JRequest::getVar('view');

		// show loagin form
		$login = '/{LOGIN}/';
		if ((($subscr && $userid) || (!$subscr && $userid)) && ($view == 'article')) {
			$row->text = preg_replace($login, '', $row->text);
		} else {
			$loginform = $this->showLoginForm();
			$row->text = preg_replace($login, $loginform, $row->text);
		}
		
		// show registration form with all available plans
		$register = '/{REGISTER}/';
		if (!$subscr && $view == 'article') {
			$registerform = $this->showRegistrationForm();
			$row->text = preg_replace($register, $registerform, $row->text);
		} else {
			$row->text = preg_replace($register, '', $row->text);
		}
		
		// show registration form with selected plans
		if (preg_match("/{JMS-SUBSCRIPTION ID=([\d,]+)}/iU", $row->text, $matches)) {
			
			$matches = $matches[1];
			if ($matches == '') {
				$subscriptions = '';
			} else {
				$subscriptions = $this->showSelectedPlansForm($matches);
			}
			
			if (!$subscr && $view == 'article') {
				$row->text = preg_replace("/{JMS-SUBSCRIPTION ID=[^}]+}/iU", $subscriptions, $row->text);
			} else {
				$row->text = preg_replace("/{JMS-SUBSCRIPTION ID=[^}]+}/iU", '', $row->text);
			}
		}
		
		return;

	}
	
	protected function showRegistrationForm() {
						
		$user = JFactory::getUser();
		$userid = $user->get('id');
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_jms');
		$paymentMethodsArr = $params->get('payment_method');
		
		// trigger jmspayment plugins
		JPluginHelper::importPlugin('jmspayment');
		
		// load the registration form or subscription form or both
		JForm::addFormPath('components/com_jms/models/forms');
		$form = new JForm('com_jms.form', array('control' => 'jform'));
		if ($userid == 0) {
			$form->loadFile('form', false);
		} else {
			$form->loadFile('jms', false);
		}
		
		$html = '';
		//$html .= '';
		
		// show registration form if user is not logged in
		if ($userid == 0) {
			
			$html .= '<form  id="jms-register-form" action="' . JRoute::_('index.php') .'" method="post" class="form-validate jms-form">';
			$html .= $this->showRegistrationFields($form);
			
		} else {
			
			$document->addScriptDeclaration($this->showScript());
			$html .= '<form action="index.php" method="post" name="subscrform" id="jms-subscribe-form" class="form-validate jms-form">';
			
		}
		
		$html .= '<fieldset name="login" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_AVAILABLE_PLANS') . '</legend>';
		$html .= $form->getInput('sid');
		$html .= '</fieldset>';		
		
		$html .= '<fieldset name="coupon" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_COUPON') . '</legend>';
		$html .= $form->getLabel('coupon');
		$html .= '<br /><br />';
		$html .= $form->getInput('coupon');
		$html .= '</fieldset>';
		
		$html .= '<fieldset name="payment" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_PAYMENT_METHOD') . '</legend>';
		$html .= $form->getInput('payment_method');
		$html .= '</fieldset>';
		
		if ($params->get('terms_conditions')) {
			
			$document->addScriptDeclaration($this->popupScript());
			$terms_link = JRoute::_('index.php?option=com_content&view=article&id=' . $params->get('terms_conditions') . '&Itemid=' . JRequest::getInt('Itemid')) . '&tmpl=component';
			
			$html .= '<fieldset name="terms" class="jmsfieldset">';
			$html .= '<legend>' . JText::_('COM_JMS_TERMS') . '</legend>';
			$html .= '<input type="checkbox" name="accept_terms" id="accept_terms" class="inputbox" />';
			$html .= ' ' . JText::_('COM_JMS_AGREE_PRE_TEXT') . ' ';
			$html .= '<a href="#jms-box" class="jms-window">' . $params->get('terms_link') . '</a>';
			$html .= '<span class="label_error invalid"><br />' . JText::_('COM_JMS_AGREE_TERMS_ERROR') . ' ' . $params->get('terms_link') . '</span>';
			$html .= '</fieldset>';
			
			//popup
			$html .= '<div style="display: none; margin-top: -113px; margin-left: -130px;" id="jms-box" class="jms-popup">';
			$html .= '<a href="#" class="jms-close"><img src="components/com_jms/assets/images/close_pop.png" class="btn_close" title="Close Window" alt="Close"></a>';
			$html .= '<div id="external">';
			$html .= '<script type="text/javascript">';
			$html .= 'jQuery(document).ready(function($){$("#external").load("' . $terms_link . '");})';
			$html .= '</script>';
			$html .= '</div>';
			$html .= '</div>';
		
		}
		
		// switch task registration/subscription or both
		if ($userid == 0) {
					
			$html .= '<p>' . JText::_('COM_JMS_FIELDS_REQUIRED') . '</p>';
			$html .= '<button type="submit" class="validate">' . JText::_('COM_JMS_REGISTER_BUTTON') . '</button> ';
			
			$html .= JText::_('COM_JMS_OR');
			$html .= ' <a href="' . JRoute::_('') . '" title="' . JText::_('COM_JMS_CANCEL_BUTTON') . '"> ' . JText::_('COM_JMS_CANCEL_BUTTON') . '</a> ';
			$html .= '<input type="hidden" name="option" value="com_jms" />';
			$html .= '<input type="hidden" name="task" value="form.register" />';
			$html .= JHtml::_('form.token');
		
		} else {
			
			$html .= '<input type="submit" class="validate button" name="btnProcess" id="btnProcess" value="' . JText::_('COM_JMS_PROCESS_SUBSCRIPTION') . '" />';
			
			$html .= '<input type="hidden" name="option" value="com_jms" />';
            $html .= '<input type="hidden" name="Itemid" value="' . JRequest::getInt('Itemid') . '" />';
            $html .= '<input type="hidden" name="view" value="jms" />';
            $html .= '<input type="hidden" name="task" value="" />';
			
			if ($params->get('mc_enable')) {
				
                $html .= '<input type="hidden" name="mc_api_key" value="' . $params->get('mc_api_key') . '" />';
                $html .= '<input type="hidden" name="mc_listid" value="' . $params->get('mc_listid') . '" />';
                $html .= '<input type="hidden" name="mc_groupid" value="' . $params->get('mc_groupid') . '" />';
				
			}
		
		}
		
		$html .= '</form>';
				
		return $html;
		
	}
	
	protected function showSelectedPlansForm($matches) {
		
		$user = JFactory::getUser();
		$userid = $user->get('id');
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_jms');
		$paymentMethodsArr = $params->get('payment_method');
		$subscribeBtnImg = $this->params->get('subscribe_btn_img', 'components/com_jms/assets/images/subscribe.gif');
		
		// trigger jmspayment plugins
		JPluginHelper::importPlugin('jmspayment');
		
		JForm::addFormPath('components/com_jms/models/forms');
		$form = new JForm('com_jms.form', array('control' => 'jform'));
		
		if ($userid == 0) {
			$form->loadFile('form', false);
		} else {
			$form->loadFile('jms', false);
		}
		
		$html = '';
		// show registration form if user is not logged in
		if ($userid == 0) {
			
			$html .= '<form id="jms-register-form" action="' . JRoute::_('index.php') .'" method="post" class="form-validate jms-form">';
			$html .= $this->showRegistrationFields($form);
			
		} else {
			
			$document->addScriptDeclaration($this->showScript());
			$html .= '<form action="index.php" method="post" name="subscrform" id="jms-subscribe-form" class="form-validate jms-form">';
			
		}
		
		$plans = $this->getPlan($matches);
		
		$html .= '<fieldset name="login" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_AVAILABLE_PLANS') . '</legend>';
		
		if (count($plans)) {
			
			$html .= '<input type="hidden" name="' .  $form->getFormControl(). '[r_times]" id="r_times" value="" />';
			$html .= '<input type="hidden" name="' .  $form->getFormControl(). '[subscription_type]" id="subscription_type" value="" />';
			$html .= '<table class="category" cellpadding="0" cellspacing="0" border="0" width="100%">';
			$html .= '<tr>';
			$html .= '<th width="20">#</th>';
			$html .= '<th>' . JText::_('COM_JMS_SUBSCRIPTION_NAME_HEAD') . '</th>';
			$html .= '<th width="20%" align="center">' . JText::_('COM_JMS_PRICE_HEAD') . '</th>';
			$html .= '</tr>';
			
			// Build the radio field output.
			foreach ($plans as $i => $plan) {
				
				$html .= '<tr>';
				$html .= '<td>';
				$html .= '<input type="radio" id="jform_sid' . $i . '" name="' .  $form->getFormControl(). '[sid]" value="'. $plan->id . '" class="inputbox required" />';
				$html .= '</td><td>';
				$html .= '<strong><label for="jform_sid' . $i . '" aria-invalid="false" class="inputbox required">' . JText::_($plan->name) . '</label></strong>';
				if ($plan->description) {
					$html .= '<p>';
					$html .= $plan->description;
					$html .= '</p>';
				}
				$html .= '</td><td>';
				
				if ($plan->discount > 0) {
					$discountedPrice = round(($plan->price - ($plan->price * ($plan->discount / 100))), 2);
					$html .= JText::_('COM_JMS_SETUP_PRICE') . ' <strong><span>' . $params->get('currency_sign') . '</span>' . $plan->price . '</strong><br />';
					$html .= JText::_('COM_JMS_DISCOUNT_PRICE') . ' <strong><span>' . $params->get('currency_sign') . '</span>' . $discountedPrice . '</strong>';
				} else {
					$html .= '<strong><span>' . $params->get('currency_sign') . '</span>' . $plan->price . '</strong>';
				}
				
				$html .= '<input type="hidden" name="r_times' . $i . '" id="r_times' . $i . '" value="' . $plan->number_of_installments . '" />';
				$html .= '<input type="hidden" name="subscription_type' . $i . '" id="subscription_type' . $i . '" value="' . $plan->plan_type . '" />';
				
				$html .= '</td>';
				$html .= '</tr>';
				
			}
			
			$html .= '</table>';
			$html .= '</fieldset>';
			
		} else {
			
			$html .= '<p>' . JText::_("COM_JMS_LAYOUT_NO_PLANS"). '</p>';
			$html .= '</fieldset>';
			
		}
		
		$html .= '<fieldset name="coupon" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_COUPON') . '</legend>';
		$html .= $form->getLabel('coupon');
		$html .= '<br /><br />';
		$html .= $form->getInput('coupon');
		$html .= '</fieldset>';
		
		$html .= '<fieldset name="payment" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_PAYMENT_METHOD') . '</legend>';
		$html .= $form->getInput('payment_method');
		$html .= '</fieldset>';
		
		if ($params->get('terms_conditions')) {
			$terms_link = JRoute::_('index.php?option=com_content&view=article&id=' . $params->get('terms_conditions') . '&Itemid=' . JRequest::getInt('Itemid')) . '&tmpl=component';
			$html .= '<fieldset name="terms" class="jmsfieldset">';
			$html .= '<legend>' . JText::_('COM_JMS_TERMS') . '</legend>';
			$html .= '<input type="checkbox" name="accept_terms" id="accept_terms" class="inputbox" />';
			$html .= ' ' . JText::_('COM_JMS_AGREE_PRE_TEXT') . ' ';
			$html .= '<a href="#jms-box" class="jms-window">' . $params->get('terms_link') . '</a>';
			$html .= '<span class="label_error invalid"><br />' . JText::_('COM_JMS_AGREE_TERMS_ERROR') . ' ' . $params->get('terms_link') . '</span>';
			$html .= '</fieldset>';
			
			//popup
			$html .= '<div style="display: none; margin-top: -113px; margin-left: -130px;" id="jms-box" class="jms-popup">';
			$html .= '<a href="#" class="jms-close"><img src="components/com_jms/assets/images/close_pop.png" class="btn_close" title="Close Window" alt="Close"></a>';
			$html .= '<div id="external">';
			$html .= '<script type="text/javascript">';
			$html .= 'jQuery(document).ready(function($){$("#external").load("' . $terms_link . '");})';
			$html .= '</script>';
			$html .= '</div>';
			$html .= '</div>';
		}
		
		// switch task registration/subscription or both
		if ($userid == 0) {
					
			$html .= '<p>' . JText::_('COM_JMS_FIELDS_REQUIRED') . '</p>';
			$html .= '<input type="image" src="' . JURI::base() . $subscribeBtnImg . '" class="validate" name="btnProcess" id="btnProcess" value="' . JText::_('COM_JMS_PROCESS_SUBSCRIPTION') . '" style="border: 0;" />';
			
			$html .= '<input type="hidden" name="option" value="com_jms" />';
			$html .= '<input type="hidden" name="task" value="form.register" />';
			$html .= JHtml::_('form.token');
		
		} else {
			
			$html .= '<input type="image" src="' . JURI::base() . $subscribeBtnImg . '" class="validate" name="btnProcess" id="btnProcess" value="' . JText::_('COM_JMS_PROCESS_SUBSCRIPTION') . '" style="border: 0;" />';
			
			$html .= '<input type="hidden" name="option" value="com_jms" />';
            $html .= '<input type="hidden" name="Itemid" value="' . JRequest::getInt('Itemid') . '" />';
            $html .= '<input type="hidden" name="view" value="jms" />';
            $html .= '<input type="hidden" name="task" value="" />';
			
			if ($params->get('mc_enable')) {
				
                $html .= '<input type="hidden" name="mc_api_key" value="' . $params->get('mc_api_key') . '" />';
                $html .= '<input type="hidden" name="mc_listid" value="' . $params->get('mc_listid') . '" />';
                $html .= '<input type="hidden" name="mc_groupid" value="' . $params->get('mc_groupid') . '" />';
				
			}
		
		}
		$html .= '</form>';
		
		return $html;
		
	}
	
	protected function showLoginForm() {
		
		$uri = JFactory::getURI();
		$url = $uri->toString(array('path', 'query', 'fragment'));
		$session = JFactory::getSession();
		
		if ($session->get('jms_access_url')) {
			$url = $session->get('jms_access_url');
		}
		
		$url = base64_encode($url);
		
		$html = '';
		$html .= '<form action="' . JRoute::_('index.php?option=com_jms') . '" method="post" id="jms-login-form" class="form-validate jms-form">';
		$html .= '<fieldset name="login" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_LOGIN') . '</legend>';
		$html .= '<table class="jms_login" cellpadding="0" cellspacing="0" border="0" width="100%">';
		$html .= '<tr>';
		$html .= '<td class="jms_td_label" width="120px">' . JText::_('Username') . '</td>';
		$html .= '<td><input type="text" name="username" class="inputbox required" size="30" /></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td class="jms_td_label">' . JText::_('Password') . '</td>';
		$html .= '<td><input type="password" name="password" class="inputbox required" size="30" /></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>&nbsp;</td>';
		$html .= '<td><input type="submit" name="Submit" class="button" value="' . JText::_('JLOGIN') . '" /></td>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '<input type="hidden" name="option" value="com_users" />';
        $html .= '<input type="hidden" name="task" value="user.login" />';
        $html .= '<input type="hidden" name="return" value="' . $url . '" />';
		$html .= JHtml::_('form.token');
		$html .= '</fieldset>';
		$html .= '</form>';
		
		return $html;
		
	}
	
	protected function showRegistrationFields($form) {
			
		$html = '';
		$html .= '<fieldset name="register" class="jmsfieldset">';
		$html .= '<legend>' . JText::_('COM_JMS_REGISTRATION') . '</legend>';		
		$html .= '<dl>';
		
		$html .= '<dt>';
		$html .= $form->getLabel('name');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('name');
		$html .= '</dd>';
		
		$html .= '<dt>';
		$html .= $form->getLabel('username');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('username');
		$html .= '</dd>';
		
		$html .= '<dt>';
		$html .= $form->getLabel('email1');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('email1');
		$html .= '</dd>';
		
		$html .= '<dt>';
		$html .= $form->getLabel('email2');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('email2');
		$html .= '</dd>';
		
		$html .= '<dt>';
		$html .= $form->getLabel('password1');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('password1');
		$html .= '</dd>';;
		
		$html .= '<dt>';
		$html .= $form->getLabel('password2');
		$html .= '</dt>';
		$html .= '<dd>';
		$html .= $form->getInput('password2');
		$html .= '</dd>';
		
		$html .= '</dl>';
		$html .= '<div class="clr"></div>';
		$html .= '</fieldset>';
		
		return $html;
			
	}
	
	protected function showScript() {
		$script = "
			//jQuery.noConflict();
			jQuery(document).ready(function($) {

				var sid = $('input[name=\"jform[sid]\"]');
				
				//var checked = $('input[name=\"jform[sid]\"]:checked', '#subscrform');
				//var id = $(sid).attr('id').replace(/jform_sid/, '');
				
				$(sid).change(function() {
					
					var id = $(this).attr('id').replace(/jform_sid/, '');
					var r_times = $('#r_times' + id).val();
					var subscription_type = $('#subscription_type' + id).val();
					
					$('input[name=\"jform[r_times]\"]').val(r_times);
					$('input[name=\"jform[subscription_type]\"]').val(subscription_type);
					
					//alert(test);
				
					if (subscription_type == 'R') {
						$('input[name=\"task\"]').val('jms.process_recurring_subscription');
					} else {
						$('input[name=\"task\"]').val('jms.process_subscription');
					}
					
				});
				
			});
			";
		return $script;
	}
	
	protected function popupScript() {
		$script = "
			//jQuery.noConflict();
			jQuery(document).ready(function($) {
				// popup
				$('a.jms-window').click(function() {
					
					// Getting the variable's value from a link 
					var jmspopupBox = $(this).attr('href');
			
					//Fade in the Popup and add close button
					$(jmspopupBox).fadeIn(300);
					
					//Set the center alignment padding + border
					var popMargTop = ($(jmspopupBox).height() + 24) / 2; 
					var popMargLeft = ($(jmspopupBox).width() + 24) / 2; 
					
					$(jmspopupBox).css({ 
						'margin-top' : -popMargTop,
						'margin-left' : -popMargLeft
					});
					
					// Add the mask to body
					$('body').append('<div id=\"jms-mask\"></div>');
					$('#jms-mask').fadeIn(300);
					
					return false;
				});
				
				// When clicking on the button close or the mask layer the popup closed
				$('a.jms-close, #jms-mask').live('click', function() { 
					$('#jms-mask , .jms-popup').fadeOut(300 , function() {
						$('#jms-mask').remove();  
					}); 
				
				return false;
				
				});
				
				// accept terms
				$('input#accept_terms').addClass('unchecked');
				$('.label_error').hide();
				
				$('input#accept_terms').click(function() {
					
					var checked_status = this.checked;
					
					if (checked_status == true) {
						$(this).removeClass('unchecked');
						$(this).toggleClass('checked');
						$('.label_error').hide();
					} else {
						$(this).removeClass('checked');
						$(this).toggleClass('unchecked');
						$('.label_error').show();
					}
				});
				
				$('.validate').click(function() {
					
					if ($('input#accept_terms').hasClass('unchecked')) {
						$('.label_error').show();
						return false;
					} else {
						$('.label_error').hide();
					}
					
					return true;
				
				});
				
			});
		";
		
		return $script;
	}

	/**
	 * Method to get a specific subscription type for a specific user 
	 * @param string $type
	 * @param int $userid
	 * @param database object $db
	 * @param string $ids
	 * @return object
	 */
	function jmsGetSubscr($type, $userid, $db, $ids = '')
	{
		$db->setQuery(
			'SELECT u.plan_id, s.params, u.id, s.`categories`, s.`articles`, u.access_count, u.access_limit, s.limit_time_type,' .
			' IF(u.access_limit > 0, IF(u.access_count >= u.access_limit, 0, 1), 1) AS cl'.
			' FROM #__jms_plan_subscrs AS u' .
			' LEFT JOIN #__jms_plans AS s ON s.id = u.plan_id  ' .
			' WHERE u.user_id = ' . (int)$userid .
			' AND u.expired > NOW()' .
			' AND u.created < NOW()' .
			' AND u.state = 1' .
			' AND s.`' . $type . '_type` = 1'
			. ((int)$ids ? ' AND s.id IN (' . (int)$ids . ') ' : null) .
			' GROUP BY u.plan_id'.
			' HAVING cl > 0'.
			' ORDER BY u.created DESC');
			
		return $db->loadObjectList();
	}
	
	protected function getPlan($matches) {
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from('#__jms_plans');
		$query->where('state = 1');
		$query->where('id IN(' . trim($matches) . ')');
		$query->order('name');

		// Get the options.
		$db->setQuery($query);
		$plans = $db->loadObjectList();
		
		return $plans;
	}
}
