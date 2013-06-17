<?php
session_start();
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

$model = $this->getModel();
if($user->id) $points = $model->getUserPointByUserId($user->id);
$sender_name=$_POST['sender_name'];
$sender_email=$_POST['sender_email'];
$receiver_name=$_POST['receiver_name'];
$receiver_email=$_POST['receiver_email'];
$message=$_POST['message'];
$id_cuopon=$_POST['id_coupon'];
$id_vuocher=$_GET['id'];

$_SESSION['id_coupon'] = $id_cuopon;
$_SESSION['id_vuocher'] = $id_vuocher;
$_SESSION['sender_name'] = $sender_name;
$_SESSION['sender_email'] = $sender_email;
$_SESSION['receiver_name'] = $receiver_name;
$_SESSION['receiver_email'] = $receiver_email;
$_SESSION['message'] = $message;
$_SESSION['voucher_id']= $this->voucher->id;

?>
<div id="wapper">
    <div id="content">
        <h2 class="title-categories">Order: <?php echo $this->voucher->title; ?></h2>
        <div id="content-right" class="lfloat prtop">
            <div class="categories-box-detail">
                <img class="bg-image lfloat" alt="" src=""/>
                <div class="lfloat info-category-detail">
                    <a href="" class="category-title"><?php echo $this->voucher->title; ?></a>
                    <p><?php echo $this->voucher->message; ?></p>
                </div>
            </div>
            <?php
            if ($user->id)
            {
                ?>
                <div class="form clearfix">
                    <form action=" <?php echo JRoute::_('index.php?option=com_jms&view=jms&task=jms.ordervoucher&id=' . $this->voucher->id); ?>" method="post" name="subscrform" id="subscrform" class="form-left lfloat clearfix">
                        <fieldset>
                            <h3>PAYMENT</h3>
                            <div class="group-input">
                                <label>Amount</label>
                                <p><?php echo"$" . $this->voucher->price . " (" . $this->voucher->price_points . " points)"; ?></p>
                            </div>
                            <div class="group-input">
                                <label>Payment Method</label>
                                <select class="check-box" name="jform[payment_method]" id="check_payment">
                                    <option value="iwl_paypal">Paypal</option>
                                    <option value="points">Points</option>
                                </select>
                            </div>
                            <div id="points" style="display: none;" class="error-paypal">
                                <img alt="" src="components/com_jms/assets/images/icon-error.png"/>
                                <p>Your current point balance is:<span><?php if($user->id) echo $points->points; ?></span></p>
                                <p>You need <span><?php echo $this->voucher->price_points; ?></span> points to purchase this product</p>         
                            </div>
                            <input type="hidden" name="voucher_id" value="<?php echo $this->voucher->id; ?>">
                            <input type="hidden" id="hpoints" value="<?php echo $points->points; ?>">
                            <input type="hidden" id="hprice_points" value="<?php echo $this->voucher->price_points; ?>">
                            <button class="btn cl-gray btn-span" style="margin-left: 120px; margin-top: 10px" type="submit"><span>Pay Now</span></button>
                            <div id="input_hidden"></div>

                            <?php if ($this->params->get('mc_enable')) 
                                { ?>
                                    <!-- mailchimp -->
                                    <input type="hidden" name="mc_api_key" value="<?php echo $this->params->get('mc_api_key'); ?>" />
                                    <input type="hidden" name="mc_listid" value="<?php echo $this->params->get('mc_listid'); ?>" />
                                    <input type="hidden" name="mc_groupid" value="<?php echo $this->params->get('mc_groupid'); ?>" />
                                    <!-- /mailchimp -->
                                <?php 
                                } ?>

                        </fieldset>
                    </form>
                </div>
                <script type="text/javascript">                    
                    jQuery(document).ready(function($){
                        jQuery("#input_hidden").html('<input type="hidden" name="option" value="com_jms" /><input type="hidden" name="view" value="jms" /><input type="hidden" value="jms.buy_voucher" name="task" />');
                        jQuery('#system-message-container').css('display', 'none');
                    });

                    jQuery('#check_payment').change(function() 
                    {
                        if(jQuery('#check_payment').val()=='points')
                        {
                            jQuery('#points').css('display', 'block');
                            jQuery("#input_hidden").html('');              
                        }
                        else
                        {
                            jQuery('#points').css('display', 'none');
                            jQuery("#input_hidden").html('<input type="hidden" name="option" value="com_jms" /><input type="hidden" name="view" value="jms" /><input type="hidden" value="jms.buy_voucher" name="task" />');
                        }
                    });
                    
                    jQuery('#subscrform').submit(function() {
                        if(parseInt(jQuery('#hprice_points').val()) > parseInt(jQuery('#hpoints').val()) && jQuery('#check_payment').val()=='points')
                        {
                            alert("You don't have enough current point balance!");
                            return false;
                        }
                    });
                </script>
            <?php 
            } ?>
        </div>
    </div>
</div> 