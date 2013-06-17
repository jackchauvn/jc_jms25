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
JHTML::_('script', 'system/multiselect.js', false, true);
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_jms');
$saveOrder = $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_jms&view=vouchers'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">


            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
            </select>


        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                </th>

                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_JMS_VOUCHERS_TITLE_LABEL', 'a.title', $listDirn, $listOrder); ?>
                </th>
                
                <th width="15%">
                        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                        <?php if ($saveOrder) :?>
                            <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'articles.saveorder'); ?>
                        <?php endif; ?>
                </th>

                <?php if (isset($this->items[0]->state))
                { ?>
                    <th width="15%">
                        <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                <?php } ?>
                <?php if (isset($this->items[0]->id))
                { ?>
                    <th width="5%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="10">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            $originalOrders = array();
            foreach ($this->items as $i => $item) :
                $ordering = ($listOrder == 'a.ordering');
                $canCreate = $user->authorise('core.create', 'com_jms');
                $canEdit = $user->authorise('core.edit', 'com_jms');
                $canCheckin = $user->authorise('core.manage', 'com_jms');
                $canChange = $user->authorise('core.edit.state', 'com_jms');
                $voucherLink = JRoute::_('index.php?option=com_jms&task=voucher.edit&id=' . $item->id);
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>

                    <td class="left">
                        <a href="<?php echo $voucherLink; ?>" class="hasTip" title="<?php echo JText::_('COM_JMS_VOUCHERS_EDIT'); ?>::<?php echo $this->escape($item->title); ?>"><?php echo $item->title; ?></a><br>
                        <?php echo JText::_('COM_JMS_PLAN_TAG'); ?> 
                        <span class="jms_greentxt">
						<?php echo JText::_('{JMS-GIFT-VOUCHER ID=' . $item->id . '}'); ?>
                        </span>
                    </td>
                    
                    <td class="order">
                        <?php if ($canChange) : ?>
                            <?php if ($saveOrder) : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'vouchers.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'vouchers.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php endif; ?>
                            <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
                            <?php $originalOrders[] = $orderkey + 1; ?>
                        <?php else : ?>
                            <?php echo $orderkey + 1;?>
                        <?php endif; ?>
                    </td>

                    <?php if (isset($this->items[0]->state))
                    { ?>
                        <td class="center">
                            <?php echo JHtml::_('jgrid.published', $item->state, $i, 'vouchers.', $canChange, 'cb'); ?>
                        </td>
                    <?php } ?>
                    <?php if (isset($this->items[0]->id))
                    { ?>
                        <td class="center">
                            <?php echo (int) $item->id; ?>
                        </td>
                    <?php } ?>
                </tr>
<?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>