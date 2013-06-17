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
?>
<div id="wapper">
    <div id="page-waper">
        <div class="my-sub lfloat">
            <span style="font-size: 12px; font-weight: bold;">MY SUBSCRIPTIONS</span>
            <div class="group-money ">
                <p><?php echo $this->plan_useid->name;?>: </p>
                <div style="float: left; margin-left: 10px;"class="monthly">
                    <h4>$ <?php echo $this->plan_useid->price;?></h4>
                    <strong>or <span> 0 points</span> per month</strong>
                </div>
            </div>
        </div>
        <div class="my-credit lfloat">
            <span style="font-size: 12px; font-weight: bold;">MY CREDITS</span>
            <div class="group-money-2">
                <img alt="" src="components/com_jms/assets/images/money.png"/>
                <p>Current balance is: <span><?php echo $this->point_useid->points;?> points<span></p>
            </div>
        </div>
    </div>
    <h4 class="my-download clear">MY DOWNLOADS</h4>
    <div id="wapper-product">
        <div id="content-left">
            <?php
            if ($this->products) 
            {
                foreach ($this->products as $product) 
                {
                    ?>
                    <div class="categories-box ct-top">
                    <?php
                    $registry      = new JRegistry;
                    $registry->loadString($product->images);
                    $product_image = $registry->toArray();
                    ?>
                        <img class="bg-image lfloat" src="<?php echo $product_image[image_intro]; ?>"/>
                        <div class="rfloat info-category" >
                            <a href="index.php?option=com_jms&view=jms&layout=productdetails&id=<?php echo $product->id; ?>" class="category-title">
                                <?php echo $product->title; ?>
                            </a>
                            <p><?php echo substr($product->description, 0, 150); ?></p>
                            <?php
                            $model         = $this->getModel();
                            $user          = JFactory::getUser();
                            if ($user->id) 
                            {
                                $is_buy    = $model->isBuyByProductIdAndUserId($product->id, $user->id);
                                $downloads = $model->getDownloadsByProductId($product->id);

                                if ($is_buy) 
                                {
                                    if ($downloads) 
                                    {
                                        if(count($downloads) > 1)
                                        {
                                        ?>
                                            <a id="<?php echo $product->id; ?>" href="#<?php echo $product->id; ?>_dialog" class="btn lfloat hcgray cl-green">
                                                Download
                                            </a>
                                            <div style="display :none;">
                                                <div id="<?php echo $product->id; ?>_dialog" style="width: 100%; overflow: auto;">
                                                    <div style="width: 100%;">
                                                        <div class="content">
                                                            <ul class="list-download">
                                                            <?php
                                                            foreach ($downloads as $download) 
                                                            {
                                                                ?>
                                                                <li><?php echo $download->title; ?>
                                                                    <a href="index.php?option=com_jms&view=jms&task=jms.download&id=<?php echo $download->id; ?>" class="btn rfloat hcgray cl-green">
                                                                        Download
                                                                    </a>
                                                                </li>
                                                                <?php
                                                            }
                                                            ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                                jQuery("#<?php echo $product->id; ?>").fancybox({
                                                    'title': "Download: <?php echo $product->title; ?>",
                                                    'transitionIn': 'elastic',
                                                    'transitionOut': 'elastic',
                                                    'titleShow': true,
                                                    'titlePosition': 'over',
                                                    'onComplete': function() { jQuery("#fancybox-title").css({'top':'0px', 'bottom':'auto'}); } 
                                                });
                                            </script>
                                        <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <a href="index.php?option=com_jms&view=jms&task=jms.download&id=<?php echo $downloads[0]->id; ?>" class="btn lfloat hcgray cl-green">
                                                Download
                                            </a>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                            <a href="index.php?option=com_jms&view=jms&layout=productdetails&id=<?php echo $product->id; ?>" class="btn cl-gray-detail rfloat hcgray">
                                More Details
                            </a>
                        </div>
                    </div> 
                    <?php
                }
            }
            ?>
        </div>    
    </div>
</div>

<style>
    h3, h4 { letter-spacing: 0px !important; }
    .my-credit p span { font-weight: bold;}
    #content-left { width: 100%; }
    #system-message-container { display: none; }
</style>