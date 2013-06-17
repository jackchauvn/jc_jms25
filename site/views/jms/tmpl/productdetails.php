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
    <div id="content">
        <h2 class="title-categories"><?php echo $this->product->title; ?></h2>        
        <div>
            <div class="product-box  lfloat">
                <div class="space">
                    <img class="bg-image rt-image" style="width: 200px!important; height: 300px!important;" alt="" id="img<?php echo $this->product->id; ?>" src="<?php echo $this->product_image; ?>"/>
                    <div style="padding: 5px 55px;">
                <?php
                $model = $this->getModel();
                $user = JFactory::getUser();
                if ($user->id) 
                {
                    $is_buy    = $model->isBuyByProductIdAndUserId($this->product->id, $user->id);
                    $downloads = $model->getDownloadsByProductId($this->product->id);

                    if ($is_buy) 
                    {
                        if($this->fade_purchased_products == 1)
                        {
                        ?>
                        <script>
                            jQuery("#img<?php echo $this->product->id; ?>").css({"opacity":"0.5"})
                        </script>
                        <?php
                        }
                        if ($downloads) 
                        {
                            if(count($downloads) > 1)
                            {
                            ?>
                                <a id="<?php echo $this->product->id; ?>" href="#<?php echo $this->product->id; ?>_dialog" class="btn lfloat hcgray cl-green">
                                    Download
                                </a>
                                <div style="display :none;">
                                    <div id="<?php echo $this->product->id; ?>_dialog" style="width: 100%; overflow: auto;">
                                        <div style="width: 100%;">
                                            <div class="content">
                                                <ul class="list-download">
                                                <?php
                                                foreach ($downloads as $download) {
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
                                    jQuery("#<?php echo $this->product->id; ?>").fancybox({
                                        'title': "Download: <?php echo addslashes($this->product->title); ?>",
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
                    else {
                        ?>
                            <a href="index.php?option=com_jms&view=jms&layout=order&id=<?php echo $this->product->id; ?>" class="btn cl-blue hcgray lfloat">
                                Buy now
                            </a>
                        <?php
                    }
                }
                else
                {
                    $uri = JFactory::getURI();
                    $url = $uri->toString(array('path', 'query', 'fragment'));
                    $session = JFactory::getSession();
                    if ($session->get('jms_access_url')) 
                        {
                            $url = $session->get('jms_access_url'); 
                        } 
                    $url = base64_encode($url);
                    ?>
                    <a href="index.php?option=com_jms&view=jms&layout=order&id=<?php echo $this->product->id; ?>" class="btn cl-blue hcgray rt-btn">
                        Buy now
                    </a>
                    <?php
                }
                   ?>
                    </div>
                </div>
            </div>
            <div class="description">
                <?php echo $this->product->description; ?>
            </div>
        </div>    
    </div>
</div>
<style>
    .cl-blue {
	   margin: 0px;
	}
</style>