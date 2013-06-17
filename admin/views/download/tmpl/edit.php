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

<form action="<?php echo JRoute::_('index.php?option=com_jms&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="download-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_JMS_LEGEND_DOWNLOAD'); ?></legend>
            <ul class="adminformlist">

                <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>

                <li><?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?></li> 

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
        <?php echo JHtml::_('sliders.start', 'content-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JMS_DOWNLOAD_FIELDSET_DOWNLOAD_LINK'), 'download-link'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                    <?php echo $this->form->getLabel('link'); ?>
                    <?php echo $this->form->getInput('link'); ?>
                </li>
                <?php foreach ($this->form->getGroup('link') as $field): ?>
                    <li>
                        <?php if($field->name == "jform[link][protected1]" || $field->name == "jform[link][protected2]") { ?>
                            <div class="clr"></div>
                            <label></label>
                        <?php echo JText::_('COM_JMS_DOWNLOAD_FIELD_PROTECTED_LABEL'); 
                            continue; 
                        } ?>
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
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script language=javascript>
	function getSelect(obj)
	{
		var tag = document.getElementsByName (obj);
		for (var i=0; i<tag.length; ++i)
		{
			if (tag[i].checked) return tag[i].value;
		}
	}
	function change(obj)
	{
		var tag = document.getElementsByName (obj);
		
		tag[0].checked = true;
		tag[1].checked = false;
	}
	
	var link = new Array;
	link[0] = "jform[link][local_link_radio]";
	link[1] = "jform[link][external_link_radio]";
	link[2] = "jform[link][amazon_s3_link_radio]";
	// Local
	$("[name='"+link[0]+"']").click(
		function()
		{
			if (getSelect(link[0])==1)
				for (var j=0; j<3; ++j)
					if (j!=0)
						change (link[j]);
		}
	)
	// External
	$("[name='"+link[1]+"']").click(
		function()
		{
			if (getSelect(link[1])==1)
				for (var j=0; j<3; ++j)
					if (j!=1)
						change (link[j]);
		}
	)
	// Amazon
	$("[name='"+link[2]+"']").click(
		function()
		{
			if (getSelect(link[2])==1)
				for (var j=0; j<3; ++j)
					if (j!=2)
						change (link[j]);
		}
	)
</script>

<style> 
    table   { width: 100% !important; }
</style>