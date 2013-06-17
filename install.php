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

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_coupons` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code` varchar(100) DEFAULT NULL,
	`discount` double(12,2) DEFAULT NULL,
	`discount_type` tinyint(1) unsigned DEFAULT NULL,
	`recurring` tinyint(1) unsigned DEFAULT NULL,
	`num_recurring` int(11) DEFAULT NULL,
	`strict` tinyint(1) unsigned DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`expired` datetime DEFAULT NULL,
	`user_ids` text,
	`plan_ids` text,
	`product_ids` text,
	`limit_time` int(11) DEFAULT NULL,
	`limit_time_user` int(11) DEFAULT NULL,
	`used_time` int(11) DEFAULT NULL,
	`checked_out` int(11) NOT NULL DEFAULT '0',
	`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`state` tinyint(1) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_coupon_subscrs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`coupon_id` int(11) DEFAULT NULL,
	`plan_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`price` float(10,2) DEFAULT NULL,
	`discount` float(10,2) DEFAULT NULL,
	`discount_type` tinyint(1) unsigned DEFAULT NULL,
	`recurring` tinyint(1) unsigned DEFAULT NULL,
	`num_recurring` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `plan_type` varchar(50) NOT NULL,
  `period` int(11) DEFAULT NULL,
  `period_type` tinyint(1) unsigned DEFAULT NULL,
  `number_of_installments` int(11) NOT NULL,
  `limit_time` int(11) DEFAULT NULL,
  `limit_time_type` tinyint(1) unsigned DEFAULT NULL,
  `one_time` tinyint(1) unsigned DEFAULT NULL,
  `price` float(10,2) DEFAULT NULL,
  `discount` float(10,2) DEFAULT NULL,
  `state` tinyint(1) unsigned DEFAULT NULL,
  `hidden_plan` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `description` mediumtext,
  `completed_msg` mediumtext,
  `cancel_msg` mediumtext,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `category_type` tinyint(1) unsigned DEFAULT NULL,
  `categories` text,
  `component_type` tinyint(1) unsigned DEFAULT NULL,
  `components` text,
  `user_type` tinyint(1) unsigned DEFAULT NULL,
  `article_type` tinyint(1) unsigned DEFAULT NULL,
  `articles` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `params` text,
  `date_strict` tinyint(3) unsigned DEFAULT NULL,
  `grant_new_user` tinyint(1) unsigned DEFAULT NULL,
  `grant_old_user` tinyint(1) unsigned DEFAULT NULL,
  `grant_url` text,
  `grant_plans` text NOT NULL,
  `alert_admin` tinyint(1) unsigned DEFAULT NULL,
  `gid` int(11) DEFAULT NULL,
  `adwords` text,
  `content_category` text,
  `cross_plans` text,
  `autores_enable` tinyint(1) NOT NULL,
  `autores_url` text NOT NULL,
  `autores_redirect` text NOT NULL,
  `autores_list` varchar(255) NOT NULL,
  `crm_enable` tinyint(1) NOT NULL,
  `crm_url` text NOT NULL,
  `inf_form_xid` text NOT NULL,
  `inf_form_name` text NOT NULL,
  `infusionsoft_version` text NOT NULL,
  `plan_mc_enable` tinyint(1) NOT NULL,
  `plan_mc_api` text NOT NULL,
  `plan_mc_listid` text NOT NULL,
  `plan_mc_groupid` text NOT NULL,
  `grant_points` int(11) DEFAULT NULL,
  `points_expire` tinyint(1) unsigned DEFAULT NULL,
  `jmsproducts` text,
  `jmscategories` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_plan_subscrs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`plan_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`expired` datetime DEFAULT NULL,
	`price` float(10,2) DEFAULT NULL,
	`number` varchar(200) DEFAULT NULL,
	`access_count` int(11) DEFAULT NULL,
	`access_limit` int(11) DEFAULT NULL,
	`payment_method` varchar(50) DEFAULT 'undefined',
	`transaction_id` varchar(255) DEFAULT NULL,
	`checked_out` int(11) NOT NULL DEFAULT '0',
	`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`parent` tinyint(1) unsigned DEFAULT NULL,
	`state` int(11) DEFAULT NULL,
	`subscription_type` varchar(50) NOT NULL,
	`r_times` int(11) NOT NULL,
	`payment_made` int(11) NOT NULL,
	`subscr_id` varchar(50) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `alias` varchar(256) NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `price_points` double NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `short_description` text,
  `full_description` text,
  `images` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `alias` varchar(256) NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `short_description` text,
  `full_description` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned DEFAULT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attribs` varchar(5120) NOT NULL,
  `images` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_categories_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `link` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) unsigned NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_downloads_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `download_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_user_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `points` varchar(11) NOT NULL,
  `description` varchar(123) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_user_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `payment` varchar(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `price_points` double NOT NULL DEFAULT '0',
  `message` varchar(256) NOT NULL,
  `allow_edit` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();
$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jms_user_giftvoucher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `giftvoucher_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_plans` ADD COLUMN `hidden_plan` tinyint(1) unsigned NOT NULL default 0 AFTER state");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_plans` ADD COLUMN `grant_points` int(11) unsigned NOT NULL default 0 AFTER plan_mc_groupid");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_plans` ADD COLUMN `points_expire` tinyint(1) unsigned NOT NULL default 0 AFTER grant_points");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_plans` ADD COLUMN `jmsproducts` text AFTER points_expire");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_plans` ADD COLUMN `jmscategories` text AFTER jmsproducts");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_categories` ADD COLUMN `short_description` text AFTER access");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_categories` ADD COLUMN `full_description` text AFTER access");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_products` ADD COLUMN `short_description` text AFTER access");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_products` ADD COLUMN `full_description` text AFTER access");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_products` ADD COLUMN `item_number` text AFTER alias");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_products` ADD COLUMN `tags` text AFTER alias");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_vouchers` ADD COLUMN `price_points` double NOT NULL DEFAULT '0' AFTER price");
$db->query();

$db->setQuery("ALTER TABLE `#__jms_coupons` ADD COLUMN `product_ids` text AFTER plan_ids");
$db->query();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmscontent');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmsutilities');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmscomponent');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmsgrant');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmspayment_authnet');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmspayment_eway');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmspayment_moneybooker');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmspayment_paypal');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmsloadsubscription');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmsloaddescription');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/plg_jmsloadaccountmodule');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/extensions/com_jcmedia');

// Publish Plugins
$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmscontent' AND folder = 'content'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmsutilities' AND folder = 'content'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmscomponent' AND folder = 'system'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmsgrant' AND folder = 'user'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'authnet' AND folder = 'jmspayment'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'eway' AND folder = 'jmspayment'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'moneybooker' AND folder = 'jmspayment'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'paypal' AND folder = 'jmspayment'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmsloadsubscription' AND folder = 'content'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmsloaddescription' AND folder = 'content'");
$db->query();

$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE type = 'plugin' AND element = 'jmsloadaccountmodule' AND folder = 'content'");
$db->query();

$db->setQuery("DELETE FROM `#__menu` WHERE title = 'com_jcmedia'");
$db->query();

?>

<img src="components/com_jms/assets/images/jms300.png" alt="JMS Membership Sites" width="286" height="300" border="0" />

<h2>JMS Membership Sites Installation</h2>
<h3><a href="index.php?option=com_jms">Go to JMS Membership Sites</a></h3>
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
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'JMS Membership Sites '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<tr>
			<th colspan="3"><?php echo JText::_('Plugins'); ?></th>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Content Restrictions'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'Content - JMS Membership Sites - Utilities'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'System - JMS Membership Sites - Component Restrictions'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'User - JMS Membership Sites - Grant User'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row1">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Authorize.Net'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Eway'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row1">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - MoneyBooker'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
        <tr class="row0">
			<td class="key" colspan="2"><?php echo 'JMS Payment - JMS Payment - Paypal'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	</tbody>
</table>