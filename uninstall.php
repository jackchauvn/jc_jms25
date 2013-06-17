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

// Imports
jimport('joomla.installer.installer');

$db = &JFactory::getDBO();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_coupons_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_coupons` TO `#__jms_coupons_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_coupon_subscrs_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_coupon_subscrs` TO `#__jms_coupon_subscrs_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_plans_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_plans` TO `#__jms_plans_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_plan_subscrs_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_plan_subscrs` TO `#__jms_plan_subscrs_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_products_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_products` TO `#__jms_products_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_categories_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_categories` TO `#__jms_categories_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_categories_products_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_categories_products` TO `#__jms_categories_products_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_downloads_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_downloads` TO `#__jms_downloads_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_downloads_products_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_downloads_products` TO `#__jms_downloads_products_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_user_products_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_user_products` TO `#__jms_user_products_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_user_points_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_user_points` TO `#__jms_user_points_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_vouchers_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_vouchers` TO `#__jms_vouchers_bak`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__jms_user_giftvoucher_bak`");
$db->query();
$db->setQuery("RENAME TABLE `#__jms_user_giftvoucher` TO `#__jms_user_giftvoucher_bak`");
$db->query();

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmscontent' AND folder = 'content' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmsloadsubscription' AND folder = 'content' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmsloaddescription' AND folder = 'content' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmsutilities' AND folder = 'content' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmscomponent' AND folder = 'system' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'jmsgrant' AND folder = 'user' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'authnet' AND folder = 'jmspayment' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'eway' AND folder = 'jmspayment' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'moneybooker' AND folder = 'jmspayment' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'paypal' AND folder = 'jmspayment' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'component' AND element = 'com_jcmedia' AND folder = 'com_jcmedia' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('component', $id);
}

?>

<h2>JMS Membership Sites Removal</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<th colspan="3"><?php echo JText::_('Core'); ?></th>
		</tr>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'JMS Membership Sites '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr>
			<th colspan="3"><?php echo JText::_('Plugins'); ?></th>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Content Restrictions'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Content Load Subscription'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Content Load Voucher Description'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Utilities'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'System - JMS Membership Sites - Component Restrictions'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'User - JMS Membership Sites - Grant User'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row1">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Authorize.Net'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Eway'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row1">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - MoneyBooker'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Paypal'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	</tbody>
</table>