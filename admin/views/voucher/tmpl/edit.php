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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jms&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="voucher-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_JMS_LEGEND_VOUCHER'); ?></legend>
            <ul class="adminformlist">

                <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>

                <li><?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?></li> 

                <li><?php echo $this->form->getLabel('price'); ?>
                    <?php echo $this->form->getInput('price'); ?></li> 
                
                <li><?php echo $this->form->getLabel('price_points'); ?>
                    <?php echo $this->form->getInput('price_points'); ?></li> 

                <li><?php echo $this->form->getLabel('message'); ?>
                    <?php echo $this->form->getInput('message'); ?></li> 

                <li><?php echo $this->form->getLabel('allow_edit'); ?>
                    <?php echo $this->form->getInput('allow_edit'); ?></li> 

                <li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li>

                <li><?php echo $this->form->getLabel('checked_out'); ?>
                    <?php echo $this->form->getInput('checked_out'); ?></li>

                <li><?php echo $this->form->getLabel('checked_out_time'); ?>
                    <?php echo $this->form->getInput('checked_out_time'); ?></li>

                <div class="clr"></div>
                <li><?php echo $this->form->getLabel('description'); ?>
                    <div class="clr"></div>
                    <?php echo JText::_('COM_JMS_VOUCHERS_DESCRIPTION_DESC'); ?>
                    <div class="clr"></div>
                    <?php echo $this->form->getInput('description'); ?></li>
            </ul>
        </fieldset>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>

<style> 
    table   { width: 100% !important; }
</style>