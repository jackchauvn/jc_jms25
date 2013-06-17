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

JHtml::_('behavior.tooltip');
?>
<div id="cpanel">
<div class="cpanel-left">

<h2 class="jms_h2"><?php echo JText::_( 'COM_JMS_COMPONENT_LABEL' ); ?></h2>

<p class="jms_pdesc"><?php echo JText::_( 'COM_JMS_ICO_ABOUT_TIP' ); ?></p>

<div style="float:left;">
	<div class="icon">
        <a href="index.php?option=com_jms&amp;view=plans" style="text-decoration:none;" title="Subscription Plans">
            <img src="components/com_jms/assets/images/icon-48-plan.png" align="middle" border="0"/>
            <span><?php echo JText::_( 'COM_JMS_ICO_PLANS' ); ?></span>
        </a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=subscrs" style="text-decoration:none;" title="Subscribers">
			<img src="components/com_jms/assets/images/icon-48-subscriber.png" align="middle" border="0"/>
				<span><?php echo JText::_( 'COM_JMS_ICO_SUBSCRIBERS' ); ?></span>
			</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=coupons" style="text-decoration:none;" title="Coupons">
			<img src="components/com_jms/assets/images/icon-48-coupon.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_COUPONS' ); ?></span>
		</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=categories" style="text-decoration:none;" title="Categories">
			<img src="components/com_jms/assets/images/icon-48-category.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_CATEGORIES' ); ?></span>
		</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=products" style="text-decoration:none;" title="Products">
			<img src="components/com_jms/assets/images/icon-48-product.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_PRODUCTS' ); ?></span>
		</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=downloads" style="text-decoration:none;" title="Downloads">
			<img src="components/com_jms/assets/images/icon-48-download.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_DOWNLOADS' ); ?></span>
		</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=vouchers" style="text-decoration:none;" title="Vouchers">
			<img src="components/com_jms/assets/images/icon-48-voucher.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_VOUCHERS' ); ?></span>
		</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="index.php?option=com_jms&amp;view=about" style="text-decoration:none;" title="About">
			<img src="components/com_jms/assets/images/icon-48-about.png" align="middle" border="0"/>
			<span><?php echo JText::_( 'COM_JMS_ICO_ABOUT' ); ?></span>
		</a>
	</div>
</div>

</div>

<div class="cpanel-right" align="right">
<img src="components/com_jms/assets/images/jms300.png" align="middle" border="0"/>
</div>

<div class="clr"></div>

</div>
