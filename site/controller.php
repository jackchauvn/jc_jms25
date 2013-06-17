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

jimport('joomla.application.component.controller');

class JmsController extends JController
{

    /**
     * Method to display a view.
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        // Get the document object.
        $document = JFactory::getDocument();

        // Set the default view name and format from the Request.
        $vName = JRequest::getCmd('view', 'form');
        $vFormat = $document->getType();
        $lName = JRequest::getCmd('layout');

        if ($view = $this->getView($vName, $vFormat))
        {

            // Do any specific processing by view.
            switch ($vName)
            {

                // Handle view specific models.
                case 'form':
                    // If the user is already logged in, redirect to the profile page.
                    $user = JFactory::getUser();
                    if ($user->get('id'))
                    {
                        // Redirect to subscription page.
                        switch ($lName)
                        {
                            case 'failure':
                                $session = JFactory::getSession();
                                $session->set('cc_reason', JRequest::getVar('reason'), 'cc_error');
                                $this->setRedirect('index.php?option=com_jms&view=jms&layout=failure&Itemid=' . JRequest::getVar('Itemid'));
                                break;

                            default:
                                $this->jmsRestrictC();
                                break;
                        }
                        return;
                    }
                    $model = $this->getModel($vName);
                    break;

                // Handle view specific models.
                case 'jms':

                    $user = JFactory::getUser();
                    if ($user->get('id') == 0)
                    {
                        switch ($lName)
                        {
                            case 'order':
                                $this->jmsRestrictC();
                                return;
                            case 'myaccount':
                                $this->jmsRestrictC();
                                return;
                        }
                    }
                    else
                    {

                        $subscr_article = $this->jmsGetSubscr('article', $user->get('id'));
                        $subscr_category = $this->jmsGetSubscr('category', $user->get('id'));
                        
                        switch ($lName)
                        {
                            case 'order':
                                if (!$subscr_article && !$subscr_category)
                                {
                                    $this->jmsRestrictC();
                                    return;
                                }
                            case 'ordervoucher':
                                if (!$subscr_article && !$subscr_category)
                                {
                                    $this->jmsRestrictC();
                                    return;
                                }
                            case 'myaccount':
                                if (!$subscr_article && !$subscr_category)
                                {
                                    $this->jmsRestrictC();
                                    return;
                                }
                        }
                    }
                    $model = $this->getModel($vName);
                    break;

                default:
                    $model = $this->getModel('form');
                    break;
            }

            // Push the model into the view (as default).
            $view->setModel($model, true);
            $view->setLayout($lName);

            // Push document object into the view.
            $view->assignRef('document', $document);

            $view->display();
        }
    }

    function jmsGetSubscr($type, $userid, $ids = '')
    {
        $db = JFactory::getDbo();
        $db->setQuery(
                'SELECT u.plan_id, s.params, u.id, s.`categories`, s.`articles`, u.access_count, u.access_limit, s.limit_time_type,' .
                ' IF(u.access_limit > 0, IF(u.access_count >= u.access_limit, 0, 1), 1) AS cl' .
                ' FROM #__jms_plan_subscrs AS u' .
                ' LEFT JOIN #__jms_plans AS s ON s.id = u.plan_id  ' .
                ' WHERE u.user_id = ' . (int) $userid .
                ' AND u.expired > NOW()' .
                ' AND u.created < NOW()' .
                ' AND u.state = 1' .
                ' AND s.`' . $type . '_type` = 1'
                . ((int) $ids ? ' AND s.id IN (' . (int) $ids . ') ' : null) .
                ' GROUP BY u.plan_id' .
                ' HAVING cl > 0' .
                ' ORDER BY u.created DESC');

        return $db->loadObjectList();
    }

    function jmsRestrictC()
    {

        $mainframe = JFactory::getApplication();
        $Itemid = JRequest::getInt('Itemid');

        $session = & JFactory::getSession();
        $uri = JFactory::getURI();
        $url = $uri->toString();

        $current = JURI::base() . preg_replace("#/#", "", $_SERVER['REQUEST_URI'], 1);
        $router = JSite::getRouter();
        $vars = $router->parse($uri);

        $session->set('jms_access_url', $url);
        $session->set('jms_article_id', $vars['id']);

        $param = JComponentHelper::getParams('com_jms');

        if ($param->get('custom_registration_page'))
        {
            $mainframe->redirect("index.php?option=com_content&view=article&id=" . $param->get('custom_registration_page') . "&Itemid=" . $Itemid);
        }
        else
        {
            $my = & JFactory::getUser();
            if ($my->get('id') == 0) $mainframe->redirect("index.php?option=com_jms&view=form&Itemid=" . $Itemid);
            else $mainframe->redirect("index.php?option=com_jms&view=jms&Itemid=" . $Itemid);
        }
    }

}