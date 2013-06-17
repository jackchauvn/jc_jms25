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

<h2 class="jms_h2"><?php echo JText::_( 'COM_JMS_TITLE_ABOUT' ); ?> <?php echo JText::_( 'COM_JMS' ); ?></h2>
<p class="jms_pdesc"><?php echo JText::_( 'COM_JMS_COMPONENT_DESC' ); ?></p>

<ul class="adminaboutlist">
            
    <li>
        <label><?php echo JText::_( 'COM_JMS_ABT_VERSION' ); ?>:</label>
        <?php echo JText::_( '2.5-2.2.0' ); ?>
    </li>
    
    <li>
        <label><?php echo JText::_( 'COM_JMS_ABT_AUTHOR' ); ?>:</label>
        <?php echo JText::_( 'Infoweblink' ); ?>
    </li>
    
    <li>
		<label><?php echo JText::_( 'COM_JMS_ABT_SUPPORTEMAIL' ); ?>:</label>
		<a href="mailto: <?php echo JText::_( 'support@infoweblink.com' ); ?>">
			<?php echo JText::_( 'support@infoweblink.com' ); ?>
		</a>	
    </li>
    
    <li>
		<label><?php echo JText::_( 'COM_JMS_ABT_HOMEPAGE' ); ?>:</label>
		<a href="<?php echo JText::_( 'http://www.joomlamadesimple.com/' ); ?>" target="_blank">
			<?php echo JText::_( 'http://www.joomlamadesimple.com/' ); ?>
		</a>	
    </li>
    
    <li>
		<label><?php echo JText::_( 'COM_JMS_ABT_COPYRIGHT' ); ?>:</label>
		<?php echo JText::_( 'Copyright &copy; 2013 Infoweblink' ); ?>
    </li>
                    
</ul>		

</div>

<div class="cpanel-right" align="right">
<img src="components/com_jms/assets/images/jms300.png" alt="<?php echo JText::_( 'COM_JMS' ); ?>" align="middle" border="0" />
</div>

<div class="clr"></div>

</div>

			
