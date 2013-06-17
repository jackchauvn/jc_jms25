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
 * View class for a list of Jms.
 */
class JmsViewVouchers extends JView
{

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

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
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'jms.php';

        $state = $this->get('State');
        $canDo	= JmsHelper::getActions();

        JToolBarHelper::title(JText::_('COM_JMS_TITLE_VOUCHERS'), 'voucher.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'voucher';
        if (file_exists($formPath))
        {

            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('voucher.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit'))
            {
                JToolBarHelper::editList('voucher.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::custom('vouchers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('vouchers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::archiveList('vouchers.archive', 'JTOOLBAR_ARCHIVE');
        }
        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::deleteList('', 'vouchers.delete', 'JTOOLBAR_DELETE');
        }
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jms');
        }
    }

}
