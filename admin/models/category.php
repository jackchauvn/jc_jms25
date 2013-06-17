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
class JmsModelCategory extends JModelAdmin
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
    public function getTable($type = 'Category', $prefix = 'JmsTable', $config = array())
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
        $form = $this->loadForm('com_jms.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_jms.edit.category.data', array());

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
            if ($item->id != null)
            {
                $db = JFactory::getDbo();

                $query = "SELECT product_id FROM #__jms_categories_products WHERE category_id = " . $item->id;
                $db->setQuery($query);
                $product_ids = $db->loadObjectList();

                $array = array();
                foreach ($product_ids as $product_id) $array[] = $product_id->product_id;

                $registry = new JRegistry;
                $registry->loadArray($array);
                $item->products = $registry->toString();

                //convert the products field to an array
                $registry = new JRegistry;
                $registry->loadString($item->products);
                $item->products = $registry->toArray();

                $registry = new JRegistry;
                $registry->loadString($item->attribs);
                $item->attribs = $registry->toArray();

                $registry = new JRegistry;
                $registry->loadString($item->images);
                $item->images = $registry->toArray();
                
                $item->description = trim($item->full_description) != '' ? $item->short_description . "<hr id=\"system-readmore\" />" . $item->full_description : $item->short_description;
            }
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
                $db->setQuery('SELECT MAX(ordering) FROM #__jms_categories');
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    public function save($data)
    {
        if (isset($data['description']))
        {
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos = preg_match($pattern, $data['description']);

            if ($tagPos == 0)
            {
                $data['short_description'] = $data['description'];
                $data['full_description'] = '';
            }
            else
            {
                list ($data['short_description'], $data['full_description']) = preg_split($pattern, $data['description'], 2);
            }
        }

        if (isset($data['images']) && is_array($data['images']))
        {
            $registry = new JRegistry;
            $registry->loadArray($data['images']);
            $data['images'] = $registry->toString();
        }

        if (isset($data['attribs']) && is_array($data['attribs']))
        {
            $registry = new JRegistry;
            $registry->loadArray($data['attribs']);
            $data['attribs'] = $registry->toString();
        }

        if (parent::save($data))
        {
            $categoryId = $this->getState($this->getName() . '.id');

            $db = JFactory::getDbo();

            $query = "DELETE FROM #__jms_categories_products WHERE category_id = $categoryId";
            $db->setQuery($query);
            $db->query();

            foreach ($data["products"] as $productId)
            {
                $query = "INSERT INTO #__jms_categories_products(category_id, product_id) VALUES($categoryId, $productId)";
                $db->setQuery($query);
                $db->query();
            }

            return true;
        }

        return false;
    }

}