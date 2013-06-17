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

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Jms records.
 */
class JmsModelSales extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'user_id', 'a.user_id',
                'product_id', 'a.product_id',
                'created', 'a.created',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
        // Adjust the context to support modal layouts.
        if ($layout = JRequest::getVar('layout'))
        {
            $this->context .= '.' . $layout;
        }

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $select = $app->getUserStateFromRequest($this->context . '.filter.select', 'filter_select', '', 'string');
        $this->setState('filter.select', $select);
        //load startdate & enddate
        $startdate = $app->getUserStateFromRequest($this->context . '.filter.startdate', 'filter_startdate');
        $this->setState('filter.startdate', $startdate);
        $enddate = $app->getUserStateFromRequest($this->context . '.filter.enddate', 'filter_enddate');
        $this->setState('filter.enddate', $enddate);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_jms');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.select');
        $id.= ':' . $this->getState('filter.startdate');
        $id.= ':' . $this->getState('filter.enddate');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );

        $query->from('`#__jms_user_products` AS a');

        $query->select('uc.name AS fullname, uc.email');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.user_id');

        $query->select('p.title, p.price, p.price_points, p.item_number, p.tags');
        $query->join('LEFT', '#__jms_products AS p ON p.id=a.product_id');


        // Filter by select box
        $select = $this->getState('filter.select');
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $token = $db->Quote('%' . $db->escape($this->getState('filter.search')) . '%');
            $searches = array();
            switch ($select)
            {
                case 0:
                    $searches[] = 'uc.name LIKE ' . $token;
                    $searches[] = 'uc.email LIKE ' . $token;
                    $searches[] = 'p.title LIKE ' . $token;
                    $searches[] = 'p.price LIKE ' . $token;
                    $searches[] = 'p.price_points LIKE ' . $token;
                    break;
                case 1:
                    $searches[] = 'p.title LIKE ' . $token;
                    break;
                case 2:
                    $searches[] = 'p.item_number LIKE ' . $token;
                    break;
                case 3:
                    $searches[] = 'p.tags LIKE ' . $token;
                    break;

                default:
                    break;
            }
            // Add the clauses to the query.
            $query->where('(' . implode(' OR ', $searches) . ')');
        }
        // startdate enddate
        $startdate = $this->getState('filter.startdate');
        $enddate = $this->getState('filter.enddate');
        
        if(!empty($startdate)&&!empty($enddate))
        {
            $startdate=date("Y-m-d", strtotime($startdate));
            $enddate=date("Y-m-d", strtotime($enddate));
            $a="'";
            $result='DATE_FORMAT(`created`, \'%Y-%m-%d\' ) BETWEEN '.$a.''.$startdate.''.$a.' AND '.$a.''.$enddate.''.$a.'';
            $query->where($result);
           
        }

        return $query;
    }

}
