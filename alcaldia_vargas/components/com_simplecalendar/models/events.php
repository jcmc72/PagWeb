<?php
/**
 *	com_simplecalendar - a simple calendar component for Joomla
 *  Copyright (C) 2008-2013 Fabrizio Albonico
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * SimpleCalendar Events Model
*/
class SimpleCalendarModelEvents extends JModelList
{
	/**
	 * @var object item
	 */
	protected $_item = null;

	protected $_events = null;

	protected $_context = 'com_simplecalendar.events';

	/**
	 * The category that applies.
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	// 	protected $_categories = null;

	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'name', 'a.name',
					'alias', 'a.alias',
					'venue', 'a.venue',
					'start_dt', 'a.start_dt',
					'end_dt', 'a.end_dt',
					'catid', 'a.catid', 'catname',
					'state', 'a.state',
					'created_dt', 'a.created_dt',
					'created_by', 'a.created_by',
					'modified_dt', 'a.modified_dt',
					'featured', 'a.featured',
					'hits', 'a.hits',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
		$app = JFactory::getApplication('site');
		$pk  = $app->input->getArray(array('id'=>null));
		
		$this->setState('category.id', $pk);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);
		$user		= JFactory::getUser();
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		
		$query->where('a.access IN (' . $groups . ')')
		->where('c.access IN (' . $groups . ')');

		if ((!$user->authorise('core.edit.state', 'com_simplecalendar')) &&  (!$user->authorise('core.edit', 'com_simplecalendar'))){
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);
			// 			// Filter by start and end dates.
			// 			$nullDate = $db->Quote($db->getNullDate());
			// 			$nowDate = $db->Quote(JFactory::getDate()->toSQL());

			// 			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			// 			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}

		// process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
		// 		var_dump($_POST, $_GET);
		// 		exit;
		// Optional filter text
		$this->setState('list.filter', $app->input->getString('filter-search'));

		// filter.order
		$itemid = $app->input->get('id', 0, 'int') . ':' . $app->input->get('Itemid', 0, 'int');
		$orderCol = $app->getUserStateFromRequest('com_simplecalendar.events.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		// 		var_dump($this->filter_fields);
		// 		exit;
		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.start_dt';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_simplecalendar.events.list.' . $itemid . '.filter_order_Dir',
				'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));

		// set limit for query. If list, use parameter. If blog, add blog parameters for limit.
		// 		if (($app->input->get('layout') == 'blog') || $params->get('layout_type') == 'blog')
			// 		{
			// 			$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
			// 			$this->setState('list.links', $params->get('num_links'));
			// 		}
			// 		else
				// 		{
		$limit = $app->getUserStateFromRequest('com_simplecalendar.events.list.' . $itemid . '.limit', 'limit', $params->get('display_num'), 'uint');
		// 		}

		$this->setState('list.limit', $limit);

		// 		$this->setState('filter.language', JLanguageMultilang::isEnabled());

		$this->setState('layout', $app->input->get('layout'));

		//get the id of the category
		// 		$this->setState('category.id', $app->input->getArray(array('catid'=>null)));


	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	// 	s


	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 */
	// 	public function getItems()
	// 	{
	// 		// Invoke the parent getItems method to get the main list
	// 		$items = parent::getItems();

	// 		// Convert the params field into an object, saving original in _params
	// 		for ($i = 0, $n = count($items); $i < $n; $i++)
		// 		{
		// 			$item = &$items[$i];
		// 			if (!isset($this->_params))
			// 			{
			// 				$params = new JRegistry;
			// 				$params->loadString($item->params);
			// 				$item->params = $params;
			// 			}
			// 		}

			// 		return $items;
			// 	}

	/**
	 * Get the events in the category
	 *
	 * @return  mixed  An array of events or false if an error occurs.
	 * @since   3.0
	 */
	function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = &$items[$i];
			if (!isset($this->_params))
			{
				$params = new JRegistry;
				$params->loadString($item->params);
				$item->params = $params;
			}
		}
		$this->_items = $items;
		return $items;
	}

	/**
	 * Get the calendar list
	 * @return array The array of calendar events (entries) to be displayed to the user
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('a.*, cat.id AS catid, cat.alias AS catalias, cat.title AS catname, cat.access AS category_access, '
				.' org.id AS gid, org.name AS org_name, org.abbr AS org_abbr, '
				.' org.contact_name AS org_contact_name, org.contact_email AS org_contact_email, '
				.' org.contact_website AS org_contact_website, org.latlon AS org_latlon, '
				.' org.contact_telephone AS org_contact_telephone, '
				.' s.id AS status_id, s.name AS status_name, s.color AS status_color, '
				.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug, '
				.' CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END AS catslug'
		);
		$query->from('#__simplecalendar as a');
		$query->join('inner', '#__categories AS cat ON a.catid = cat.id');
		$query->join('left', '#__simplecalendar_organizers AS org ON a.organizer_id = org.id');
		$query->join('left', '#__simplecalendar_statuses AS s ON a.statusid = s.id');
		$whereString = SimpleCalendarModelEvents::_buildQueryWhere();
		if ( $whereString != '' )
		{
			$query->where($whereString);
		}
		$query->order(SimpleCalendarModelEvents::_buildQueryOrderBy());
		return $query;
	}



	/**
	 * Builds the WHERE part of the query
	 * @return string where part of the query
	 */
	protected function _buildQueryWhere()
	{
		global $app;
		$db			= JFactory::getDBO();
		$config 	= JComponentHelper::getParams('com_simplecalendar');
		$params 	= $app->getParams();
		$user		= JFactory::getUser();

		$search 	= $app->getUserStateFromRequest( 'list.filter','filter-search','','string' );
		// 		var_dump($search);
		$search		= JString::strtolower( $search );

		$where = array();

		// Get list ordering default from the parameters
		$menuParams = new JRegistry;
		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}
		$mergedParams = clone $params;
		$mergedParams->merge($menuParams);

		// 		$period =  $mergedParams->get('period', '0');

		switch ( $mergedParams->get('period', '0') )
		{
			case 1: //future events only
				$where[] = '(CASE WHEN a.end_dt > a.start_dt THEN a.end_dt ELSE a.start_dt END) >= CAST(CURDATE() AS date)';
				break;
			case 2: //past events only
				$where[] = '(CASE WHEN a.end_dt > a.start_dt THEN a.end_dt ELSE a.start_dt END) <= CAST(CURDATE() AS date)';
				break;
			case -1: //archived events only
				$where[] = 'a.state = -1';
				break;
			case '3':
				// upcoming, featured events
				$where[] = 'a.state = 1 and a.start_dt >= CURDATE() AND a.featured = 1';
				break;
			case '4':
				// past, featured events
				$where[] = 'a.state = 1 and a.start_dt <= CURDATE() AND a.featured = 1';
				break;
			case 0:
			default: //future and past events
				break;
		}

		// Filter by category, if necessary
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$view = $app->input->getString('view');
		if ( $view = 'events' )
		{
			$categoryArray = $app->input->getArray(array('catid'=>''));
			$whereString = '';
			if ( !is_array($categoryArray['catid']) )
			{
				$whereString = 'a.catid = ' . (int)$categoryArray['catid'];
				$category = JCategories::getInstance('SimpleCalendar')->get((int)$categoryArray['catid']);
				if ( $params->get('show_subcategory_content', '1') == '1' && $category->hasChildren() == true ) 
				{
					$childCategories = $category->getChildren();
					foreach ( $childCategories as $childCategory ) 
					{
						$whereString .= ' OR a.catid = ' . (int) $childCategory->get('id');
					}
				}
			}
			if ( strlen($whereString) > '' )
			{
				$where[] = '(' . $whereString . ')';
			}
			unset($categoryArray['id']);
		}
		else
		{
			$categoryArray = $app->input->getArray(array('catid'=>''));
		}
		
		if ( $categoryArray )
		{
			$catid = array();
			$categoryArray = is_array($categoryArray['catid']) ? $categoryArray['catid'] : false;
			if ( $categoryArray )
			{
				$categoryId = array();
				foreach ( $categoryArray as $row )
				{
					// transform slugified catids
					$catid = explode(':', $row);
					if ( $row != '' )
					{
						$categoryId[] = $catid[0];
					}
				}
			}
			else
			{
				$categoryId = $categoryArray;
			}
			if ( $categoryId && implode( ',', $categoryId ) != '' )
			{
				$where[] = '(cat.id=' . implode( ' OR cat.id=', $categoryId ) . ')';
			}
			$where[] = 'cat.access IN (' . $groups . ')';
		}

		if ( $user->guest )
		{
			// guests can only see published events
			$where[] = 'a.state = \'1\'';
		}
		else
		{
			// Registered users can only see their own disabled/archived/trashed events
			if ( 1 == 2 ) 
			{
				var_dump($user);
				exit;
			} 			
			// else, Root users see everything
		}

		if ($params->get('filter'))
		{
			$filter 		= JRequest::getString('filter', '', 'request');
			$filter_type 	= JRequest::getWord('filter_type', '', 'request');
		}

		// Filter by search in title
		if ( !empty($search) ) {
			$where[] = '( LOWER(a.name) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ' OR ' .
					' LOWER(a.venue) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ' OR ' .
					' LOWER(a.description) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ' OR ' .
					' LOWER(org.contact_name) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ' OR ' .
					' LOWER(cat.title) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ).
					')';
		}

		$whereString = count( $where ) ? implode( ' AND ', $where ) : '';

		return $whereString;
	}

	/**
	 * Builds the order by part of the query
	 * @return string
	 */
	function _buildQueryOrderBy()
	{
		$config 	= JComponentHelper::getParams('com_simplecalendar');
		$app 		= JFactory::getApplication();
		$params 	= $app->getParams();
		$order_dir 	= '';

		$order_dir = ' '. $app->input->get('sort');

		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;

		$itemid		= $app->input->get('id', 0, 'int') . ':' . $app->input->get('Itemid', 0, 'int');

		$orderCol	= $app->getUserStateFromRequest('com_simplecalendar.events.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$orderDirn	= $app->getUserStateFromRequest('com_simplecalendar.events.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$orderby	= ' ';

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = null;
		}

		if ( !in_array(strtoupper($orderDirn), array('ASC', 'DESC')) )
		{
			$orderDirn = $params->get('sort') != '' ? $params->get('sort') : 'ASC';
		}
		if ($orderCol && $orderDirn)
		{
			$orderby .= $db->escape($orderCol) . ' ' . $db->escape($orderDirn) .', a.start_dt, a.start_time';
		}
		else
		{
			$orderby = 'a.start_dt '. $db->escape($orderDirn) .', a.start_time, a.id';
		}

		return $orderby;
	}

	/**
	 * Get the parent category.
	 *
	 * @param   integer  An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 */
	public function getParent()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the child categories.
	 *
	 * @param   integer  An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 * @since   1.6
	 */
	function &getChildren()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		// Order subcategories
		if (count($this->_children))
		{
			// 			$params = $this->getState()->get('params');
			// 			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha')
				// 			{
				// 				jimport('joomla.utilities.arrayhelper');
				// 				JArrayHelper::sortObjects($this->_children, 'name', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
				// 			}
		}

		return $this->_children;
	}


	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('list.start'), $this->getState('list.limit') );
		}
		return $this->_pagination;
	}


	/**
	 * Method to get category data for the current category
	 *
	 * @param   integer  An optional ID
	 *
	 * @return  object
	 * @since   1.5
	 */
	public function getCategory()
	{
		if (!is_object($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;

			if ($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
// 			$options['countItems'] = $params->get('show_cat_items', 1) || $params->get('show_empty_categories', 0);
			$categoriesObject = JCategories::getInstance('SimpleCalendar', $options);
			if ( $app->input->get('catid', '') != '' ) {
				$categoryArray = array('id' => $app->input->get('catid', ''));
			} else {
				$categoryArray = $this->getState('category.id', 'root');
			}
			if ( !is_array($categoryArray) )
			{
				// Only one category - get its data
				$this->_item = $categoriesObject->get($categoryArray);
			}
			else
			{
				
				if ( is_array($categoryArray['id']) ) {
					// no category
					$categoryId = 0;
				} else {
					// multiple categories, get the values from the first one
					$categoryId = (int)$categoryArray['id'];
				}
				
				$this->_item = $categoriesObject->get($categoryId);
			}

			if (is_object($this->_item)) //TODO
			{
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
				// 				$this->_rightsibling = $this->_item->getSibling();
				// 				$this->_leftsibling = $this->_item->getSibling(false);
// 				var_dump($this->_item, $this->_parent, $this->_children);
// 				exit;
			} else {
				$this->_children = false;
				$this->_parent = false;
			}
		}
		// 		var_dump($this->_item);
		// 		exit;
		return $this->_item;
	}
}
