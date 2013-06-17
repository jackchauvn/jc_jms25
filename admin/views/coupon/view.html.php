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
 * View to edit
 */
class JmsViewCoupon extends JView
{

    protected $state;
    protected $item;
    protected $form;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        JHtml::stylesheet('administrator/components/com_jms/assets/jms.css');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        JRequest::setVar('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out))
        {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }
        else
        {
            $checkedOut = false;
        }
        $canDo = JmsHelper::getActions();

        JToolBarHelper::title($isNew ? JText::_('COM_JMS_TITLE_COUPON_NEW') : JText::_('COM_JMS_TITLE_COUPON_EDIT'), 'coupon.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
        {

            JToolBarHelper::apply('coupon.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('coupon.save', 'JTOOLBAR_SAVE');
        }
        if (!$checkedOut && ($canDo->get('core.create')))
        {
            JToolBarHelper::custom('coupon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('coupon.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            JToolBarHelper::cancel('coupon.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
