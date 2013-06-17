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

$model = $this->getModel();
if($user->id) $points = $model->getUserPointByUserId($user->id);

?>
<div id="wapper">
    <div id="content">
        <h2 class="title-categories">Order: <?php echo $this->product->title; ?></h2>
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
            if ($user->id)
            {
                ?>
                <div class="form clearfix">
                    <form action=" <?php echo JRoute::_('index.php?option=com_jms&view=jms&task=jms.order&id=' . $this->product->id); ?>" method="post" name="subscrform" id="subscrform" class="form-left lfloat clearfix">
                        <fieldset>
                            <h3>PAYMENT</h3>
                            <div class="group-input">
                                <label>Amount</label>
                                <p><?php echo"$" . $this->product->price . " (" . $this->product->price_points . " points)"; ?></p>
                            </div>
                            <div class="group-input">
                                <label>Payment Method</label>
                                <select class="check-box" name="jform[payment_method]" id="check_payment">
                                    <option value="iwl_paypal">Paypal</option>
                                    <option value="points">Points</option>
                                </select>
                            </div>
                            <div id="coupon" class="group-input">
                                <label>Coupon</label>
                                <input name="coupon" class="size185" type="text" />
                            </div>
                            <div id="points" style="display: none;" class="error-paypal">
                                <img alt="" src="components/com_jms/assets/images/icon-error.png"/>
                                <p>Your current point balance is:<span><?php if($user->id) echo $points->points; ?></span></p>
                                <p>You need <span><?php echo $this->product->price_points; ?></span> points to purchase this product</p>         
                            </div>
                            <input type="hidden" name="points" value="<?php echo $this->product->price_points; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $this->product->id; ?>">
                            <input type="hidden" name="user_point" value="<?php if($user->id) echo $points->points; ?>">
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
                        jQuery("#input_hidden").html('<input type="hidden" name="option" value="com_jms" /><input type="hidden" name="view" value="jms" /><input type="hidden" value="jms.buy_product" name="task" />');
                        jQuery('#system-message-container').css('display', 'none');
                    });

                    jQuery('#check_payment').change(function() 
                    {
                        if(jQuery('#check_payment').val()=='points')
                        {
                            jQuery('#coupon').css('display', 'none');
                            jQuery('#points').css('display', 'block');
                            jQuery("#input_hidden").html('');              
                        }
                        else
                        {
                            jQuery('#coupon').css('display', 'block');
                            jQuery('#points').css('display', 'none');
                            jQuery("#input_hidden").html('<input type="hidden" name="option" value="com_jms" /><input type="hidden" name="view" value="jms" /><input type="hidden" value="jms.buy_product" name="task" />');
                        }
                    });
                </script>
            <?php 
            } ?>
        </div>
    </div>
</div> 