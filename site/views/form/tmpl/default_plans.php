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
?>

<h1><?php echo $this->params->get('subscription_page_title'); ?></h1>

<div class="contentpanopen">

    <p><?php echo $this->params->get('subscription_page_text'); ?></p>

<?php
if (!count($this->plans))
{
    ?>

        <p><?php echo JText::_('COM_JMS_LAYOUT_NO_PLANS'); ?></p>

<?php }
else
{ ?>

        <fieldset name="plans">
            <legend><?php echo $this->form->getLabel('sid'); ?></legend>
            <div><?php echo $this->form->getInput('sid'); ?></div>
        </fieldset>

        <fieldset name="coupon">
            <legend><?php echo JText::_('COM_JMS_COUPON'); ?></legend>
            <div><?php echo $this->form->getLabel('coupon'); ?><br /><?php echo $this->form->getInput('coupon'); ?></div>
        </fieldset>

        <fieldset name="payment">
            <legend><?php echo JText::_('COM_JMS_PAYMENT_METHOD'); ?></legend>
            <div><?php echo $this->form->getInput('payment_method'); ?></div>
        </fieldset>

    <?php } ?>

</div>
<div class="clr" style="clear:both"></div>