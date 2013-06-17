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
        <h2 class="title-categories"><?php echo $this->category->title; ?></h2>        
        <div>
            <div><?php if ($_GET['description']) echo $this->category->description; ?></div>
            <?php
            if ($this->products) 
            {
                $count = 0;
                foreach ($this->products as $product) 
                {
                    $count++;
                    ?>
                    <div class="categories-box ct-top">
                    <?php
                    $registry      = new JRegistry;
                    $registry->loadString($product->images);
                    $product_image = $registry->toArray();
                    ?>
                        <img class="bg-image lfloat" id="img<?php echo $product->id; ?>" src="<?php echo $product_image[image_intro]; ?>"/>
                        <div class="rfloat info-category" >
                            <a href="index.php?option=com_jms&view=jms&layout=productdetails&id=<?php echo $product->id; ?>" class="category-title">
                                <?php echo $product->title; ?>
                            </a>
                            <?php if($product->short_description != "") echo $product->short_description; else echo "<p></p>" ?>
                            <?php
                            $model         = $this->getModel();
                            $user          = JFactory::getUser();
                            if ($user->id) 
                            {
                                $is_buy    = $model->isBuyByProductIdAndUserId($product->id, $user->id);
                                $downloads = $model->getDownloadsByProductId($product->id);

                                if ($is_buy) 
                                {
                                    if($this->fade_purchased_products == 1)
                                    {
                                    ?>
                                    <script>
                                        jQuery("#img<?php echo $product->id; ?>").css({"opacity":"0.5"})
                                    </script>
                                    <?php
                                    }
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
                                else 
                                {
                                    ?>
                                        <a href="index.php?option=com_jms&view=jms&layout=order&id=<?php echo $product->id; ?>" class="btn cl-blue hcgray lfloat">
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
                                <a href="index.php?option=com_jms&view=jms&layout=order&id=<?php echo $product->id; ?>&url=<?php echo $url;?>" class="btn cl-blue hcgray lfloat">
                                    Buy now
                                </a>
                                <?php
                            }
                            ?>
                            <a href="index.php?option=com_jms&view=jms&layout=productdetails&id=<?php echo $product->id; ?>" class="btn cl-gray-detail rfloat hcgray">
                                More Details
                            </a>
                        </div>
                    </div> 
                    <?php
                    if($count >= $this->attribs['number_of_columns'] && $count % $this->attribs['number_of_columns'] == 0)
                    {
                        echo "<div style='clear:both'></div>";
                    }
                }
            }
            ?>
			<div class="pagenav">
				<?php
				$pageNav = new JPagination($this->count, $this->limitstart, $this->attribs["number_of_products"]);
				echo $pageNav->getPagesLinks();
				?>
			</div>
        </div>    
    </div>
</div>