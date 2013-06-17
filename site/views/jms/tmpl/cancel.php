<?php
session_start();

unset($_SESSION['sender_name']);
unset($_SESSION['sender_email']);
unset($_SESSION['receiver_name']);
unset($_SESSION['receiver_email']);
unset($_SESSION['message']);
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

?>

<?php $Itemid = JRequest::getInt('Itemid'); ?>

<h1><?php echo JText::_('COM_JMS_PURCHASE_SUBSCRIPTION_CANCELLATION'); ?></h1>

<div class="contentpanopen">

	<p>
		<?php
		
			if ($this->plan->cancel_msg) {
				echo $this->plan->cancel_msg;
			} else {
				echo $this->params->get('cancel_msg');
			}
			
		?>
	</p>

	<a href="<?php echo JRoute::_("index.php?option=com_jms&view=jms&Itemid=" . $Itemid); ?>">
		<?php echo $this->params->get('subscription_page_title'); ?>
	</a>

</div>	