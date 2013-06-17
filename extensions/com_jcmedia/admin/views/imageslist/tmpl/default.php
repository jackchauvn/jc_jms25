<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_jcmedia
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<?php if (count($this->images) > 0 || count($this->folders) > 0) { ?>
<div class="manager">

		<?php for ($i=0, $n=count($this->folders); $i<$n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>
    
                <?php for ($i=0, $n=count($this->documents); $i<$n; $i++) :
			$this->setDoc($i);
			echo $this->loadTemplate('doc');
		endfor; ?>

		<?php for ($i=0, $n=count($this->images); $i<$n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('image');
		endfor; ?>
    
</div>
<?php } else { ?>
	<div id="media-noimages">
		<p><?php echo JText::_('COM_MEDIA_NO_IMAGES_FOUND'); ?></p>
	</div>
<?php } ?>

<style>
    div.imgOutline {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        border-bottom-color: #CCCCCC;
        border-bottom-style: solid;
        border-bottom-width: 1px;
        border-image-outset: 0 0 0 0;
        border-image-repeat: stretch stretch;
        border-image-slice: 100% 100% 100% 100%;
        border-image-source: none;
        border-image-width: 1 1 1 1;
        border-left-color-ltr-source: physical;
        border-left-color-rtl-source: physical;
        border-left-color-value: -moz-use-text-color;
        border-left-style-ltr-source: physical;
        border-left-style-rtl-source: physical;
        border-left-style-value: none;
        border-left-width-ltr-source: physical;
        border-left-width-rtl-source: physical;
        border-left-width-value: 1px;
        border-right-color-ltr-source: physical;
        border-right-color-rtl-source: physical;
        border-right-color-value: #F0F0F0;
        border-right-style-ltr-source: physical;
        border-right-style-rtl-source: physical;
        border-right-style-value: solid;
        border-right-width-ltr-source: physical;
        border-right-width-rtl-source: physical;
        border-right-width-value: 1px;
        border-top-color: -moz-use-text-color;
        border-top-style: none;
        border-top-width: 1px;
        float: left;
        width: 90px;
    }


    div.imgBorder {
        height: 72px;
        overflow-x: hidden;
        overflow-y: hidden;
        vertical-align: middle;
        width: 88px;
    }
</style>
