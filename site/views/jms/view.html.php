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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Jms component
 */
class JmsViewJms extends JView
{

    protected $data;
    protected $form;
    protected $params;
    protected $state;
    protected $item;

    /**
     * Method to display the view.
     *
     * @param	string	The template file to include
     * @since	1.6
     */
    public function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $config = JComponentHelper::getParams('com_jms');

        JHTML::stylesheet('components/com_jms/assets/jms.css');
        JHTML::stylesheet('components/com_jms/assets/fancybox/jquery.fancybox-1.3.4.css');
        JHTML::script('components/com_jms/assets/jquery-1.6.2.min.js');
        JHTML::script('components/com_jms/assets/fancybox/jquery.fancybox-1.3.4.pack.js');

        switch ($this->getLayout())
        {
            case 'history':
                {
                    $this->_displayHistory($tpl);
                    return;
                }
            case 'cancel':
            case 'complete':
                {
                    $this->_displayFinalPage($tpl);
                    return;
                }
            case 'productdetails':
                {
                    $this->_displayProductDetails($tpl);
                    return;
                }
            case 'categories':
                {
                    $this->_displayCategories($tpl);
                    return;
                }
            case 'category':
                {
                    $this->_displayCategory($tpl);
                    return;
                }
            case 'order':
                {
                    $this->_displayOrder($tpl);
                    return;
                }
            case 'ordervoucher':
                {
                    $this->_displayOrderVoucher($tpl);
                    return;
                }
            case 'myaccount':
                {
                    $this->_displayMyAccount($tpl);
                    return;
                }
            case 'failure':
                {
                    $this->_displayFailure($tpl);
                    return;
                }
            case 'default':
                {
                    $this->setLayout('default');
                }
        }

        // Get some data from the models
        $this->items = $this->get('Data');
        $this->state = $this->get('State');
        $this->params = $this->state->get('params');
        $this->user = JFactory::getUser();
        $this->form = $this->get('Form');
        
        $uri = JFactory::getURI();
        $url = $uri->toString(array('path', 'query', 'fragment'));
        $session = JFactory::getSession();

        if ($session->get('jms_access_url'))
        {
            $url = $session->get('jms_access_url');
        }

        $this->url = base64_encode($url);

        // Get return post variables
        $rTimes = JRequest::getVar('r_times', '2');
        $paymentMethod = JRequest::getVar('payment_method', 'iwl_paypal');

        $this->assignRef('rTimes', $rTimes);
        $this->assignRef('paymentMethod', $paymentMethod);

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        parent::display($tpl);
    }

    /**
     * Private method to display history
     *
     * @param template object $tpl
     */
    protected function _displayHistory($tpl)
    {
        // Get configuration
        $this->state = $this->get('State');
        $this->params = $this->state->get('params');
        // Get subscriptions
        $this->items = $this->get('Subscriptions');
        parent::display($tpl);
    }

    /**
     * Private method to display final (cancel/complete) page
     *
     * @param template object $tpl
     */
    protected function _displayFinalPage($tpl)
    {
        // Get configuration
        $planId = JRequest::getInt('plan_id');

        $model = $this->getModel();
        $this->plan = $model->getPlan($planId);

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }

    protected function _displayProductDetails($tpl)
    {
        $app = &JFactory::getApplication();
        $params = &$app->getParams();
        
        $model = $this->getModel();
        $productId = JRequest::getInt('id');

        $this->product = $model->getProductById($productId);

        $this->product->description = trim($this->product->full_description) != '' ? $this->product->short_description . $this->product->full_description : $this->product->short_description;

        $registry = new JRegistry;
        $registry->loadString($this->product->images);
        $product_image = $registry->toArray();
        $this->product_image = $product_image["image_intro"];

        $this->categories = $model->getCategories();

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');
        $this->fade_purchased_products = $params->get('fade_purchased_products');

        parent::display($tpl);
    }

    protected function _displayCategories($tpl)
    {
        $model = $this->getModel();
        $this->categories = $model->getCategories();

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }

    protected function _displayCategory($tpl)
    {
        $app = &JFactory::getApplication();
        $params = &$app->getParams();
        
        $model = $this->getModel();
        $categoryId = JRequest::getInt('id');
        $this->limitstart = JRequest::getInt('limitstart');

        $this->categories = $model->getCategories();
        $this->category = $model->getCategoryById($categoryId);

        $this->category->description = trim($this->category->full_description) != '' ? $this->category->short_description . $this->category->full_description : $this->category->short_description;

        $registry = new JRegistry;
        $registry->loadString($this->category->attribs);
        $this->attribs = $registry->toArray();
        
        if($params->get('number_of_products') != null) $this->attribs["number_of_products"] = $params->get('number_of_products');
        if($params->get('ordering') != null) $this->attribs["ordering"] = $params->get('ordering');
        if($params->get('number_of_columns') != null) $this->attribs["number_of_columns"] = $params->get('number_of_columns');

        $this->products = $model->getProductsByCategoryId($categoryId, $this->attribs["ordering"], $this->limitstart, $this->attribs["number_of_products"]);
        $this->count = $model->getCountProductsByCategoryId($categoryId);

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');
        $this->fade_purchased_products = $params->get('fade_purchased_products');

        parent::display($tpl);
    }

    protected function _displayOrder($tpl)
    {
        $model = $this->getModel();
        $productId = JRequest::getInt('id');

        $this->categories = $model->getCategories();
        $this->product = $model->getProductById($productId);

        $this->product->description = trim($this->product->full_description) != '' ? $this->product->short_description . $this->product->full_description : $this->product->short_description;

        $registry = new JRegistry;
        $registry->loadString($this->product->images);
        $product_image = $registry->toArray();

        $this->product_image = $product_image["image_intro"];

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }

    protected function _displayOrderVoucher($tpl)
    {
        $model = $this->getModel();
        $voucherId = JRequest::getInt('id');
        
        $_SESSION['voucher'] = $_REQUEST;

        $this->voucher = $model->getVoucherById($voucherId);

        $this->state = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }

    protected function _displayMyAccount($tpl)
    {
        $model = $this->getModel();
        $this->user = JFactory::getUser();

        $this->products = $model->getproductbyuser($this->user->id);
        $this->plan_useid = $model->getplan_userid($this->user->id);
        $this->point_useid = $model->getpoint_userid($this->user->id);
        $this->state = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }

    /**
     * Private method to display failure page from authorize.net payment gateway
     *
     * @param template object $tpl
     */
    protected function _displayFailure($tpl)
    {
        $session = JFactory::getSession();
        if ($session->get('cc_reason', '', 'cc_error'))
        {
            $reason = $session->get('cc_reason', '', 'cc_error');
        }
        else
        {
            $reason = JRequest::getVar('reason', '');
        }
        $this->assignRef('reason', $reason);
        parent::display($tpl);
    }

}