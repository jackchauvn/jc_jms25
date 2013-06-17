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
class plgContentJmsloadaccountmodule extends JPlugin
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
        if (JString::strpos($row->text, 'username') === false && JString::strpos($row->text, 'points-balance') === false)
        {
            return true;
        }
        
        $user = JFactory::getUser();

        if ($user->get('id') != 0)
        {
            if (preg_match("/{username}/iU", $row->text, $matches))
            {
                $row->text = preg_replace("/{username}/iU", $user->username, $row->text);
            }

            if (preg_match("/{points-balance}/iU", $row->text, $matches))
            {
                $points_balance = $this->_getPointsBalance($user->id);
                $row->text = preg_replace("/{points-balance}/iU", $points_balance, $row->text);
            }
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
    
    function _getPointsBalance($matches)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT points FROM #__jms_user_points' .
                ' WHERE user_id IN (' . trim($matches) . ')'
        ;
        $db->setQuery($sql);
        return $db->loadResult();
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
