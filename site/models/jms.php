<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');
jimport('joomla.application.component.helper');
jimport('joomla.event.dispatcher');
jimport('joomla.plugin.helper');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class JmsModelJms extends JModelForm
{

    /**

     * Plan id

     *

     * @var int

     */
    var $_id = null;

    /**

     * Plan data

     *

     * @var array

     */
    var $_plan = null;

    /**

     * Plan data array

     *

     * @var array

     */
    var $_data = null;

    /**

     * subscriptions for current user

     *

     * @var array

     */
    var $_subscriptions = null;

    /**

     * Constructor

     * @since 1.5

     */
    public function __construct()
    {
        parent::__construct();
        $id = JRequest::getVar('id', '', 'default', 'int');
        $this->setId($id);
    }

    /**

     * Method to set the plan identifier

     *

     * @access	public

     * @param	int plan identifier

     */
    public function setId($id)
    {
        // Set plan id and wipe data
        $this->_id = $id;
        $this->_plan = null;
        $this->_data = null;
        $this->_subscriptions = null;
    }

    /**

     *

     * @param	array	$data		An optional array of data for the form to interogate.

     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.

     * @return	JForm	A JForm object on success, false on failure

     * @since	1.6

     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jms.jms', 'jms', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }

    /**

     * Method to get plan data

     *

     * @return plan object list

     */
    public function getData()
    {
        $sql = 'SELECT * FROM #__jms_plans' .
                ' WHERE state = 1 ORDER BY ordering'
        ;
        $this->_db->setQuery($sql);

        $this->_data = $this->_db->loadObjectList();

        // Trigger the data preparation event.
        JPluginHelper::importPlugin('jmspayment');

        return $this->_data;
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
        $app = JFactory::getApplication();
        $params = $app->getParams('com_jms');

        // Load the parameters.
        $this->setState('params', $params);
    }

    /**

     * Method to get subscriptions data for current user

     *

     * @return subscriptions object list

     */
    public function getSubscriptions()
    {
        // Get user
        $user = JFactory::getUser();

        $sql = 'SELECT a.*, p.name AS pname, DATEDIFF(a.expired, NOW()) AS days_left' .
                ' FROM #__jms_plan_subscrs AS a' .
                ' INNER JOIN #__jms_plans AS p ON (a.plan_id  = p.id)' .
                ' WHERE a.user_id = ' . (int) $user->get('id');

        $this->_db->setQuery($sql);

        $this->_subscriptions = $this->_db->loadObjectList();

        return $this->_subscriptions;
    }

    /**

     * Public method to process subscription

     *

     * @param array $data

     */
    public function processSubscription($data)
    {
        // Import help library
        jimport('joomla.user.helper');

        $siteUrl = JURI::root();

        // Get configuration
        $config = $this->getConfig();

        // Get user
        $user = &JFactory::getUser();

        $data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
        $row = JTable::getInstance('subscr', 'JmsTable');

        $row->bind($data);

        // Get plan information
        $plan = $this->getPlan($row->plan_id);

        $row->user_id = $user->get('id');

        // offset
        $format = 'Y-m-d H:i:s';
        $date = date($format);

        $row->created = date($format, strtotime('-1 day' . $date));

        $couponCode = $data['coupon'];
        $coupon = $this->getCoupon($couponCode);

        // Get maximum of expired date
        $maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);

        if (!empty($couponCode))
        {

            if ($coupon->recurring == 1)
            {

                $row->expired = $this->_getExpiredDate($coupon->num_recurring, $plan->period_type, $maxExpDate);
            }
            else
            {

                if ($plan->plan_type == 'R')
                {

                    $row->expired = $this->_getExpiredDate($plan->number_of_installments, $plan->period_type, $maxExpDate);
                }
                else
                {

                    $row->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $maxExpDate);
                }
            }
        }
        else
        {

            $row->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $maxExpDate);
        }

        $row->number = 0;
        $row->access_count = 0;
        $row->access_limit = $plan->limit_time;
        $paymentMethod = $data['payment_method'];
        $row->payment_method = JText::_(strtoupper($paymentMethod));
        $row->parent = 0;
        $row->state = 0;

        // Get coupon recurring information
        if (!empty($couponCode) && ($coupon->recurring == 1))
        {

            $recurring = true;

            $num_recurring = $coupon->num_recurring;
        }
        else
        {

            $recurring = false;
        }

        if ($recurring)
        {

            $row->subscription_type = 'R';

            $row->r_times = $num_recurring;
        }
        else
        {

            $row->subscription_type = 'I';

            $row->r_times = 0;
        }

        $row->payment_made = 0;
        $row->subscr_id = '';
        if ($row->price == 0.00)
        {

            $row->transaction_id = time();

            $row->state = 1;
        }
        
        $user_id = $row->user_id;
        $plan_id = $row->plan_id;
        
        $query = "DELETE FROM #__jms_plan_subscrs WHERE user_id = '$user_id' AND plan_id = '$plan_id'";
        $this->_db->setQuery($query);
        $this->_db->query();

        $row->store();

        $query = "SELECT grant_points FROM #__jms_plans WHERE id = $plan_id";
        $this->_db->setQuery($query);
        $grant_points = $this->_db->loadResult();

        $query = "DELETE FROM #__jms_user_points WHERE user_id = $user_id";
        $this->_db->setQuery($query);
        $this->_db->query();

        $query = "INSERT INTO #__jms_user_points(user_id, points) VALUES($user_id, $grant_points)";
        $this->_db->setQuery($query);
        $this->_db->query();

        if ($row->price == 0.00)
        {
            // Send notification email
            $this->_sendEmails($row, $config);

            // Add user to autoresponder
            if ($plan->autores_enable || $plan->crm_enable || $plan->plan_mc_enable)
            {
                if ($plan->autores_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_aweber.php';
                    $gateWay = new iwl_aweber();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->crm_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_crm.php';
                    $gateWay = new iwl_crm();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->plan_mc_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
                    $gateWay = new iwl_mailchimp();
                    $gateWay->autoresponder($plan, $user);

                    $data['return'] = base64_decode($data['return']);
                    $app = JFactory::getApplication();
                    $app->setUserState('users.login.form.return', $data['return']);
                    $app->redirect(JRoute::_($app->getUserState('users.login.form.return'), false));
                    return;
                }
                
                if ($plan->ihub_crm)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_ihub_crm.php';
                    $gateWay = new iwl_ihub_crm();
                    $gateWay->autoresponder($plan, $user);
                }
            }
            else
            {
                $data['return'] = base64_decode($data['return']);
                $app = JFactory::getApplication();
                $app->setUserState('users.login.form.return', $data['return']);
                $app->redirect(JRoute::_($app->getUserState('users.login.form.return'), false));
                return;
            }
        }
        else
        {
            // Trigger JMS Plugins
            JPluginHelper::importPlugin('jmspayment');
            $dispatcher = JDispatcher::getInstance();
            $ret = $dispatcher->trigger('onJmsProcessPayment', array($row, $data, $plan, $config));

            $paymentSettings = $config->get('payment_settings');
            $type = $paymentMethod . '_type';
            $payment_type = $paymentSettings->$type;

            if ((in_array(1, $ret)) && ($payment_type == 1))
            {
                $this->_sendEmails($row, $config);

                // Add user to autoresponder
                if ($plan->autores_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_aweber.php';
                    $gateWay = new iwl_aweber();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->crm_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_crm.php';
                    $gateWay = new iwl_crm();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->plan_mc_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
                    $gateWay = new iwl_mailchimp();
                    $gateWay->autoresponder($plan, $user);
                }
                
                if ($plan->ihub_crm)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_ihub_crm.php';
                    $gateWay = new iwl_ihub_crm();
                    $gateWay->autoresponder($plan, $user);
                }
            }
        }
    }

    /**

     * Public method to process subscription

     *

     * @param array $data

     */
    public function buy_product($data)
    {
        // Import help library
        jimport('joomla.user.helper');

        // Get configuration
        $config = $this->getConfig();

        $data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
        $row = JTable::getInstance('product', 'JmsTable');
        $row->bind($data);

        // offset
        $format = 'Y-m-d H:i:s';
        $date = date($format);
        $row->created = date($format, strtotime('-1 day' . $date));
        $row->number = 0;
        $row->access_count = 0;
        $paymentMethod = $data['payment_method'];
        $row->payment_method = JText::_(strtoupper($paymentMethod));
        $row->parent = 0;
        $row->state = 0;
        $row->payment_made = 0;

        // Trigger JMS Plugins
        JPluginHelper::importPlugin('jmspayment');
        $dispatcher = JDispatcher::getInstance();
        $ret = $dispatcher->trigger('onJmsProcessPayment', array($row, $data, $plan, $config));

        $paymentSettings = $config->get('payment_settings');
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
            $gateWay = new iwl_mailchimp();
        }
    }

    public function buy_voucher($data)
    {
        // Import help library
        jimport('joomla.user.helper');

        // Get configuration
        $config = $this->getConfig();

        $data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
        $row = JTable::getInstance('voucher', 'JmsTable');
        $row->bind($data);

        // offset
        $format = 'Y-m-d H:i:s';
        $date = date($format);
        $row->created = date($format, strtotime('-1 day' . $date));
        $row->number = 0;
        $row->access_count = 0;
        $paymentMethod = $data['payment_method'];
        $row->payment_method = JText::_(strtoupper($paymentMethod));
        $row->parent = 0;
        $row->state = 0;
        $row->payment_made = 0;

        // Trigger JMS Plugins
        JPluginHelper::importPlugin('jmspayment');
        $dispatcher = JDispatcher::getInstance();
        $ret = $dispatcher->trigger('onJmsProcessPayment', array($row, $data, $plan, $config));
        $paymentSettings = $config->get('payment_settings');
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ($payment_type == 1)
        {
            require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
            $gateWay = new iwl_mailchimp();
        }
    }

    /**

     * Public method to process recurring subscription

     *

     * @param array $data

     */
    public function processRecurringSubscription($data)
    {
        // Import help library
        jimport('joomla.user.helper');
        $siteUrl = JURI::root();

        // Get configuration
        $config = $this->getConfig();

        // Get user
        $user = & JFactory::getUser();

        $data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
        $row = JTable::getInstance('subscr', 'JmsTable');
        $row->bind($data);

        // Get plan information
        $plan = $this->getPlan($row->plan_id);
        $row->user_id = $user->get('id');

        // offset
        $format = 'Y-m-d H:i:s';
        $date = date($format);
        $row->created = date($format, strtotime('-1 day' . $date));

        // Get maximum of expired date
        $maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);
        $row->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $maxExpDate);
        $row->number = 0;
        $row->access_count = 0;
        $row->access_limit = $plan->limit_time;
        $paymentMethod = $data['payment_method'];
        $row->payment_method = JText::_(strtoupper($paymentMethod));
        $row->parent = 0;
        $row->state = 0;
        $row->subscription_type = 'R';
        $row->payment_made = 0;
        $row->subscr_id = '';
        
        $user_id = $row->user_id;
        $plan_id = $row->plan_id;
        
        $query = "DELETE FROM #__jms_plan_subscrs WHERE user_id = '$user_id' AND plan_id = '$plan_id'";
        $this->_db->setQuery($query);
        $this->_db->query();
        
        $row->store();

        $query = "SELECT grant_points FROM #__jms_plans WHERE id = $plan_id";
        $this->_db->setQuery($query);
        $grant_points = $this->_db->loadResult();

        $query = "DELETE FROM #__jms_user_points WHERE user_id = $user_id";
        $this->_db->setQuery($query);
        $this->_db->query();

        $query = "INSERT INTO #__jms_user_points(user_id, points) VALUES($user_id, $grant_points)";
        $this->_db->setQuery($query);
        $this->_db->query();

        $gatewayData = array();

        // Require the payment method
        // Trigger JMS Plugins
        JPluginHelper::importPlugin('jmspayment');
        $dispatcher = JDispatcher::getInstance();
        $ret = $dispatcher->trigger('onJmsProcessRecurringPayment', array($row, $data, $plan, $config));

        $paymentSettings = $config->get('payment_settings');
        $type = $paymentMethod . '_type';
        $payment_type = $paymentSettings->$type;

        if ((in_array(1, $ret)) && ($payment_type == 1))
        {
            $this->_sendEmails($row, $config);

            // Add user to autoresponder
            if ($plan->autores_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_aweber.php';
                $gateWay = new iwl_aweber();
                $gateWay->autoresponder($plan, $user);
            }

            if ($plan->crm_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_crm.php';
                $gateWay = new iwl_crm();
                $gateWay->autoresponder($plan, $user);
            }

            if ($plan->plan_mc_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
                $gateWay = new iwl_mailchimp();
                $gateWay->autoresponder($plan, $user);
            }
            
            if ($plan->ihub_crm)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_ihub_crm.php';
                $gateWay = new iwl_ihub_crm();
                $gateWay->autoresponder($plan, $user);
            }
        }
    }

    /**

     * Process confirm subscription

     *

     */
    public function subscriptionConfirm()
    {
        $config = $this->getConfig();
        $data = JRequest::get('post');
        $paymentMethod = JRequest::getVar('payment_method');
        $paymentSettings = $config->get('payment_settings');
        $custom = $paymentMethod . '_custom';
        $payment_custom = $paymentSettings->$custom;
        $id = JRequest::getInt($payment_custom);
        $row = JTable::getInstance('subscr', 'JmsTable');
        $row->load($id);
        $ret = false;

        // Get plan information
        $plan = $this->getPlan($row->plan_id);

        // Get maximum of expired date
        $maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);

        // Trigger JMS Plugins
        JPluginHelper::importPlugin('jmspayment');
        $dispatcher = JDispatcher::getInstance();
        $ret = $dispatcher->trigger('onJmsSubscriptionConfirm', array($data, $plan, $config, $maxExpDate, $paymentMethod));

        if (in_array(1, $ret))
        {
            $row = JTable::getInstance('subscr', 'JmsTable');
            $row->load($id);
            $this->_sendEmails($row, $config);

            // Get user
            $user = & JFactory::getUser();

            // Add user to autoresponder
            if ($plan->autores_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_aweber.php';
                $gateWay = new iwl_aweber();
                $gateWay->autoresponder($plan, $user);
            }

            if ($plan->crm_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_crm.php';
                $gateWay = new iwl_crm();
                $gateWay->autoresponder($plan, $user);
            }

            if ($plan->plan_mc_enable)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
                $gateWay = new iwl_mailchimp();
                $gateWay->autoresponder($plan, $user);
            }
            
            if ($plan->ihub_crm)
            {
                require_once JPATH_COMPONENT . '/helpers/iwl_ihub_crm.php';
                $gateWay = new iwl_ihub_crm();
                $gateWay->autoresponder($plan, $user);
            }
        }
    }

    /**

     * Process recurring subscription confirm

     *

     */
    public function recurringSubscriptionConfirm()
    {
        $config = $this->getConfig();
        $data = JRequest::get('post');
        $paymentMethod = JRequest::getVar('payment_method');
        $paymentSettings = $config->get('payment_settings');
        $custom = $paymentMethod . '_custom';
        $payment_custom = $paymentSettings->$custom;
        $id = JRequest::getInt($payment_custom);

        $row = JTable::getInstance('subscr', 'JmsTable');
        $row->load($id);
        $ret = false;

        // Get plan information
        $plan = $this->getPlan($row->plan_id);

        // Get maximum of expired date
        $maxExpDate = $this->_getMaxExpDate($row->plan_id, $row->user_id);

        // Trigger JMS Plugins
        JPluginHelper::importPlugin('jmspayment');
        $dispatcher = JDispatcher::getInstance();
        $ret = $dispatcher->trigger('onJmsRecurringSubscriptionConfirm', array($data, $plan, $config, $maxExpDate, $paymentMethod));

        if (in_array(1, $ret))
        {
            $row = JTable::getInstance('subscr', 'JmsTable');
            $row->load($id);
            $txnType = JRequest::getVar('txn_type', '');

            if ($txnType == 'subscr_signup')
            {
                $this->_sendEmails($row, $config);

                // Get user
                $user = & JFactory::getUser();

                // Add user to autoresponder
                if ($plan->autores_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_aweber.php';
                    $gateWay = new iwl_aweber();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->crm_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_crm.php';
                    $gateWay = new iwl_crm();
                    $gateWay->autoresponder($plan, $user);
                }

                if ($plan->plan_mc_enable)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_mailchimp.php';
                    $gateWay = new iwl_mailchimp();
                    $gateWay->autoresponder($plan, $user);
                }
                
                if ($plan->ihub_crm)
                {
                    require_once JPATH_COMPONENT . '/helpers/iwl_ihub_crm.php';
                    $gateWay = new iwl_ihub_crm();
                    $gateWay->autoresponder($plan, $user);
                }
            }
            else
            {
                $this->_sendRecurringEmail($row, $config);
            }
        }
    }

    /**

     * Public method to process Arb Silent Post from authorize.net

     *

     * @param object $subscription

     */
    protected function arbSilentPostProcess($subscription)
    {
        $row = JTable::getInstance('subscr', 'JmsTable');
        $row->load($subscription->id);

        // Get plan information
        $plan = $this->getPlan($row->plan_id);

        $row->expired = $this->_getExpiredDate($plan->period, $plan->period_type, $row->expired);
        $row->access_limit = $row->access_limit + $plan->access_limit;
        $row->payment_made = $row->payment_made + 1;
        $row->price = $row->price + $_POST['x_amount'];
        $row->store();

        // Send recurring email
        $config = $this->getConfig();
        $this->_sendRecurringEmails($row, $config);
    }

    /**

     * Public method to get config object

     *

     * @return object

     */
    public function getConfig()
    {
        // Get the application object.
        $app = JFactory::getApplication();

        // Get the component config/params object.
        $config = $app->getParams('com_jms');

        return $config;
    }

    /**

     * Public method to get price for a specific subscription plan

     *

     * @param int $planId

     * @return plan object

     */
    public function getPlan($planId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_plans' .
                ' WHERE id = ' . (int) $planId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    /**

     *

     *

     */
    public function getCoupon($couponCode)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_coupons' .
                ' WHERE code = "' . $couponCode . '"'
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getCouponid($couponid)
    {
        $query = 'SELECT code' .
                ' FROM #__jms_coupons' .
                ' WHERE id = ' . $couponid . ''
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    /**

     * Private method to get maximum of expired date

     *

     * @param int $planId

     * @param int $userId

     * @return datetime

     */
    protected function _getMaxExpDate($planId, $userId)
    {
        $query = 'SELECT MAX(expired)' .
                ' FROM #__jms_plan_subscrs' .
                ' WHERE plan_id = ' . $planId .
                ' AND user_id = ' . $userId .
                ' AND expired > NOW()' .
                ' AND state = 1'
        ;
        $this->_db->setQuery($query);

        $maxExpDate = $this->_db->loadResult();

        // If max expired date is not set, then assign it to current date
        if (!isset($maxExpDate))
        {
            $maxExpDate = gmdate('Y-m-d H:i:s');
        }

        return $maxExpDate;
    }

    /**

     * Private method to get expired date

     *

     * @param int $periodType

     * @param datetime $maxExpDate

     * @return datetime

     */
    protected function _getExpiredDate($period, $periodType, $maxExpDate)
    {
        if ($periodType == 1)
        {
            $expired = gmdate('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int) JHTML::date($maxExpDate, 'M'), (int) JHTML::date($maxExpDate, 'S'), (int) JHTML::date($maxExpDate, 'm'), (int) JHTML::date($maxExpDate, 'd') + $period, (int) JHTML::date($maxExpDate, 'Y')));
        }
        else if ($periodType == 2)
        {
            $expired = gmdate('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int) JHTML::date($maxExpDate, 'M'), (int) JHTML::date($maxExpDate, 'S'), (int) JHTML::date($maxExpDate, 'm'), (int) JHTML::date($maxExpDate, 'd') + $period * 7, (int) JHTML::date($maxExpDate, 'Y')));
        }
        else if ($periodType == 3)
        {
            $expired = gmdate('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int) JHTML::date($maxExpDate, 'M'), (int) JHTML::date($maxExpDate, 'S'), (int) JHTML::date($maxExpDate, 'm') + $period, (int) JHTML::date($maxExpDate, 'd'), (int) JHTML::date($maxExpDate, 'Y')));
        }
        else if ($periodType == 4)
        {
            $expired = gmdate('Y-m-d H:i:s', mktime(JHTML::date($maxExpDate, 'H'), (int) JHTML::date($maxExpDate, 'M'), (int) JHTML::date($maxExpDate, 'S'), (int) JHTML::date($maxExpDate, 'm'), (int) JHTML::date($maxExpDate, 'd'), (int) JHTML::date($maxExpDate, 'Y') + $period));
        }
        else if ($periodType == 5)
        {
            $expired = '3009-12-31 23:59:59';
        }

        return $expired;
    }

    /**

     * Private method to send emails

     *

     * @param subscriber object $row

     * @param array $config

     */
    protected function _sendEmails($row, $config)
    {
        // Get global joomla configuration
        $jconfig = new JConfig();

        $fromEmail = $jconfig->mailfrom;
        $fromName = $jconfig->fromname;

        // Get user information
        $user = JFactory::getUser($row->user_id);

        // Send notification email to user
        $subject = $config->get('user_email_subject');
        $body = nl2br($config->get('user_email_body'));
        $subscriptionDetail = $this->_getSubscriptionDetail($row, $config);
        $body = str_replace('{subscription_detail}', $subscriptionDetail, $body);
        $body = str_replace('{username}', $user->username, $body);

        JUtility::sendMail($fromEmail, $fromName, $user->get('email'), $subject, $body, 1);

        // Send notification email to admin or notification emails address
        if ($config->get('notification_emails') == '')
        {
            $notificationEmails = $fromEmail;
        }
        else
        {
            $notificationEmails = $config->get('notification_emails');
        }

        $notificationEmails = str_replace(' ', '', $notificationEmails);
        $emails = explode(',', $notificationEmails);
        $subject = $config->get('admin_email_subject');
        $body = $config->get('admin_email_body');
        $body = nl2br($body);
        $subscriptionDetail = $this->_getSubscriptionDetail($row, $config);
        $body = str_replace('{subscription_detail}', $subscriptionDetail, $body);
        $body = str_replace('{name}', $user->get('name'), $body);
        $body = str_replace('{email}', '<a href="mailto:' . $user->get('email') . '">' . $user->get('email') . '</a>', $body);

        for ($i = 0, $n = count($emails); $i < $n; $i++)
        {
            $email = $emails[$i];
            JUtility::sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
        }
    }

    /**

     * Private method to send recurring emails

     *

     * @param subscriber object $row

     * @param array $config

     */
    protected function _sendRecurringEmails($row, $config)
    {
        // Get global joomla configuration
        $jconfig = new JConfig();

        $fromEmail = $jconfig->mailfrom;
        $fromName = $jconfig->fromname;

        // Get user information
        $user = JFactory::getUser($row->user_id);

        // Send notification recurring email to user
        $subject = $config->get('user_recurring_email_subject');
        $body = nl2br($config->get('user_recurring_email_body'));
        $subscriptionDetail = $this->_getSubscriptionDetail($row, $config);
        $body = str_replace('{subscription_detail}', $subscriptionDetail, $body);
        $body = str_replace('{username}', $user->username, $body);

        JUtility::sendMail($fromEmail, $fromName, $user->get('email'), $subject, $body, 1);

        // Send notification recurring email to admin or notofication emails address
        if ($config->get('notification_emails') == '')
        {
            $notificationEmails = $fromEmail;
        }
        else
        {
            $notificationEmails = $config->get('notification_emails');
        }

        $notificationEmails = str_replace(' ', '', $notificationEmails);
        $emails = explode(',', $notificationEmails);
        $subject = $config->get('admin_recurring_email_subject');
        $body = $config->get('admin_recurring_email_body');
        $body = nl2br($body);
        $subscriptionDetail = $this->_getSubscriptionDetail($row, $config);
        $body = str_replace('{subscription_detail}', $subscriptionDetail, $body);
        $body = str_replace('{name}', $user->get('name'), $body);
        $body = str_replace('{email}', '<a href="mailto:' . $user->get('email') . '">' . $user->get('email') . '</a>', $body);

        for ($i = 0, $n = count($emails); $i < $n; $i++)
        {
            $email = $emails[$i];
            JUtility::sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
        }
    }

    /**

     * Private method to get subscriber detail

     *

     * @param subscriber object $row

     * @param array $config

     * @return subscriber detail

     */
    protected function _getSubscriptionDetail($row, $config)
    {
        $plan = $this->getPlan($row->plan_id);
        $return = '<table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_SUBSCRIPTION_NAME') . '</td>
                                <td>' . $plan->name . '</td>
                        </tr>					
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_START_ON') . '</td>
                                <td>' . JHTML::date($row->created, '%d-%m-%Y') . '</td>
                        </tr>
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_FINISH_ON') . '</td>
                                <td>' . JHTML::date($row->expired, '%d-%m-%Y') . '</td>
                        </tr>
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_PRICE') . '</td>
                                <td>' . $config->get('currency_sign') . $row->price . '</td>
                        </tr>
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_LIMIT') . '</td>
                                <td>' . ($row->access_limit == 0 ? JText::_('COM_JMS_EMAIL_NO_LIMITS') : $row->access_limit) . '</td>
                        </tr>
                        <tr>
                                <td>' . JText::_('COM_JMS_EMAIL_SUBSCRIPTION_DESCRIPTION') . '</td>
                                <td>' . $plan->description . '</td>
                        </tr>
                </table>';

        return $return;
    }

    /**

     * Public method to cancel subscription

     *

     * @param array $data

     */
    public function cancelSubscription($data)
    {
        $id = (int) $data['id'];
        $sql = 'DELETE FROM #__jms_plan_subscrs' .
                ' WHERE id = ' . (int) $id
        ;
        $this->_db->setQuery($sql);

        $this->_db->query();
    }

    public function getProductsByCategoryId($categoryId, $order, $limitstart, $limit)
    {
        $query = "SELECT * 
            FROM #__jms_products 
            WHERE id IN (SELECT product_id FROM #__jms_categories_products WHERE category_id = $categoryId) AND state=1 ORDER BY $order 
            LIMIT $limitstart, $limit";

        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    public function getCountProductsByCategoryId($categoryId)
    {
        $query = "SELECT count(*) AS total 
            FROM #__jms_products 
            WHERE id IN (SELECT product_id FROM #__jms_categories_products WHERE category_id = $categoryId) AND state=1";

        $this->_db->setQuery($query);

        return $this->_db->loadResult();
    }

    public function isBuyByProductIdAndUserId($productId, $userId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_user_products' .
                ' WHERE product_id = ' . (int) $productId .
                ' AND user_id = ' . (int) $userId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function updateUserPoints($points, $userId)
    {
        $query = 'UPDATE #__jms_user_points' .
                ' SET points = ' . $points .
                ' WHERE user_id = ' . $userId
        ;
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

    public function insertUserProduct($userId, $productId, $payment, $created)
    {
        $query = "INSERT INTO #__jms_user_products(user_id, product_id, payment, created) 

                  VALUES($userId, $productId, '$payment', $created)"
        ;
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

    public function insertUserGiftvoucher($userId, $voucherId, $created)
    {
        $query = "INSERT INTO #__jms_user_giftvoucher(user_id, giftvoucher_id, created) 
                  VALUES($userId, $voucherId, $created)"
        ;
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

    public function getUserPointByUserId($userId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_user_points' .
                ' WHERE user_id = ' . (int) $userId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getProductById($productId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_products' .
                ' WHERE id = ' . (int) $productId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getVoucherById($voucherId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_vouchers' .
                ' WHERE id = ' . (int) $voucherId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getDownloadsByProductId($productId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_downloads' .
                ' WHERE id IN (SELECT download_id FROM #__jms_downloads_products WHERE product_id = ' . (int) $productId . ')'
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    public function getDownloadsById($downloadId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_downloads' .
                ' WHERE id = ' . (int) $downloadId
        ;
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getCategories()
    {
        $query = 'SELECT *' .
                ' FROM #__jms_categories WHERE state=1';

        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    public function getCategoryById($categoryId)
    {
        $query = 'SELECT *' .
                ' FROM #__jms_categories' .
                ' WHERE id = ' . (int) $categoryId
        ;

        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getproductbyuser($userId)
    {
        $query = "SELECT * 
            FROM #__jms_products 
            WHERE id IN (SELECT product_id FROM #__jms_user_products WHERE user_id = $userId) 
            ";
        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    public function getplan_userid($userId)
    {
        $query = "SELECT * 
            FROM #__jms_plans
            WHERE id IN (SELECT plan_id FROM #__jms_plan_subscrs WHERE user_id = $userId) 
            ";
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    public function getpoint_userid($userId)
    {
        $query = "SELECT * 
            FROM #__jms_user_points
            WHERE user_id = $userId 
            ";
        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }
}