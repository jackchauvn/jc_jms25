<?php
/* -------------------------------------------------------------------------------
  # plg_jmsloadsubscription - JMS Membership Sites Plugin
  # -------------------------------------------------------------------------------
  # author    			Infoweblink
  # copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
  # @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: 			http://www.joomlamadesimple.com/
  # Technical Support:  	http://www.joomlamadesimple.com/forums
  --------------------------------------------------------------------------------- */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Content Load Subscription Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentJmsloadsubscription extends JPlugin
{

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param object $params  The object that holds the plugin parameters
     * @since 1.5
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);

        // Add style css		
        $document = JFactory::getDocument();
        JHTML::stylesheet('components/com_jms/assets/jms.css');

        // Load the translation
        $this->loadLanguage();
    }

    /**
     * Prepare content method
     *
     * Method is called by the view
     *
     * @param 	object		The article object.  Note $article->text is also available
     * @param 	object		The article params
     * @param 	int			The 'page' number
     */
    function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        $mainframe = JFactory::getApplication();
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        
        // Simple performance check to determine whether the plugin should process further
        if (JString::strpos($row->text, 'JMS-SUBSCRIPTION ID') === false)
        {
            return true;
        }
        if (preg_match("/{JMS-SUBSCRIPTION ID=([\d,]+)}/iU", $row->text, $matches))
        {
            $matches = $matches[1];
            if ($matches == '')
            {
                $subscriptions = '';
            }
            else
            {
                ob_start();
                $this->_showSubOptions($matches);
                $subscriptions = ob_get_contents();
                ob_end_clean();
            }
            $row->text = preg_replace("/{JMS-SUBSCRIPTION ID=[^}]+}/iU", $subscriptions, $row->text);
        }
    }

    /**
     * Private method to show subscriptions options
     *
     * @param string $matches
     */
    function _showSubOptions($matches)
    {
        // Get DB connector
        $db = JFactory::getDBO();

        // Get the component config/params object.
        $config = JComponentHelper::getParams('com_jms');
        $subscribeBtnImg = $this->params->get('subscribe_btn_img', 'components/com_jms/assets/images/subscribe.gif');

        // Get user
        $user = JFactory::getUser();

        // Get subscritpions to show
        $sql = 'SELECT * FROM #__jms_plans' .
                ' WHERE id IN (' . trim($matches) . ')' .
                ' AND state = 1' .
                ' ORDER BY ordering'
        ;
        $db->setQuery($sql);
        $rows = $db->loadObjectList();

        $paymentMethodsArr = $config->get('payment_method');
        $defaultpaymentMethod = JRequest::getVar('payment_method', 'iwl_paypal');

        if (count($rows))
        {
            ?>
            <script language="javascript" type="text/javascript">
                // Method to process changing payment method
                var numberOfInstallments = new Array();
                var subscriptionType = new Array();
            <?php
            foreach ($rows as $i => $item)
            {
                ?>
                                            numberOfInstallments[<?php echo $i; ?>] = <?php echo $item->number_of_installments; ?>;
                                            subscriptionType[<?php echo $i; ?>] = '<?php echo $item->plan_type; ?>';
                <?php
            }
            ?>
                        function changePlanParameters() {
                            var form = document.subscrform;
                            var sid = document.getElementsByName('sid');
                            for (var i = 0; i < sid.length; i++) {
                                if (sid[i].checked == true) {
                                    break;
                                }
                            }
                            form.r_times.value = numberOfInstallments[i];
                            form.subscription_type.value = subscriptionType[i];
                            if (form.subscription_type.value == 'I') {
                                form.task.value = 'jms.process_subscription';
                            } else {
                                form.task.value = 'jms.process_recurring_subscription';
                            }
                        }
            				
                        // Method to process changing payment method
                        function changePaymentMethod() {
                            var form = document.subscrform;
                            var paymentMethod;
                            for (var i = 0; i < form.payment_method.length; i++) {
                                if (form.payment_method[i].checked == true) {
                                    paymentMethod = form.payment_method[i].value ;
                                    break;
                                }
                            }						
                            var trCardInfo = document.getElementById('card_info');
                            var trEwayCardInfo = document.getElementById('eway_card_info');
                            if (paymentMethod == 'iwl_authnet') {
                                trCardInfo.style.display = '';
                                trEwayCardInfo.style.display = 'none';
                            } else if (paymentMethod == 'iwl_eway') {
                                trCardInfo.style.display = 'none';
                                trEwayCardInfo.style.display = '';
                            } else {
                                trCardInfo.style.display = 'none';
                            }
                        }
            				
                        // Method to check number
                        function checkNumber(txtName)
                        {			
                            var num = txtName.value			
                            if(isNaN(num)) {			
                                alert("<?php echo JText::_('JMS_AUTH_ONLY_NUMBER'); ?>");			
                                txtName.value = "";			
                                txtName.focus();
                            }			
                        }
            				
                        function processSubscription() {
                            var form = document.subscrform;								
                            // Authorize.net validate
                            var paymentMethod = "";
            <?php
            if (count($paymentMethodsArr) > 1)
            {
                ?>
                                                for (var i = 0 ; i < form.payment_method.length; i++) {
                                                    if (form.payment_method[i].checked == true) {
                                                        paymentMethod = form.payment_method[i].value;
                                                        break;
                                                    }
                                                }
                <?php
            }
            else
            {
                ?>
                                                paymentMethod = form.payment_method.value;
                <?php
            }
            ?>
                                    if (paymentMethod == "iwl_authnet") {
                                        if (form.x_card_num.value == "") {
                                            alert('<?php echo JText::_('JMS_AUTH_ENTER_CARD_NUMBER'); ?>');
                                            form.x_card_num.focus();
                                            return;
                                        }
                                        if (form.x_exp_date.value == "") {
                                            alert('<?php echo JText::_('JMS_AUTH_ENTER_EXP_DATE'); ?>');
                                            form.x_exp_date.focus();
                                            return ;					
                                        }
                                        if (form.x_card_code.value == "") {
                                            alert('<?php echo JText::_("JMS_AUTH_ENTER_CARD_CODE"); ?>');
                                            form.x_card_code.focus();
                                            return ;
                                        }
                                    }
                                    form.submit();		
                                }
            </script>
            <form action="index.php" method="post" name="subscrform" id="subscrform">
                <p><?php echo $config->get('subscription_page_text'); ?></p>
                <table class="category" cellpadding="1" cellspacing="0" border="0" width="100%">
                    <tr>
                        <th class="sectiontableheader" width="1%" align="center"><?php echo JText::_('JMS_RADIO_HEAD'); ?></th>
                        <th class="sectiontableheader" width="1%" align="center"><?php echo JText::_('JMS_PERIOD_HEAD'); ?></th>
                        <th class="sectiontableheader"><?php echo JText::_('JMS_SUBSCRIPTION_NAME_HEAD'); ?></td>
                        <th class="sectiontableheader" width="20%" align="center"><?php echo JText::_('JMS_PRICE_HEAD'); ?></th>
                    </tr>
                            <?php
                            foreach ($rows as $i => $item) :
                                if ($i == 0)
                                {
                                    ?>
                            <tr>
                                <td colspan="4">
                                    <input type="hidden" name="r_times" id="r_times" value="<?php echo $item->number_of_installments; ?>" />
                                    <input type="hidden" name="subscription_type" id="subscription_type" value="<?php echo $item->plan_type ?>" />
                                </td>
                            </tr>
                                <?php } ?>
                        <tr class="cat-list-row<?php echo $i % 2; ?>" valign="top">
                            <td align="center">
                                <input id="sid<?php echo ($i + 1); ?>" type="radio" <?php if ($i == 0) echo 'checked'; ?> value="<?php echo $item->id; ?>" name="sid" onclick="changePlanParameters();" />
                            </td>			    						
                            <td align="center" nowrap>
                                <?php
                                $periodType = $item->period_type;
                                switch ($periodType)
                                {
                                    case 1:
                                        echo $item->period . ' ' . JText::_('JMS_DAYS');
                                        break;
                                    case 2:
                                        echo $item->period . ' ' . JText::_('JMS_WEEKS');
                                        break;
                                    case 3:
                                        echo $item->period . ' ' . JText::_('JMS_MONTHS');
                                        break;
                                    case 4:
                                        echo $item->period . ' ' . JText::_('JMS_YEARS');
                                        break;
                                    case 5:
                                        echo JText::_('JMS_UNLIMITED');
                                        break;
                                    default:
                                        echo $item->period . ' ' . JText::_('');
                                }
                                ?>
                            </td>
                            <td>
                                <strong><label for="sid<?php echo ($i + 1); ?>"><?php echo $item->name; ?></label></strong><br />
                                <p><?php echo $item->description; ?></p>
                            </td>
                            <td align="center">
                        <?php
                        if ($item->discount > 0)
                        {
                            $discountedPrice = round(($item->price - ($item->price * ($item->discount / 100))), 2);
                            echo JText::_('JMS_SETUP_PRICE') . ' <strong>\\' . $config->get('currency_sign') . $item->price . '</strong><br />';
                            echo JText::_('JMS_DISCOUNT_PRICE') . ' <strong>\\' . $config->get('currency_sign') . $discountedPrice . '</strong> ';
                        }
                        else
                        {
                            echo '<strong>\\' . $config->get('currency_sign', '$') . $item->price . '</strong>';
                        }
                        ?>
                            </td>
                        </tr>
                            <?php endforeach; ?>
                </table>
                <p><?php echo JText::_('JMS_LAYOUT_COUPON_TEXT'); ?>
                    <input type="text" class="inputbox" name="coupon" id="coupon" size="40" value="<?php echo JRequest::getVar('coupon'); ?>" /></p>
                <table cellpadding="5" cellspacing="5" border="0" width="100%" class="jms button-table category">
                    <?php
                    if (count($paymentMethodsArr) > 1)
                    {
                        ?>
                        <tr>
                            <td class="title_cell" valign="top">
                        <?php echo JText::_('JMS_PAYMENT_METHOD'); ?>
                            </td>
                            <td>
                        <?php
                        for ($i = 0, $n = count($paymentMethodsArr); $i < $n; $i++)
                        {
                            $paymentMethod = $paymentMethodsArr[$i];
                            if ($paymentMethod == $defaultpaymentMethod) $checked = 'checked="checked" ';
                            else $checked = '';
                            ?>
                                    <input onchange="changePaymentMethod();" type="radio" name="payment_method" value="<?php echo $paymentMethod; ?>" <?php echo $checked; ?> /><?php echo JText::_(strtoupper($paymentMethod)); ?> <br />
                            <?php
                        }
                        ?>
                            </td>
                        </tr>				
                        <?php
                    }

                    $cardInfoDisplay = false;
                    $ewayCardInfoDisplay = false;
                    $n = count($paymentMethodsArr);
                    if ($n == 1)
                    {
                        if ($paymentMethodsArr[0] == 'iwl_authnet')
                        {
                            $cardInfoDisplay = true;
                        }
                        elseif ($paymentMethodsArr[0] == 'iwl_eway')
                        {
                            $ewayCardInfoDisplay = true;
                        }
                    }
                    elseif ($n == 2)
                    {
                        if ($paymentMethodsArr[0] == 'iwl_authnet' && $paymentMethodsArr[1] == 'iwl_eway')
                        {
                            $cardInfoDisplay = true;
                        }
                    }

                    if ($paymentMethodsArr[0] == 'iwl_authnet' || $cardInfoDisplay)
                    {
                        $style = '';
                        $ewayStyle = 'style="display: none;"';
                    }
                    elseif ($paymentMethodsArr[0] == 'iwl_eway' || $ewayCardInfoDisplay)
                    {
                        $style = 'style="display: none;"';
                        $ewayStyle = '';
                    }
                    else
                    {
                        $style = 'style="display: none;"';
                        $ewayStyle = 'style="display: none;"';
                    }
                    ?>
                    <tr id="card_info" <?php echo $style; ?>>
                        <td>&nbsp;</td>
                        <td>
                            <table width="100%" cellspacing="5" cellpadding="5" class="jms button-table category">								
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('JMS_AUTH_CARD_NUMBER'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="x_card_num" class="inputbox" onkeyup="checkNumber(this);" value="<?php echo JRequest::getVar('x_card_num', ''); ?>" size="20" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('JMS_AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="x_exp_date" class="inputbox" value="<?php echo JRequest::getVar('x_exp_date', ''); ?>" size="20" />&nbsp;&nbsp;(mm/yy)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('JMS_AUTH_CVV_CODE'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="x_card_code" class="inputbox" onKeyUp="checkNumber(this);" value="<?php echo JRequest::getVar('x_card_code', ''); ?>" size="20" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr id="eway_card_info" <?php echo $ewayStyle; ?>>
                        <td>&nbsp;</td>
                        <td>
                            <table width="100%" cellspacing="3" cellpadding="3" class="subscr_sub_table">
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('COM_JMS_CARD_HOLDER_NAME'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="card_holder_name" class="inputbox" value="<?php echo JRequest::getVar('card_holder_name', ''); ?>" size="20" />
                                    </td>
                                </tr>								
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('COM_JMS_CARD_NUMBER'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="card_num" class="inputbox" onkeyup="checkNumber(this);" value="<?php echo JRequest::getVar('card_num', ''); ?>" size="20" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('COM_JMS_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="exp_date" class="inputbox" value="<?php echo JRequest::getVar('exp_date', ''); ?>" size="20" />&nbsp;&nbsp;(mm/yy)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="title_cell">
            <?php echo JText::_('COM_JMS_EWAY_CARD_CODE'); ?><span class="required">*</span>
                                    </td>
                                    <td class="field_cell">
                                        <input type="text" name="card_code" class="inputbox" onKeyUp="checkNumber(this);" value="<?php echo JRequest::getVar('card_code', ''); ?>" size="20" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <p><a href="javascript: void(0);" title="<?php echo JText::_('JMS_PROCESS_SUBSCRIPTION'); ?>" onclick="processSubscription();">
                        <img src="<?php echo JURI::base() . $subscribeBtnImg; ?>" border="0" alt="Submit" /></a></p>

                <input type="hidden" name="option" value="com_jms" />
                <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
                <input type="hidden" name="view" value="jms" />
                <input type="hidden" value="" name="task"/>

            <?php
            if ($config->get('mc_enable'))
            {
                ?>
                    <!-- mailchimp -->
                    <input type="hidden" name="mc_api_key" value="<?php echo $config->get('mc_api'); ?>" />
                    <input type="hidden" name="mc_listid" value="<?php echo $config->get('mc_listid'); ?>" />
                    <input type="hidden" name="mc_groupid" value="<?php echo $config->get('mc_groupid'); ?>" />
                    <!-- /mailchimp -->
            <?php } ?>

                <script language="javascript" type="text/javascript">		
            		
                    var form = document.subscrform;
            		
                    if (form.subscription_type.value == 'I') {
                        form.task.value = 'jms.process_subscription';
                    } else {
                        form.task.value = 'jms.process_recurring_subscription';
                    }
            	
                </script>
            <?php
            if (count($paymentMethodsArr) == 1)
            {
                ?>
                    <input type="hidden" value="<?php echo $paymentMethodsArr[0]; ?>" name="payment_method" />
                <?php
            }
            ?>
            </form>
            <?php
        }
    }

}
