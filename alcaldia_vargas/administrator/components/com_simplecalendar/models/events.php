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

defined('_JEXEC') or die;

/**
 * Methods supporting a list of event records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_simplecalendar
 * @since       1.6
 */
class SimplecalendarModelEvents extends JModelList
{
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
			/* Questi vanno configurati tutte le volte che si vuole filtrare una lista */
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'start_dt', 'a.start_dt',
				'end_dt', 'a.end_dt',
				'alias', 'a.alias',
				'state', 'a.state',
				'catid', 'a.catid', 'category_title'
			);
		}

		parent::__construct($config);
	}
	
	/**
    * Set default values when no action is specified (ie for cancel)
    */
    public function getModel($name = 'Event', $prefix = 'SimplecalendarModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

	/**
	 * Method to get the maximum ordering value for each category.
	 *
	 * @since   1.6
	 */
	public function getCategoryOrders()
	{
		if (!isset($this->cache['categoryorders']))
		{
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('catid');
			$query->from('#__simplecalendar');
			$db->setQuery($query);
			$this->cache['categoryorders'] = $db->loadAssocList('catid', 0);
		}
		return $this->cache['categoryorders'];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from($db->quoteName('#__simplecalendar').' AS a');

		
		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where('a.state = '.(int) $state);
		} elseif ($state === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where('a.catid = '.(int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}
		
		// Filter by date/period
		$period = $this->getState('filter.period', 'all');
		//var_dump($period);
		switch ( $period )
		{
			
			case 'past':
				$query->where('a.start_dt <= CURDATE()');
				break;
			case 'upcoming':
				$query->where('a.start_dt >= CURDATE()');
				break;
			case 'all':
			default:
				break;
				
		}
		
// 		Filter by venue
		$venue = $this->getState('filter.venue', '');
		if ( $venue != '' )
		{
			$query->where('a.venue = ' . $db->Quote($db->escape($venue, true)));
		}
		
// 		Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
// 			$query->where('a.language = ' . $db->quote($language));
		}

// 		Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.start_dt');
		$orderDirn	= $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id	A prefix for the store id.
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.category_id');
		//$id .= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 * @since   1.6
	 */
	public function getTable($type = 'Event', $prefix = 'SimpleCalendarTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);
		
		$period = $this->getUserStateFromRequest($this->context.'.filter.period', 'filter_period', '');
		$this->setState('filter.period', $period);
		
		$venue = $this->getUserStateFromRequest($this->context.'.filter.venue', 'filter_venue', '');
		$this->setState('filter.venue', $venue);
		
		// $clientId = $this->getUserStateFromRequest($this->context.'.filter.client_id', 'filter_client_id', '');
		// $this->setState('filter.client_id', $clientId);

		// $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		// $this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_simplecalendar');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.start_dt', 'asc');
	}
}
