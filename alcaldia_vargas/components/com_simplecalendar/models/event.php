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
 * SimpleCalendar Model
 */
class SimpleCalendarModelEvent extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;
 
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
	 * @since	3.0
	 */
	protected function populateState() 
	{
		$app = JFactory::getApplication();
		// Get the message id
		$id = JRequest::getInt('id');
		$this->setState('event.id', $id);
 
		$catid = JRequest::getInt('catid');
		$this->setState('event.catid', $catid);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}
	
	
 
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	3.0
	 */
	public function getTable($type = 'Event', $prefix = 'SimplecalendarTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

 
 
	/**
	 * Get the event item
	 * @return object The event item to be displayed to the user
	 * @since 3.0
	 */
	public function &getItem($pk = null) 
	{
		$user = JFactory::getUser();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');
		$db = $this->getDbo();
		if ($this->_item === null)
		{
			$this->_item = array();
		}
		
		if (!isset($this->_item[$pk]))
		{
			try 
			{
				$db = $this->getDbo();
				
				$query = $this->_db->getQuery(true);
				$query->select('a.*, cat.id AS catid, cat.alias AS catalias, cat.title AS catname, cat.access AS category_access, '
						.' g.id AS gid, g.name AS org_name, g.abbr AS org_abbr, '
						.' g.contact_name AS org_contact_name, g.contact_email AS org_contact_email, '
						.' g.contact_website AS org_contact_website, g.latlon AS org_latlon, '
						.' g.contact_telephone AS org_contact_telephone, '
						.' s.id AS status_id, s.name AS status_description, s.color AS status_color, '
						.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug, '
						.' CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END AS catslug'
					)
					->from('#__simplecalendar as a')
					->join('inner', '#__categories AS cat ON a.catid = cat.id')
					->join('left', '#__simplecalendar_organizers AS g ON a.organizer_id = g.id')
					->join('left', '#__simplecalendar_statuses AS s ON a.statusid = s.id')
					->where('a.id=' . (int) $pk);
				
				//TODO Filter by state
				if ( $user->guest ) {
					$query->where('a.state = \'1\'');
				}

				$db->setQuery($query);
				
				if ( !$data = $db->loadObject() ) 
				{
					$this->setError($db->getError());
				}
				else
				{
					// Convert parameter fields to objects.
					$registry = new JRegistry;
					$registry->loadString($data->params);
					$data->params = clone $this->getState('params');
					$data->params->merge($registry);
					
					$registry = new JRegistry;
					$registry->loadString($data->metakey);
					$data->metadata = $registry;
					
					// Compute selected asset permissions.
					$user	= JFactory::getUser();
					
					// Technically guest could edit an article, but lets not check that to improve performance a little.
					if (!$user->get('guest'))
					{
						$userId	= $user->get('id');
						$asset	= 'com_simplecalendar.event.'.$data->id;
						
						// Check general edit permission first.
						$edit = $user->authorise('core.edit', $asset);
						$editOwn = $user->authorise('core.edit.own', $asset);
						
						if ($user->authorise('core.edit', $asset))
						{
							$data->params->set('access-edit', true);
						}
						// Now check if edit.own is available.
						elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
						{
							// Check for a valid user and that they are the owner.
							if ($userId == $data->created_by)
							{
								$data->params->set('access-edit', true);
							}
						}
					}
					
					// Compute access permissions.
					if ($access = $this->getState('filter.access'))
					{
						// If the access filter has been set, we already know this user can view.
						$data->params->set('access-view', true);
					}
					else
					{
						// If no access filter is set, the layout takes some responsibility for display of limited information.
// 						$user = JFactory::getUser();
						$groups = $user->getAuthorisedViewLevels();
						if ($data->catid == 0 || $data->category_access === null)
						{
							$data->params->set('access-view', in_array($data->access, $groups));
						}
						else
						{
							$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
						}
					}
					
					$this->_item[$pk] = $data;
				}
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}
	
	
	/**
	 * Hits the URL, increments the counter
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function hit()
	{
		$id = $this->getState('event.id');
	
		// update hits count
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->update('#__simplecalendar');
		$query->set('hits = (hits + 1)');
		$query->where('id = ' . (int) $id);
	
		$db->setQuery((string) $query);
	
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
		}
	}
}
