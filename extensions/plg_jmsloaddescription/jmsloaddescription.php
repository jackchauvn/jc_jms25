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
 * Content Load Voucher Description Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentJmsloaddescription extends JPlugin
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
        // Simple performance check to determine whether the plugin should process further
        if (JString::strpos($row->text, 'JMS-GIFT-VOUCHER ID') === false)
        {
            return true;
        }

        if (preg_match("/{JMS-GIFT-VOUCHER ID=([\d,]+)}/iU", $row->text, $matches))
        {
            $matches = $matches[1];
            $id = $matches;
            if ($matches == '')
            {
                $voucher = '';
            }
            else
            {
                ob_start();
                $this->_showGiftVoucher($matches);
                $voucher = ob_get_contents();
                ob_end_clean();
            }
            $row->text = preg_replace("/{JMS-GIFT-VOUCHER ID=[^}]+}/iU", $voucher, $row->text);

            if (preg_match("/{jms-coupon-code ID=([\d,]+)}/iU", $row->text, $matches))
            {
                $matches = $matches[1];
				$id_coupon = $matches;
                if ($matches == '')
                {
                    $voucher = '';
                }
                else
                {
                    ob_start();
                    $this->_showCouponName($matches);
                    $voucher = ob_get_contents();
                    ob_end_clean();
                }
                $voucher = '&nbsp;';
                
                $row->text = preg_replace("/{jms-coupon-code ID=[^}]+}/iU", $voucher, $row->text);
            }

            if (preg_match("/{jms-coupon-message}/iU", $row->text, $matches))
            {
                ob_start();
                $this->_showMessage($id);
                $voucher = ob_get_contents();
                ob_end_clean();
                $row->text = preg_replace("/{jms-coupon-message}/iU", $voucher, $row->text);
            }

            if (preg_match("/{jms-coupon-rname}/iU", $row->text, $matches))
            {
                $voucher = "(Receiver Name)";
                $row->text = preg_replace("/{jms-coupon-rname}/iU", $voucher, $row->text);
            }

            if (preg_match("/{jms-coupon-sname}/iU", $row->text, $matches))
            {
                $voucher = "(Sender Name)";
                $row->text = preg_replace("/{jms-coupon-sname}/iU", $voucher, $row->text);
            }
            $voucher_obj = $this->_getVoucher($id);
			$user = JFactory::getUser();
            JHTML::stylesheet('components/com_jms/assets/fancybox/jquery.fancybox-1.3.4.css');
            JHTML::script('components/com_jms/assets/jquery-1.6.2.min.js');
            JHTML::script('components/com_jms/assets/fancybox/jquery.fancybox-1.3.4.pack.js');
            if($user->id)
            {       
            $row->text .= '<a id="' . $id . '" href="#dialog" class="btn cl-blue hcgray clearfix">
                Buy now
            </a>';
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
                $row->text .= '<a  href="index.php?option=com_jms&view=form&url=' .$url .'" class="btn cl-blue hcgray clearfix">
                Buy now
            </a>';  
            }   
            ?>
            <div style="display :none;">
                <div id="dialog" style="width: 100%; overflow: auto;">
                    <div style="width: 100%;">
                        <div class="content" style="margin-top: 80px;">
                            <form id="form" action="index.php?option=com_jms&view=jms&layout=ordervoucher&id=<?php echo $id; ?>" method="post">
                                <table id="table">
                                    <tr>
										<input type="hidden" name="id_coupon" value="<?php echo $id_coupon; ?>">
                                        <td align="left">
                                            <label>Sender name</label>
                                        </td>
                                        <td>
                                            <input type="text" id="sender_name" name="sender_name" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            <label>Sender email</label>
                                        </td>
                                        <td>
                                            <input type="text" id="sender_email" name="sender_email" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            <label>Receiver name</label>
                                        </td>
                                        <td>
                                            <input type="text" id="receiver_name" name="receiver_name" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            <label>Receiver email</label>
                                        </td>
                                        <td>
                                            <input type="text" id="receiver_email" name="receiver_email" />
                                        </td>
                                    </tr>
                                    <?php if($voucher_obj->allow_edit == 1) : ?>
                                    <tr>
                                        <td align="left">
                                            <label>Message</label>
                                        </td>
                                        <td>
                                            <input type="text" id="message" name="message" value="<?php echo $voucher_obj->message; ?>" />
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <a onclick="formvalidate()" class="btn cl-blue hcgray clearfix">
                                                Buy now
                                            </a>
                                        </td>
                                    </tr>
                                </form>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <style>                
                #main .cl-blue:hover, #main .cl-blue:focus {
                    background-attachment: scroll;
                    background-clip: border-box;
                    background-color: transparent;
                    background-image: url("components/com_jms/assets/images/btn-blue.png");
                    background-origin: padding-box;
                    background-position: left top;
                    background-repeat: no-repeat;
                    background-size: auto auto;
                }
                #dialog .cl-blue:hover, #dialog .cl-blue:focus {
                    background-attachment: scroll;
                    background-clip: border-box;
                    background-color: transparent;
                    background-image: url("components/com_jms/assets/images/btn-blue.png");
                    background-origin: padding-box;
                    background-position: left top;
                    background-repeat: no-repeat;
                    background-size: auto auto;
                }
                #table tr, #table td {
                    border: none;
                }
                #table td {
                    padding: 10px;
                }
                #table label {
                    font-size: 11pt;
                }
                #table input {
                    font-size: 11pt;
                    padding: 5px;
                    width: 200%;
                }
            </style>
            <?php
            $row->text .= "
                <script type=\"text/javascript\">
                    jQuery(\"#$id\").fancybox({
                        'title': 'Buy Voucher Details',
                        'transitionIn': 'elastic',
                        'transitionOut': 'elastic',
                        'titleShow': true,
                        'titlePosition': 'over',
                        'onComplete': function() { jQuery(\"#fancybox-title\").css({'top':'0px', 'bottom':'auto'}); } 
                    });
                    function formvalidate()
                    {
                        if(jQuery('#sender_email').val() == '')
                        {
                            alert('Please input Sender email');
                            return false;
                        }
                        if(jQuery('#receiver_email').val() == '')
                        {
                            alert('Please input Receiver email');
                            return false;
                        }
                        jQuery('form#form').submit();
                    }
                </script>
                ";
        }
    }

    function _showMessage($matches)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT message FROM #__jms_vouchers' .
                ' WHERE id IN (' . trim($matches) . ')' .
                ' AND state = 1' .
                ' ORDER BY ordering'
        ;
        $db->setQuery($sql);
        $row = $db->loadObject();
        echo $row->message;
    }

    function _showGiftVoucher($matches)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT description FROM #__jms_vouchers' .
                ' WHERE id IN (' . trim($matches) . ')' .
                ' AND state = 1' .
                ' ORDER BY ordering'
        ;
        $db->setQuery($sql);
        $row = $db->loadObject();
        echo $row->description;
    }

    function _getVoucher($matches)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__jms_vouchers' .
                ' WHERE id IN (' . trim($matches) . ')' .
                ' AND state = 1' .
                ' ORDER BY ordering'
        ;
        $db->setQuery($sql);
        return $db->loadObject();
    }

    function _showCouponName($matches)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT code FROM #__jms_coupons' .
                ' WHERE id IN (' . trim($matches) . ')' .
                ' AND state = 1'
        ;
        $db->setQuery($sql);
        $row = $db->loadObject();
        echo $row->code;
    }

}
