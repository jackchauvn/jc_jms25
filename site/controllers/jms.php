<?php

/* -------------------------------------------------------------------------------
  # com_jms - JMS Membership Sites
  # -------------------------------------------------------------------------------
  # author    			Infoweblink
  # copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
  # @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: 			http://www.joomlamadesimple.com/
  # Technical Support:  	http://www.joomlamadesimple.com/forums
  --------------------------------------------------------------------------------- */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/controller.php';

jimport('joomla.application.controller');
jimport('joomla.utilities.utility');

class JmsControllerJms extends JmsController
{

    /**
     * Coupon Exsit
     *
     * @var boolean, true if coupon is exists, false if vice versa
     */
    var $_couponExists = true;

    /**
     * Coupon Valid
     *
     * @var boolean, true if coupon is valid, false if vice versa
     */
    var $_couponValid = true;

    /**
     * Coupon Error
     *
     * @var string
     */
    var $_couponError = '';

    /**
     * Method to process subscription
     *
     */
    public function process_subscription()
    {
        $mainframe = JFactory::getApplication();
        $model = & $this->getModel('jms');
        $post = JRequest::get('post');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $session = & JFactory::getSession();
        $config = JComponentHelper::getParams('com_jms');
        
        // Get the user data.
        $data = JRequest::getVar('jform', array(), 'post', 'array');
        
        // paymentsettings
        $paymentSettings = $config->get('payment_settings');
        
        // check if subscribed on signup
        if (JRequest::getVar('signup') == 'signup')
        {

            $sid = $session->get('sid', '', 'cc_fields');
            $coupon = $session->get('coupon', '', 'cc_fields');
            $paymentMethod = $session->get('payment_method', '', 'cc_fields');
            $return = $session->get('return', '', 'cc_fields');
            
            $data['sid'] = $sid;
            $data['coupon'] = $coupon;
            $data['payment_method'] = $paymentMethod;
            $data['return'] = $return;

            // check payment type
            $type = $paymentMethod . '_type';
            $payment_type = $paymentSettings->$type;

            if ($payment_type == 1)
            {

                $foldername = substr($paymentMethod, 4);
                $file = JPATH_PLUGINS . DS . 'jmspayment' . DS . $foldername . DS . 'forms' . DS . $foldername . '_cc_fields.xml';
                $form = JForm::getInstance($foldername . '_cc_fields', $file);
                $fields = $form->getFieldset($foldername . '_cc_fields');

                foreach ($fields as $field)
                {
                    $data[$field->fieldname] = $session->get($field->fieldname, '', 'cc_fields');
                }
            }
        }
        else
        {

            $sid = $data['sid'];
            $coupon = $data['coupon'];
            $paymentMethod = $data['payment_method'];
        }

        //exit(print_r($data));
        // If subscription id is null, user can't purchase it
        if (!$sid)
        {

            $msg = JText::_('COM_JMS_CAN_NOT_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        // If users are not login, they can't purchase subscription
        if (!$user->get('id'))
        {

            $msg = JText::_('COM_JMS_YOU_MUST_LOGIN_TO_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        // Get subscription information		
        $plan = $model->getPlan($sid);

        // As default, order amount is equal subscription price
        $data['price'] = $plan->price;

        // Re-calculate order amount based on discount
        if ($plan->discount > 0)
        {
            $data['price'] = round(($data['price'] - ($data['price'] * ($plan->discount / 100))), 2);
        }

        $data['plan_id'] = $sid;
        $data['item_name'] = $mainframe->getCfg('sitename') . ' :: ' . $plan->name;
        $data['order_quantity'] = 1;

        if ($coupon && ($data['price'] > 0))
        {
            $this->_validateCoupon($coupon, $sid);
            if (!$this->_couponExists)
            {
                JError::raiseNotice(100, JText::_('COM_JMS_COUPON_NOT_EXISTS'));
                parent::display();
                return;
            }
            else
            {
                if (!$this->_couponValid)
                {
                    JError::raiseNotice(100, JText::_('COM_JMS_COUPON_NOT_VALID'));
                    JError::raiseNotice(100, $this->_couponError);
                    parent::display();
                    return;
                }
                else
                {
                    $data['price'] = $this->_applyCoupon($coupon, $data['price']);
                }
            }
        }
        
        // Process Subscription
        $model->processSubscription($data);

        // check payment type
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            $this->display();
        }
    }

    public function buy_product()
    {
        $mainframe = JFactory::getApplication();
        $model = & $this->getModel('jms');
        $post = JRequest::get('post');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $session = & JFactory::getSession();
        $config = JComponentHelper::getParams('com_jms');

        // Get the user data.
        $data = JRequest::getVar('jform', array(), 'post', 'array');

        // paymentsettings
        $paymentSettings = $config->get('payment_settings');

        // check if subscribed on signup
        if (JRequest::getVar('signup') == 'signup')
        {
            $paymentMethod = $session->get('payment_method', '', 'cc_fields');

            $data['payment_method'] = $paymentMethod;

            // check payment type
            $type = $paymentMethod . '_type';
            $payment_type = $paymentSettings->$type;

            if ($payment_type == 1)
            {

                $foldername = substr($paymentMethod, 4);
                $file = JPATH_PLUGINS . DS . 'jmspayment' . DS . $foldername . DS . 'forms' . DS . $foldername . '_cc_fields.xml';
                $form = JForm::getInstance($foldername . '_cc_fields', $file);
                $fields = $form->getFieldset($foldername . '_cc_fields');

                foreach ($fields as $field)
                {
                    $data[$field->fieldname] = $session->get($field->fieldname, '', 'cc_fields');
                }
            }
        }
        else
        {
            $paymentMethod = $data['payment_method'];
        }

        // If users are not login, they can't purchase subscription
        if (!$user->get('id'))
        {

            $msg = JText::_('COM_JMS_YOU_MUST_LOGIN_TO_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        $product = $model->getProductById($post["product_id"]);

        $data['price'] = $product->price;

        $coupon = $post["coupon"];

        if ($coupon && ($data['price'] > 0))
        {
            $this->_validateCoupon($coupon, $sid);
            if (!$this->_couponExists)
            {
                JError::raiseNotice(100, JText::_('COM_JMS_COUPON_NOT_EXISTS'));
                parent::display();
                return;
            }
            else
            {
                $data['price'] = $this->_applyCoupon($coupon, $data['price']);
            }
        }

        if ($data['price'] == 0.00)
        {
            $payment = 'Paypal';
            $model->insertUserProduct($user->id, $product->id, $payment, CURRENT_TIMESTAMP);
            $this->setRedirect('index.php?option=com_jms&view=jms&layout=productdetails&id=' . $product->id);
            return;
        }

        $data['product_id'] = $product->id;
        $data['item_name'] = $mainframe->getCfg('sitename') . ' :: ' . $product->title;
        $data['order_quantity'] = 1;

        $model->buy_product($data);

        // check payment type
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            $this->display();
        }
    }

    public function buy_voucher()
    {
        $mainframe = JFactory::getApplication();
        $model = & $this->getModel('jms');
        $post = JRequest::get('post');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $session = & JFactory::getSession();
        $config = JComponentHelper::getParams('com_jms');

        // Get the user data.
        $data = JRequest::getVar('jform', array(), 'post', 'array');

        // paymentsettings
        $paymentSettings = $config->get('payment_settings');

        // check if subscribed on signup
        if (JRequest::getVar('signup') == 'signup')
        {
            $paymentMethod = $session->get('payment_method', '', 'cc_fields');

            $data['payment_method'] = $paymentMethod;

            // check payment type
            $type = $paymentMethod . '_type';
            $payment_type = $paymentSettings->$type;

            if ($payment_type == 1)
            {

                $foldername = substr($paymentMethod, 4);
                $file = JPATH_PLUGINS . DS . 'jmspayment' . DS . $foldername . DS . 'forms' . DS . $foldername . '_cc_fields.xml';
                $form = JForm::getInstance($foldername . '_cc_fields', $file);
                $fields = $form->getFieldset($foldername . '_cc_fields');

                foreach ($fields as $field)
                {
                    $data[$field->fieldname] = $session->get($field->fieldname, '', 'cc_fields');
                }
            }
        }
        else
        {
            $paymentMethod = $data['payment_method'];
        }

        // If users are not login, they can't purchase subscription
        if (!$user->get('id'))
        {

            $msg = JText::_('COM_JMS_YOU_MUST_LOGIN_TO_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        $voucher = $model->getVoucherById($post["voucher_id"]);

        $data['price'] = $voucher->price;

        $data['voucher_id'] = $voucher->id;
        $data['item_name'] = $mainframe->getCfg('sitename') . ' :: ' . $voucher->title;
        $data['order_quantity'] = 1;

        $model->buy_voucher($data);

        // check payment type
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            $this->display();
        }
    }

    /**
     * Method to process recurring subscription
     *
     */
    public function process_recurring_subscription()
    {

        $mainframe = JFactory::getApplication();
        $model = & $this->getModel('jms');
        $post = JRequest::get('post');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $session = JFactory::getSession();
        $config = JComponentHelper::getParams('com_jms');

        // Get the user data.
        $data = JRequest::getVar('jform', array(), 'post', 'array');

        // paymentsettings
        $paymentSettings = $config->get('payment_settings');

        // check if subscribed on signup
        if (JRequest::getVar('signup') == 'signup')
        {

            $sid = $session->get('sid', '', 'cc_fields');
            $coupon = $session->get('coupon', '', 'cc_fields');
            $paymentMethod = $session->get('payment_method', '', 'cc_fields');

            $data['sid'] = $sid;
            $data['coupon'] = $coupon;
            $data['payment_method'] = $paymentMethod;

            // check payment type
            $type = $paymentMethod . '_type';
            $payment_type = $paymentSettings->$type;

            if ($payment_type == 1)
            {

                $foldername = substr($paymentMethod, 4);
                $file = JPATH_PLUGINS . DS . 'jmspayment' . DS . $foldername . DS . 'forms' . DS . $foldername . '_cc_fields.xml';
                $form = JForm::getInstance($foldername . '_cc_fields', $file);
                $fields = $form->getFieldset($foldername . '_cc_fields');

                foreach ($fields as $field)
                {
                    $data[$field->fieldname] = $session->get($field->fieldname, '', 'cc_fields');
                }
            }
        }
        else
        {

            $sid = $data['sid'];
            $coupon = $data['coupon'];
            $paymentMethod = $data['payment_method'];
        }

        // If subscription id is null, user can't purchase it
        if (!$sid)
        {

            $msg = JText::_('COM_JMS_CAN_NOT_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        // If users are not login, they can't purchase subscription
        if (!$user->get('id'))
        {

            $msg = JText::_('COM_JMS_YOU_MUST_LOGIN_TO_PURCHASE');

            if ($config->get('custom_registration_page'))
            {
                $url = JRoute::_('index.php?option=com_content&view=article&id=' . $config->get('custom_registration_page') . '&Itemid=' . JRequest::getInt('Itemid'), false);
            }
            else
            {
                $url = JRoute::_('index.php?option=com_jms&view=form&Itemid=' . JRequest::getInt('Itemid'), false);
            }

            $mainframe->enqueueMessage($msg, 'Error');
            $this->setRedirect($url);
            return;
        }

        // Get subscription information		
        $plan = $model->getPlan($sid);

        // As default, order amount is equal subscription price
        $data['price'] = $plan->price;

        // Re-calculate order amount based on discount, if user have already purchase this subscription, then they will be discounted
        if ($plan->discount > 0)
        {
            $data['price'] = round(($data['price'] - ($data['price'] * ($plan->discount / 100))), 2);
        }
        $data['plan_id'] = $sid;
        $data['item_name'] = $mainframe->getCfg('sitename') . ' :: ' . $plan->name;
        $data['order_quantity'] = 1;

        if ($coupon && ($data['price'] > 0))
        {
            $this->_validateCoupon($coupon, $sid);
            if (!$this->_couponExists)
            {
                JError::raiseNotice(100, JText::_('COM_JMS_COUPON_NOT_EXISTS'));
                parent::display();
                return;
            }
            else
            {
                if (!$this->_couponValid)
                {
                    JError::raiseNotice(100, JText::_('COM_JMS_COUPON_NOT_VALID'));
                    JError::raiseNotice(100, $this->_couponError);
                    parent::display();
                    return;
                }
                else
                {
                    $data['price'] = $this->_applyCoupon($coupon, $data['price']);
                }
            }
        }

        // Process Subscription
        if ($data['price'] == 0.00)
        {
            $model->processSubscription($data);
        }
        else
        {
            $model->processRecurringSubscription($data);
        }

        // check payment type
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            $this->display();
        }
    }

    /**
     * Method to confirm subscription
     *
     */
    public function subscription_confirm()
    {
        $model = & $this->getModel('jms');
        $model->subscriptionConfirm();


        // $this->setRedirect('index.php?option=com_jms&task=jms.subscription_confirm&payment_method=iwl_paypal');
    }

    /**
     * Method to confirm recurring subscription
     *
     */
    public function recurring_subscription_confirm()
    {
        $model = & $this->getModel('jms');
        $model->recurringSubscriptionConfirm();
    }

    public function arb_silent_post_process()
    {
        $subscription_id = (int) $_POST['x_subscription_id'];
        if ($subscription_id)
        {
            // Get the response code. 1 is success, 2 is decline, 3 is error
            $response_code = (int) $_POST['x_response_code'];
            // Get the reason code. 8 is expired card.
            $reason_code = (int) $_POST['x_response_reason_code'];
            if ($response_code == 1)
            {
                // Success
                $db = JFactory::getDBO();
                // Get the corresponding subscription base on subscription id
                $query = 'SELECT * FROM #__jms_plan_subscrs WHERE transaction_id = "' . $subscription_id . '" AND subscription_type = "R"';
                $db->setQuery($query);
                $subscription = $db->loadObject();
                if (is_object($subscription))
                {
                    // Update subscription
                    $model = $this->getModel('jms');
                    $model->arbSilentPostProcess($subscription);
                }
            }
            else if ($response_code == 2)
            {
                // Declined
            }
            else if ($response_code == 3 && $reason_code == 8)
            {
                // An expired card
            }
            else
            {
                // Other error
            }
        }
    }

    /**
     * Method to cancel subscription
     *
     */
    public function cancel()
    {
        $post = $_REQUEST;
        $model = & $this->getModel('jms');
        $model->cancelSubscription($post);
        $this->setRedirect('index.php?option=com_jms&view=jms&layout=cancel&id=' . $post['id'] . '&plan_id=' . $post['plan_id']);
    }

    /**
     * Method to cancel subscription
     *
     */
    public function complete()
    {
        $post = $_REQUEST;
        $this->setRedirect('index.php?option=com_jms&view=jms&layout=complete&plan_id=' . $post['plan_id']);
    }

    /**
     * Private method to apply coupon
     *
     * @param string $coupon
     * @param float $price
     * @return new price after apply coupon
     */
    protected function _applyCoupon($coupon, $price)
    {

        $db = & JFactory::getDBO();
        $user = & JFactory::getUser();
        $sid = JRequest::getInt('sid');

        // Update used time for this coupon
        $sql = 'UPDATE #__jms_coupons' .
                ' SET used_time = used_time + 1' .
                ' WHERE code = "' . $coupon . '"'
        ;
        $db->setQuery($sql);
        $db->query();

        // Get coupon information
        $sql = 'SELECT *' .
                ' FROM #__jms_coupons' .
                ' WHERE code = "' . $coupon . '"'
        ;
        $db->setQuery($sql);
        $row = $db->loadObject();

        // Make coupon history for this current user
        $sql = 'INSERT INTO #__jms_coupon_subscrs' .
                ' VALUES ("", ' . $user->get('id') . ', ' . $row->id . ', ' . $sid . ', NOW(), ' . $price . ', ' . $row->discount . ', ' . $row->discount_type . ', ' . $row->recurring . ', ' . $row->num_recurring . ')'
        ;
        $db->setQuery($sql);
        $db->query();

        // Discount based on amount
        if ($row->discount_type == 2)
        {
            $new = round(($row->discount - $price), 2);
            if ($new < 0) $new = 0;
            $sql = 'UPDATE #__jms_coupons' .
                    ' SET discount = ' . $new .
                    ' WHERE code = "' . $coupon . '"'
            ;
            $db->setQuery($sql);
            $db->query();

            $out = $price - $row->discount;
            if ($out < 0) $out = 0;
            $out = round($out, 2);
        } else
        {
            $out = $price - ($price * ($row->discount / 100));
            $out = round($out, 2);
        }
        return $out;
    }

    /**
     * Private method to validate coupon
     *
     * @param string $coupon
     */
    protected function _validateCoupon($coupon, $sid)
    {
        // Get DB connector
        $db = & JFactory::getDBO();
        $sql = 'SELECT *, ' .
                ' IF(expired > NOW() OR YEAR (expired) = 0, 0, 1) AS expired_coupon' .
                ' FROM #__jms_coupons' .
                ' WHERE code = "' . $coupon . '"' .
                ' AND state = 1' .
                ' AND created <= NOW()'
        ;
        $db->setQuery($sql);
        $row = $db->loadObject();

        if (!isset($row))
        {
            $this->_couponExists = false;
            $this->_couponValid = false;
            $this->_couponError = JText::_('COM_JMS_COUPON_USED_OUT');
        }
        else
        {
            if ($row->code)
            {
                $this->_couponExists = true;
            }
            else
            {
                $this->_couponExists = false;
            }

            // Validate limit time
            if (($row->limit_time > 0) && ($row->used_time >= $row->limit_time))
            {
                $this->_couponValid = false;
                $this->_couponError = JText::_('COM_JMS_COUPON_USED_OUT');
                return;
            }

            // Validate gif certificate
            if (($row->discount_type == 2) && ($row->discount <= 0))
            {
                $this->_couponValid = false;
                $this->_couponError = JText::_('COM_JMS_COUPON_USED_OUT');
                return;
            }

            // Validate expired
            if ($row->expired_coupon)
            {
                $this->_couponValid = false;
                $this->_couponError = JText::_('COM_JMS_COUPON_EXPIRED');
                return;
            }

            // Validate bind to this user
            if ($row->user_ids)
            {
                $user = JFactory::getUser();
                $ids = $row->user_ids;
                // string to array
                $registry = new JRegistry;
                $registry->loadString($ids);
                $ids = $registry->toArray();
                if (!in_array($user->get('id'), $ids))
                {
                    $this->_couponValid = false;
                    $this->_couponError = JText::_('COM_JMS_COUPON_NOT_ALLOWED_TO_USE');
                    return;
                }
            }

            // Validate bind to this subscription
            if ($row->plan_ids)
            {
                $ids = $row->plan_ids;
                // string to array
                $registry = new JRegistry;
                $registry->loadString($ids);
                $ids = $registry->toArray();
                if (!in_array($sid, $ids))
                {
                    $this->_couponValid = false;
                    $this->_couponError = JText::_('COM_JMS_COUPON_NOT_VALID_FOR_PLAN');
                    return;
                }
            }

            // Validate limit time per user
            if ($row->limit_time_user > 0)
            {
                $user = &JFactory::getUser();
                $sql = 'SELECT COUNT(*)' .
                        ' FROM #__jms_coupon_subscrs' .
                        ' WHERE coupon_id = ' . (int) $row->id .
                        ' AND user_id = ' . (int) $user->get('id')
                ;
                $db->setQuery($sql);
                $num = $db->loadResult();
                if ($num >= $row->limit_time_user)
                {
                    $this->_couponValid = false;
                    $this->_couponError = JText::_('COM_JMS_EXCEED_COUPON_TIME');
                    return;
                }
            }
        }
    }

    public function download()
    {
        $model = $this->getModel();
        $id = JRequest::getInt('id');

        $download = $model->getDownloadsById($id);

        $registry = new JRegistry;
        $registry->loadString($download->link);
        $links = $registry->toArray();

        if ($links["local_link_radio"] == "1")
        {
            require_once(JPATH_COMPONENT_ADMINISTRATOR . "/helpers/class.chip_download.php");

            $args = array(
                'download_path' => "",
                'file' => $links["local_link"],
                'extension_check' => TRUE,
                'referrer_check' => FALSE,
                'referrer' => NULL,
            );

            $download = new chip_download($args);
            $download_hook = $download->get_download_hook();
            if ($download_hook['download'] == TRUE) $download->get_download();

            return true;
        }

        if ($links["external_link_radio"] == "1")
        {
            if ($links["download_username"] != "" && $links["download_password"] != "")
            {
                $requestUrl = $links["external_link"];
            }
            else $requestUrl = $links["external_link"];

            header("Location: $requestUrl");

            return true;
        }

        if ($links["amazon_s3_link_radio"] == "1")
        {
            if ($links["access_key"] != "" && $links["secret_key"] != "")
            {
                $url = str_replace("https://", "", $links["amazon_s3_link"]);
                $url = str_replace("http://", "", $url);
                $url = str_replace(".s3.amazonaws.com", "", $url);

                $url_parts = explode("/", $url);

                $accessKey = $links["access_key"];
                $secretAccessKey = $links["secret_key"];
                $bucketName = $url_parts[0];
                $bucketFile = $url_parts[count($url_parts) - 1];

                $file = rawurlencode($bucketFile);
                $file = str_replace('%2F', '/', $file);
                $path = $bucketName . '/' . $file;

                //generate our timeout timestamp
                $timeoutTmestamp = time() + 3600;

                //generate our authentication signature
                $stringToSign = "GET\n\n\n$timeoutTmestamp\n/$path";

                $sig = utf8_encode($stringToSign);
                $sig = hash_hmac('sha1', $sig, $secretAccessKey, true);
                $sig = base64_encode($sig);

                $signature = urlencode($sig);

                //construct our request URL
                $requestUrl = $links["amazon_s3_link"] . "?AWSAccessKeyId=" . $accessKey
                        . "&Expires=" . $timeoutTmestamp
                        . "&Signature=" . $signature;
            }
            else $requestUrl = $links["amazon_s3_link"];

            header("Location: $requestUrl");

            return true;
        }
    }

    public function order()
    {
        $model = $this->getModel();
        $user = JFactory::getUser();

        if ($_POST["jform"]["payment_method"])
        {
            $payment = 'Points';
            $points = ((int) $_POST['user_point'] - (int) $_POST['points']);
            $model->updateUserPoints($points, $user->id);
        }
        else
        {
            $payment = 'Paypal';
        }
        $model->insertUserProduct($user->id, $_POST['product_id'], $payment, CURRENT_TIMESTAMP);

        $this->setRedirect('index.php?option=com_jms&view=jms&layout=productdetails&id=' . $_POST['product_id']);
    }

    public function ordervoucher()
    {
        $model = $this->getModel();
        $user = JFactory::getUser();

        $points = ((int) $_POST['user_point'] - (int) $_POST['points']);
        $model->updateUserPoints($points, $user->id);

        $this->setRedirect('index.php?option=com_jms&view=jms&layout=complete&id=' . $_POST['voucher_id']);
    }

}