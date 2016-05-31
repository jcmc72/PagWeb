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
 * SimpleCalendar component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_simplecalendar
 * @since       1.6
 */
class SimpleCalendarHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	The name of the active view.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		$uri = (string) JUri::getInstance();
		$return = urlencode(base64_encode($uri));
		
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_CALENDAR'),
			'index.php?option=com_simplecalendar&view=events',
			$vName == 'events'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_simplecalendar',
			$vName == 'categories'
		);
		
		if ($vName == 'categories')
		{
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_simplecalendar')),
				'simplecalendar-categories	');
		}

		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_ORGANIZERS'),
			'index.php?option=com_simplecalendar&view=organizers',
			$vName == 'organizers'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_STATUSES'),
			'index.php?option=com_simplecalendar&view=statuses',
			$vName == 'statuses'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_EDIT_CSS'),
			'index.php?option=com_simplecalendar&view=editcss&return=' . $return,
			$vName == 'editcss'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_IMPORT_EVENTS'),
			'index.php?option=com_simplecalendar&view=importevents',
			$vName == 'importevents'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLECALENDAR_SUBMENU_SETTINGS'),
			'index.php?option=com_config&view=component&component=com_simplecalendar&return=' . $return,
			$vName == 'configuration'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 *
	 * @return  JObject
	 * @since   3.0
	 */
	public static function getActions($categoryId = 0, $eventId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		
		if ( empty($eventId) && empty($categoryId) )
		{
			$assetName = 'com_simplecalendar';
			$level = 'component';
		}
		elseif ( empty($eventId) )
		{
			$assetName = 'com_simplecalendar.category.' . (int) $categoryId;
			$level = 'category';
		}
		else
		{
			$assetName = 'com_simplecalendar.event.' . (int) $eventId;
			$level = 'event';
		}

		$actions = JAccess::getActions('com_simplecalendar', $level);

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
		
		return $result;
	}
	
	/**
	 * * Returns the array type for the Venue filter drop-down
	 * @return array
	 */
	public static function getVenueOptions()
	{
		$options = array();
	
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('DISTINCT a.venue As value, a.venue As text');
		$query->from('#__simplecalendar AS a');
		$query->order('a.venue');
	
		// Get the options.
		$db->setQuery($query);
	
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		
		return $options;
	}
	
	/**
	 * * Returns the array type for the Organizer drop-down
	 * @return array
	 */
	public static function getOrganizerOptions()
	{
		$options = array();
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('DISTINCT a.id As value, a.name As text');
		$query->from('#__simplecalendar_organizers AS a');
		$query->order('a.ordering ASC');
	
		// Get the options.
		$db->setQuery($query);
	
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		array_unshift($options, JHTML::_('select.option', '', JText::_('JOPTION_DO_NOT_USE')));
		return $options;
	}
	
	/**
	 * * Returns the array type for the Status drop-down
	 * @return array
	 */
	public static function getStatusOptions()
	{
		$options = array();
	
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('DISTINCT a.id As value, a.name As text');
		$query->from('#__simplecalendar_statuses AS a');
		$query->order('a.ordering ASC');
	
		// Get the options.
		$db->setQuery($query);
	
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		array_unshift($options, JHTML::_('select.option', '', JText::_('JOPTION_DO_NOT_USE')));
		return $options;
	}

	
	/**
	 * * Returns the array type for the Events drop-down
	 * @return array
	 */
	public static function getEventsOptions()
	{
		$options = array();
	
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('DISTINCT a.id As value, CONCAT(CASE WHEN a.state <> \'1\' THEN \'*\' ELSE \'\' END, a.name, \' (\', a.start_dt, \')\') As text');
		$query->from('#__simplecalendar AS a');
		$query->order('a.start_dt ASC, a.start_time ASC');
	
		// Get the options.
		$db->setQuery($query);
	
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		array_unshift($options, JHTML::_('select.option', '', JText::_('JOPTION_DO_NOT_USE')));
		return $options;
	}
	
	/**
	 * Returns the array type for the Period filter drop-down
	 * @return array
	 */
	public static function getPeriodOptions() {
		$options = array();
		$options[] = JHTML::_('select.option', 'upcoming', JText::_('COM_SIMPLECALENDAR_SIDEBAR_SELECT_PERIOD_UPCOMING'));
		$options[] = JHTML::_('select.option', 'past', JText::_('COM_SIMPLECALENDAR_SIDEBAR_SELECT_PERIOD_PAST'));
		$options[] = JHTML::_('select.option', 'all', JText::_('COM_SIMPLECALENDAR_SIDEBAR_SELECT_PERIOD_ALL'));
		
		return $options;
	}
}
