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

/**
 * Jms helper.
 */
class JmsHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '') {
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_CONTROL_PANEL'),
			'index.php?option=com_jms',
			$vName == 'jms'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_PLANS'),
			'index.php?option=com_jms&view=plans',
			$vName == 'plans'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_SUBSCRS'),
			'index.php?option=com_jms&view=subscrs',
			$vName == 'subscrs'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_COUPONS'),
			'index.php?option=com_jms&view=coupons',
			$vName == 'coupons'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_CATEGORIES'),
			'index.php?option=com_jms&view=categories',
			$vName == 'categories'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_PRODUCTS'),
			'index.php?option=com_jms&view=products',
			$vName == 'products'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_SALES'),
			'index.php?option=com_jms&view=sales',
			$vName == 'sales'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_DOWNLOADS'),
			'index.php?option=com_jms&view=downloads',
			$vName == 'downloads'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_VOUCHERS'),
			'index.php?option=com_jms&view=vouchers',
			$vName == 'vouchers'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_SALES_VOUCHER'),
			'index.php?option=com_jms&view=salesvoucher',
			$vName == 'salesvoucher'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JMS_TITLE_ABOUT'),
			'index.php?option=com_jms&view=about',
			$vName == 'about'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_jms';
		}
		else {
			$assetName = 'com_jms.message.'.(int) $messageId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete',
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
