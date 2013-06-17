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

JHtml::_('behavior.tooltip');

$select = array();
$select[0]->value = 0;
$select[0]->text = "All";
$select[1]->value = 1;
$select[1]->text = "Product title";
$select[2]->value = 2;
$select[2]->text = "Item no.";
$select[3]->value = 3;
$select[3]->text = "Tags";
?>

<form action="<?php echo JRoute::_('index.php?option=com_jms&view=sales'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
            <select name="filter_select" class="inputbox" onchange="this.form.submit()">
                <?php echo JHtml::_('select.options', $select, "value", "text", $this->state->get('filter.select'), true); ?>
            </select>
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        
        <div class="filter-date fltlft">
            
            <label class="filter-date-lbl" for="filter_startdate"><?php echo JText::_('JSTART_DATE_LABEL'); ?></label>
            <input placeholder="Year/Month/Day" type="text" name="filter_startdate" id="filter_startdate" value="<?php echo $this->escape($this->state->get('filter.startdate')); ?>" title="<?php echo JText::_('Start date'); ?>" />
            <label class="filter-date-lbl" for="filter_enddate"><?php echo JText::_('JSEND_DATE_LABEL'); ?></label>
            <input placeholder="Year/Month/Day" type="text" name="filter_enddate" id="filter_enddate" value="<?php echo $this->escape($this->state->get('filter.enddate')); ?>" title="<?php echo JText::_('End date'); ?>" />
            
        </div>
    </fieldset>
    
    <div class="clr"> </div>
	
	<?php
            foreach ($this->items as $i => $item) :
             $price =$price + $item->price;
            endforeach; 
    ?>
    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_DATE', 'a.created', null, null); ?>
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_PRICE', 'a.price', null, null); ?>
					<a class="amount"><?php echo"$price" ?></a>
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_PRICE_POINTS', 'a.price_points', null, null); ?>
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_PRODUCT', 'a.title', null, null); ?>
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_FULLNAME', 'a.fullname', null, null); ?>
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_SALES_EMAIL', 'a.email', null, null); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            foreach ($this->items as $i => $item) :
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>

                    <td class="left">
                        <?php echo date("d/m/Y", strtotime($item->created)); ?>
                    </td>

                    <td class="center">
                        <?php echo "$".$item->price; ?>
                    </td>

                    <td class="center">
                    <?php echo $item->price_points; ?>
                    </td>

                    <td class="center">
                    <?php echo $item->title; ?>
                    </td>

                    <td class="center">
                    <?php echo $item->fullname; ?>
                    </td>

                    <td class="center">
                    <?php echo $item->email; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>