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
        <h2 class="title-categories">
            Categories
        </h2>
    </div>
<?php foreach ($this->categories as $category) : ?>
        <div class="categories-box">
        <?php
        $registry = new JRegistry;
        $registry->loadString($category->images);
        $images = $registry->toArray();
        $category_image = $images["image_intro"];
        
        $registry = new JRegistry;
        $registry->loadString($category->attribs);
        $attribs = $registry->toArray();
        $show_description = $attribs["show_description"];
        ?>
            <img class="bg-image lfloat" src="<?php echo $category_image; ?>"/>
            <div class="rfloat info-category" >
                <a href="index.php?option=com_jms&view=jms&layout=category&id=<?php echo $category->id; ?>&number_of_columns=2&product_in_page=2&order=1" 
                   class="category-title"><?php echo $category->title; ?>
                </a>
                <?php if($category->short_description != "") echo $category->short_description; else echo "<p></p>" ?>
                <a href="index.php?option=com_jms&view=jms&layout=category&id=<?php echo $category->id; ?>&description=<?php echo $show_description; ?>"
                   class="btn cl-gray hcgray">Read more</a>
            </div>
        </div>
<?php endforeach; ?>
</div>