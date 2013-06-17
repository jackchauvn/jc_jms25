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

jimport('joomla.application.component.modeladmin');

/**
 * Jms model.
 */
class JmsModelsubscr extends JModelAdmin
{

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_JMS';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Subscr', $prefix = 'JmsTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm('com_jms.subscr', 'subscr', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jms.edit.subscr.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	1.6
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            //Do any procesing on fields here if needed
            $user_id = $item->user_id;
            $db = JFactory::getDbo();
            $db->setQuery("SELECT points FROM #__jms_user_points WHERE user_id='$user_id'");
            $item->reward_point = $db->loadResult();
        }

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
    protected function prepareTable(&$table)
    {
        jimport('joomla.filter.output');

        if (empty($table->id))
        {

            // Set ordering to the last item if not set
            if (@$table->ordering === '')
            {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__jms_plan_subscrs');
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    public function save($data)
    {
        $user_id = $data["user_id"];
        $plan_id = $data["plan_id"];

        $db = JFactory::getDbo();

        if ($data["id"] == 0)
        {
            $query = "SELECT count(*) AS id_exist FROM #__jms_plan_subscrs WHERE user_id = '$user_id' AND plan_id = '$plan_id'";
            $db->setQuery($query);
            $id_exist = $db->loadResult();

            if ($id_exist)
            {
                $this->setError('This subscriber is already existed.');
                return false;
            }
        }
        if (parent::save($data))
        {
            $points = $data["reward_point"];

            $query = "DELETE FROM #__jms_user_points WHERE user_id = $user_id";
            $db->setQuery($query);
            $db->query();

            $query = "INSERT INTO #__jms_user_points(user_id, points) VALUES($user_id, $points)";
            $db->setQuery($query);
            $db->query();

            return true;
        }

        return false;
    }

}