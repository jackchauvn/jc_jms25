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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$Itemid = JRequest::getInt('Itemid');
$document = Jfactory::getDocument();

if($this->user->get('id')) :
	
	$paymentMethodsArr = $this->params->get('payment_method');
	
?>

	<script language="javascript" type="text/javascript">	
	
		if (typeof jQuery == 'undefined') { 
			<?php $document->addScript( 'components/com_jms/assets/jquery.js' ) ;?>
		}
			
		jQuery(document).ready(function($) {

			var sid = $('input[name="jform[sid]"]');
			
			//var checked = $('input[name=\"jform[sid]\"]:checked', '#subscrform');
			//var id = $(sid).attr('id').replace(/jform_si/, '');
			
			$(sid).click(function() {
				
				var id = $(this).attr('id').replace(/jform_sid/, '');
				var r_times = $('#r_times' + id).val();
				var subscription_type = $('#subscription_type' + id).val();
				
				$('input[name="r_times"]').val(r_times);
				$('input[name="subscription_type"]').val(subscription_type);
				
				//alert(subscription_type);
			
				if (subscription_type == 'R') {
					$('input[name="task"]').val('jms.process_recurring_subscription');
				} else {
					$('input[name="task"]').val('jms.process_subscription');
				}
				
			});
			
		// popup
			$('a.jms-window').click(function() {
				
				// Getting the variable's value from a link 
				var jmspopupBox = $(this).attr('href');
		
				//Fade in the Popup and add close button
				$(jmspopupBox).fadeIn(300);
				
				//Set the center alignment padding + border
				var popMargTop = ($(jmspopupBox).height() + 24) / 2; 
				var popMargLeft = ($(jmspopupBox).width() + 24) / 2; 
				
				$(jmspopupBox).css({ 
					'margin-top' : -popMargTop,
					'margin-left' : -popMargLeft
				});
				
				// Add the mask to body
				$('body').append('<div id="jms-mask"></div>');
				$('#jms-mask').fadeIn(300);
				
				return false;
			});
			
			// When clicking on the button close or the mask layer the popup closed
			$('a.jms-close, #jms-mask').live('click', function() { 
				$('#jms-mask , .jms-popup').fadeOut(300 , function() {
					$('#jms-mask').remove();  
				}); 
			
			return false;
			
			});
			
			// accept terms
			$('input#accept_terms').addClass('unchecked');
			$('.label_error').hide();
			
			$('input#accept_terms').click(function() {
				
				var checked_status = this.checked;
				
				if (checked_status == true) {
					$(this).removeClass('unchecked');
					$(this).toggleClass('checked');
					$('.label_error').hide();
				} else {
					$(this).removeClass('checked');
					$(this).toggleClass('unchecked');
					$('.label_error').show();
				}
			});
			
			$('input.validate').click(function() {
				
				if ($('input#accept_terms').hasClass('unchecked')) {
					$('.label_error').show();
					return false;
				} else {
					$('.label_error').hide();
				}
				
				return true;
			
			});
			
			
		});

	</script>
    
<h1><?php echo $this->params->get('subscription_page_title'); ?></h1>

<div class="contentpanopen">

    <p><a href="<?php echo JRoute::_("index.php?option=com_jms&view=jms&layout=history&Itemid=$Itemid"); ?>">
    <?php echo $this->params->get('history_page_title'); ?></a></p>
    
    <form action="index.php" method="post" name="subscrform" id="subscrform" class="form-validate jms-form">
        
            <p><?php echo $this->params->get('subscription_page_text'); ?></p>
        
        <?php if (!count($this->items)) : ?>
        
            <p><?php echo JText::_('COM_JMS_LAYOUT_NO_PLANS'); ?></p>
        
        <?php else : ?>
            
            <fieldset name="plans">
                <legend><?php echo $this->form->getLabel('sid'); ?></legend>
                <p><?php echo $this->form->getInput('sid'); ?></p>
            </fieldset>
    
            <fieldset name="coupon">
                <legend><?php echo JText::_('COM_JMS_COUPON'); ?></legend>
                <p><?php echo $this->form->getLabel('coupon'); ?><br /><?php echo $this->form->getInput('coupon'); ?></p>
            </fieldset>
            
            <fieldset name="payment">
                <legend><?php echo JText::_('COM_JMS_PAYMENT_METHOD'); ?></legend>
                <p><?php echo $this->form->getInput('payment_method'); ?></p>
            </fieldset>
            
            <?php if ($this->params->get('terms_conditions')) : ?>
            <fieldset name="terms">
                <legend><?php echo JText::_('COM_JMS_TERMS'); ?></legend>
                <p>
                    <input type="checkbox" name="accept_terms" id="accept_terms" class="inputbox" /> <?php echo JText::_('COM_JMS_AGREE_PRE_TEXT'); ?> <a class="jms-window" href="#jms-box"> <?php echo $this->params->get('terms_link'); ?></a>
                    <span class="label_error invalid"><br /><?php echo JText::_('COM_JMS_AGREE_TERMS_ERROR'); ?> <?php echo $this->params->get('terms_link'); ?></span>
                </p>
            </fieldset>
            <?php endif; ?>
            
            <input type="submit" class="validate button" name="btnProcess" id="btnProcess" value="<?php echo JText::_('COM_JMS_PROCESS_SUBSCRIPTION'); ?>" />
        
        <?php endif; ?>
                
            <input type="hidden" name="option" value="com_jms" />
            <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
            <input type="hidden" name="view" value="jms" />
            <input type="hidden" value="" name="task" />
                
        <?php if($this->params->get('mc_enable')) : ?>
            <!-- mailchimp -->
            <input type="hidden" name="mc_api_key" value="<?php echo $this->params->get('mc_api_key'); ?>" />
            <input type="hidden" name="mc_listid" value="<?php echo $this->params->get('mc_listid'); ?>" />
            <input type="hidden" name="mc_groupid" value="<?php echo $this->params->get('mc_groupid'); ?>" />
            <!-- /mailchimp -->
        <?php endif; ?>
    <input type="hidden" name="jform[return]" value="<?php echo $this->url; ?>" />
    </form>
	    
<script language="javascript" type="text/javascript">		
		
	var form = document.subscrform;
		
	if (form.subscription_type.value == 'I') {
		form.task.value = 'jms.process_subscription';
	} else {
		form.task.value = 'jms.process_recurring_subscription';
	}

</script>
	 
</div>

<div class="clr"></div>

<?php endif; ?>

<div style="display: none; margin-top: -113px; margin-left: -130px;" id="jms-box" class="jms-popup">
    
    <a href="#" class="jms-close"><img src="components/com_jms/assets/images/close_pop.png" class="btn_close" title="Close Window" alt="Close"></a>
    
    <div id="jms-external"></div>
    
    <script type="text/javascript">
		jQuery(document).ready(function($){
			$('#jms-external').load('index.php?option=com_content&view=article&id=<?php echo $this->params->get('terms_conditions'); ?>&Itemid=<?php echo JRequest::getInt('Itemid'); ?>&tmpl=component');
		});
	</script>
            
</div>