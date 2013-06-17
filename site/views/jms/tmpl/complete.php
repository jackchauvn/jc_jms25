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
jimport('joomla.utilities.utility');
jimport('joomla.application.controller');

$model = $this->getModel();
$user = JFactory::getUser();
if ($_SESSION['voucher_id'] != null)
{
    $model->insertUserGiftvoucher($user->id, $_SESSION['voucher_id'], CURRENT_TIMESTAMP);
    unset($_SESSION['voucher_id']);
}
if ($_SESSION['id_coupon'] != null)
{
    $coupon = $model->getCouponid($_SESSION['id_coupon']);
    unset($_SESSION['id_coupon']);
}
if ($_SESSION['id_vuocher'] != null)
{
    $vuocher = $model->getVoucherById($_SESSION['id_vuocher']);
    unset($_SESSION['id_vuocher']);
}
$uri = JFactory::getURI();
$url = $uri->toString(array('scheme', 'host', 'path', 'fragment'));
$sender_name = $_SESSION['sender_name'];
$sender_email = $_SESSION['sender_email'];
$receiver_name = $_SESSION['receiver_name'];
$receiver_email = $_SESSION['receiver_email'];
$message = $_SESSION['message'];
$id_coupon = $_SESSION['id_coupon'];
$jconfig = new JConfig();
$fromEmail = $jconfig->mailfrom;
$fromName = $jconfig->fromname;
$subject1 = "Gift Voucher from " . $sender_name;
$subject2 = "Gift Voucher To " . $receiver_name;

$message1 = '
<html>
<head>
<title>voucher-v2</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<div>					
	<h3>' . $sender_name . ' has sent you a gift voucher to use at:(' . $url . ')</h3><br>
	<h5>(You may need to click on "download images" to view this Voucher correctly)</h5>
</div>		
<!-- Save for Web Slices (voucher-v2.psd) -->
<table id="Table_01" width="480" height="201" border="0" cellpadding="0" cellspacing="0" valign="bottom">
	<tr>
		<td style="line-height: 1px;" rowspan="3" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_01.png" width="162" height="200" alt=""></td>
		<td style="line-height: 1px;"  rowspan="1" colspan="4" valign="bottom">
			<table border="0" cellpadding="0" cellspacing="0" valign="bottom">
				<tr>
					<td valign="bottom"><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_02.png" width="192" height="96" alt=""></td>
					<td valign="bottom"><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_03.png" width="126" height="8" alt="">
						<table valign="bottom" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="114" height="88" align="right">
									<h1 style="margin: 0px; font-size: 38px; display: block; line-height: 28px;"><font color="#fb7925">$' . $vuocher->price . '</font></h1>
									<p style="line-height: 15px; margin: 0px; display: block;"><font face="arial" size="2" color="#666666">CODE:<br />' . $coupon->code . '</font></p>
								</td>
								<td><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_05.png" width="12" height="88" alt=""></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" width="284" height="95" valign="top">
			<p style="margin: 0px; display: block;"><font face="arial" color="#333333" size="2"><em>To:</em>' . $receiver_name . '<br />
			<em>From:</em>' . $sender_name . '</font></p>
			<table border="0" cellpadding="0" cellspacing="0" valign="top" width="275">
				<tr>
					<td style="border: 1px solid #ccc; border-radius: 5px; padding: 5px;"  height="50" valign="top"><font face="arial" color="#666666" size="2">' . $message . '</font></td>
				<td>
			</table>
		</td>
		<td style="line-height: 1px;"  colspan="2" rowspan="2" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_07.png" width="34" height="104" alt=""></td>
	</tr>
	<tr>
		<td style="line-height: 1px;"  colspan="2" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_08.png" width="284" height="8" alt=""></td>
	</tr>

</table>
<!-- End Save for Web Slices -->
</body>
</html>';

$message2 = '
<html>
<head>
<title>voucher-v2</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<div>					
	<h3>Your voucher was sent to ' . $receiver_name . '</h3>					
</div>			
<!-- Save for Web Slices (voucher-v2.psd) -->
<table id="Table_01" width="480" height="201" border="0" cellpadding="0" cellspacing="0" valign="bottom">
	<tr>
		<td style="line-height: 1px;" rowspan="3" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_01.png" width="162" height="200" alt=""></td>
		<td style="line-height: 1px;"  rowspan="1" colspan="4" valign="bottom">
			<table border="0" cellpadding="0" cellspacing="0" valign="bottom">
				<tr>
					<td valign="bottom"><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_02.png" width="192" height="96" alt=""></td>
					<td valign="bottom"><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_03.png" width="126" height="8" alt="">
						<table valign="bottom" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="114" height="88" align="right">
									<h1 style="margin: 0px; font-size: 38px; display: block; line-height: 28px;"><font color="#fb7925">$' . $vuocher->price . '</font></h1>
									<p style="line-height: 15px; margin: 0px; display: block;"><font face="arial" size="2" color="#666666">CODE:<br />' . $coupon->code . '</font></p>
								</td>
								<td><img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_05.png" width="12" height="88" alt=""></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" width="284" height="95" valign="top">
			<p style="margin: 0px; display: block;"><font face="arial" color="#333333" size="2"><em>To:</em>' . $receiver_name . '<br />
			<em>From:</em>' . $sender_name . '</font></p>
			<table border="0" cellpadding="0" cellspacing="0" valign="top" width="275">
				<tr>
					<td style="border: 1px solid #ccc; border-radius: 5px; padding: 5px;"  height="50" valign="top"><font face="arial" color="#666666" size="2">' . $message . '</font></td>
				<td>
			</table>
		</td>
		<td style="line-height: 1px;"  colspan="2" rowspan="2" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_07.png" width="34" height="104" alt=""></td>
	</tr>
	<tr>
		<td style="line-height: 1px;"  colspan="2" valign="bottom">
			<img style="display: block;" src="http://begood.vn/emailtest/img/voucher-v2_08.png" width="284" height="8" alt=""></td>
	</tr>

</table>
<!-- End Save for Web Slices -->
</body>
</html>';

if (($sender_email != "") && ($receiver_email != ""))
{
    JUtility::sendMail($fromEmail, $fromName, $receiver_email, $subject1, $message1, 1);
    JUtility::sendMail($fromEmail, $fromName, $sender_email, $subject2, $message2, 1);
    unset($_SESSION['receiver_name']);
    unset($_SESSION['receiver_email']);
    unset($_SESSION['message']);
}
?>

    <?php $Itemid = JRequest::getInt('Itemid'); ?>

<h1><?php
    if (($sender_email != "") && ($receiver_email != ""))
    {
        echo JText::_('Your gift Voucher has been sent');
        unset($_SESSION['sender_name']);
        unset($_SESSION['sender_email']);
    }
    else
    {
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $return_url = $session->get("return_url");
        $app->redirect(JRoute::_($return_url, false));
    }
    ?></h1>

<div class="contentpanopen">

    <p>

        <?php
//        if ($this->plan->completed_msg)
//        {
//            echo $this->plan->completed_msg;
//        }
//        else
//        {
//            echo $this->params->get('completed_msg');
//        }
        ?>

    </p>

    <a href="<?php echo JRoute::_("index.php?option=com_jms&view=jms&Itemid=" . $Itemid); ?>">
<?php echo $this->params->get('subscription_page_title'); ?>
    </a>

</div>	

<?php
$user = JFactory::getUser();
$config = JComponentHelper::getParams('com_jms');

if (($user->get('id')) && (!$config->get('enable_autologin')))
{
    $app = JFactory::getApplication();
    $app->logout($user->get('id'));
}
?>