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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
jimport('joomla.html.pagination');

$user = JFactory::getUser();

?>
<div id="wapper">
    <div id="content">
        <h2 class="title-categories">Order: <?php echo $this->product->title; ?></h2>
        <div id="sidebar" class="lfloat lf-menu">
            <ul><?php foreach ($this->categories as $category)
                {
                    ?>
                        <li <?php if ($category->id == 0) echo "class='active'"; ?>>
                            <a href="index.php?option=com_jms&view=jms&layout=category&id=<?php echo $category->id; ?>">
                                <?php echo $category->title; ?>
                            </a>
                        </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <div id="content-right" class="lfloat prtop">
            <div class="categories-box-detail">
                <img class="bg-image lfloat" alt="" src="<?php echo $this->product_image; ?>"/>
                <div class="lfloat info-category-detail">
                    <a href="" class="category-title"><?php echo $this->product->title; ?></a>
                    <p><?php echo $this->product->description; ?></p>
                        <a  href="index.php?option=com_jms&view=jms&layout=productdetails&id=<?php echo $this->product->id; ?>" class="btn lfloat hcgray cl-gray-go">
                            Go Back To Product
                        </a>
                </div>
            </div>
            <?php 
            if (!$user->id)
            {
                $document = Jfactory::getDocument();
            ?>
                <script language="javascript" type="text/javascript">	
                    if (typeof jQuery == 'undefined') 
                    { 
                        <?php $document->addScript('components/com_jms/assets/jquery.js'); ?>
                    }

                    jQuery(document).ready(function($) {

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

                        $('button.validate').click(function() {

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

                <h1><?php echo $this->params->get('login_form_title'); ?></h1>
                <p><?php echo $this->params->get('login_form_text'); ?></p>

                <div class="registration<?php echo $this->pageclass_sfx ?>">

                    <div class="jms40 floatleft">

                        <?php if ($this->type == 'logout') : ?>

                            <?php
                            if ($this->name != '')
                            {
                                echo JText::sprintf('MOD_LOGIN_HINAME', $this->name);
                            }
                            else
                            {
                                echo JText::sprintf('MOD_LOGIN_HINAME', $this->username);
                            }
                            ?>
                            <form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="login-form" >
                                <div class="logout-button">
                                    <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
                                    <input type="hidden" name="option" value="com_users" />
                                    <input type="hidden" name="task" value="user.logout" />
                                    <input type="hidden" name="return" value="<?php echo $this->url; ?>" />
                                    <?php echo JHtml::_('form.token'); ?>
                                </div>
                            </form>

                        <?php else : ?>

                            <form action="<?php echo JRoute::_('index.php?option=com_jms'); ?>" method="post" id="jms-login-form" class="form-validate jms-form" >
                                <fieldset name="login">
                                    <legend><?php echo JText::_('COM_JMS_LOGIN'); ?></legend>

                                    <dl>
                                        <dt><label for="modlgn-username">User Name</label></dt>
                                        <dd><input type="text" name="username" class="inputbox required"  size="30" /></dd>

                                        <dt><label for="modlgn-passwd">Password</label></dt>
                                        <dd><input type="password" name="password" class="inputbox required" size="30"  /></dd>

                                        <dt></dt>
                                        <dd><input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" /></dd>
                                    </dl>

                                    <div class="clr"></div>

                                    <ul>
                                        <li>
                                            <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                                                <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
                                                <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
                                        </li>
                                    </ul>

                                    <input type="hidden" name="option" value="com_users" />
                                    <input type="hidden" name="task" value="user.login" />
                                    <input type="hidden" name="return" value="<?php echo $this->url; ?>" />
                                    <?php echo JHtml::_('form.token'); ?>
                                </fieldset>
                            </form>

                        <?php endif; ?>
                    </div>

                    <div class="jms60 floatright">
                        <form action="index.php" method="post" id="jms-register-form" class="form-validate jms-form">
                            <fieldset name="register">
                                <legend><?php echo JText::_('COM_JMS_REGISTER'); ?></legend>
                                <dl>

                                    <dt><?php echo $this->form->getLabel('name'); ?></dt>
                                    <dd><?php echo $this->form->getInput('name'); ?></dd>

                                    <dt><?php echo $this->form->getLabel('username'); ?></dt>
                                    <dd><?php echo $this->form->getInput('username'); ?></dd>

                                    <dt><?php echo $this->form->getLabel('email1'); ?></dt>
                                    <dd><?php echo $this->form->getInput('email1'); ?></dd>

                                    <dt><?php echo $this->form->getLabel('email2'); ?></dt>
                                    <dd><?php echo $this->form->getInput('email2'); ?></dd>

                                    <dt><?php echo $this->form->getLabel('password1'); ?></dt>
                                    <dd><?php echo $this->form->getInput('password1'); ?></dd>

                                    <dt><?php echo $this->form->getLabel('password2'); ?></dt>
                                    <dd><?php echo $this->form->getInput('password2'); ?></dd>

                                </dl>
                            </fieldset>

                            <div class="clr"></div>

                            <?php
                            if ($this->params->get('show_available_plans_to_guest'))
                            {
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
                                <?php
                            }
                            ?>

                            <?php if ($this->params->get('terms_conditions')) : ?>
                                <fieldset name="terms">
                                    <legend><?php echo JText::_('COM_JMS_TERMS'); ?></legend>
                                    <p>
                                        <input type="checkbox" name="accept_terms" id="accept_terms" class="inputbox" /> <?php echo JText::_('COM_JMS_AGREE_PRE_TEXT'); ?> <a href="#jms-box" class="jms-window"><?php echo $this->params->get('terms_link'); ?></a>
                                        <span class="label_error invalid"><br /><?php echo JText::_('COM_JMS_AGREE_TERMS_ERROR'); ?> <?php echo $this->params->get('terms_link'); ?></span>
                                    </p>
                                </fieldset>
                            <?php endif; ?>

                            <div>
                                <p><?php echo JText::_('COM_JMS_FIELDS_REQUIRED'); ?></p>
                                <button type="submit" class="validate"><?php echo JText::_('COM_JMS_REGISTER_BUTTON'); ?></button>
                                <?php echo JText::_('COM_JMS_OR'); ?>
                                <a href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('COM_JMS_CANCEL_BUTTON'); ?>"><?php echo JText::_('COM_JMS_CANCEL_BUTTON'); ?></a>
                                <input type="hidden" name="option" value="com_jms" />
                                <input type="hidden" name="task" value="form.register" />
                                <?php echo JHtml::_('form.token'); ?>
                            </div>

                        </form>
                    </div>

                    <div class="clr"></div>

                </div>

                <div style="display: none; margin-top: -113px; margin-left: -130px;" id="jms-box" class="jms-popup">

                    <a href="#" class="jms-close"><img src="components/com_jms/assets/images/close_pop.png" class="btn_close" title="Close Window" alt="Close"></a>

                    <div id="jms-external"></div>

                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            $('#jms-external').load('index.php?option=com_content&view=article&id=<?php echo $this->params->get('terms_conditions'); ?>&Itemid=<?php echo JRequest::getInt('Itemid'); ?>&tmpl=component');
                        });
                    </script>

                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div> 