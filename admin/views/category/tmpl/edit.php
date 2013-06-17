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
JHtml::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="category-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JMS_LEGEND_CATEGORY'); ?></legend>
			<ul class="adminformlist">

                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
                
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li> 
                
                <li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li> 
                
                <li><?php echo $this->form->getLabel('state'); ?>
                <?php echo $this->form->getInput('state'); ?></li>
                
                <li><?php echo $this->form->getLabel('access'); ?>
                <?php echo $this->form->getInput('access'); ?></li>     
                
                <li><?php echo $this->form->getLabel('checked_out'); ?>
                <?php echo $this->form->getInput('checked_out'); ?></li>
                
                <li><?php echo $this->form->getLabel('checked_out_time'); ?>
                <?php echo $this->form->getInput('checked_out_time'); ?></li>
                
                <div class="clr"></div>
                <li><?php echo $this->form->getLabel('description'); ?>
                <div class="clr"></div>
                <?php echo $this->form->getInput('description'); ?></li>
            </ul>
		</fieldset>
	</div>
    
        <div class="width-40 fltrt">
            <?php echo JHtml::_('sliders.start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
            <?php echo JHtml::_('sliders.panel', JText::_('COM_JMS_CATEGORIES_FIELDSET_PRODUCTS'), 'products'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('products'); ?>
                    <?php echo $this->form->getInput('products'); ?></li>
                    <?php foreach($this->form->getGroup('attribs') as $field): ?>
                        <li>
                            <?php if (!$field->hidden): ?>
                                <?php echo $field->label; ?>
                            <?php endif; ?>
                            <?php echo $field->input; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </fieldset>
            <?php echo JHtml::_('sliders.panel', JText::_('COM_JMS_CATEGORIES_FIELDSET_IMAGES'), 'images'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <?php echo $this->form->getLabel('images'); ?>
                    <?php echo $this->form->getInput('images'); ?></li>
                    <?php foreach($this->form->getGroup('images') as $field): ?>
                        <li>
                            <?php if (!$field->hidden): ?>
                                <?php echo $field->label; ?>
                            <?php endif; ?>
                            <?php echo $field->input; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </fieldset>
            <?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>

<style> 
    table   { width: 100% !important; }
</style>