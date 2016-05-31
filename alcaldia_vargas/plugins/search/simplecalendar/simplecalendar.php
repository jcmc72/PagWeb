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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once(JPATH_SITE.DS.'administrator' . DS . 'components'.DS.'com_simplecalendar'. DS . 'helpers' . DS . 'simplecalendar.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_simplecalendar'. DS . 'helpers' . DS . 'route.php');

/**
 * SimpleCalendar Search plugin
 *
 * @package		SimpleCalendar
*/
class plgSearchSimplecalendar extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access		protected
	 * @param		object	$subject The object to observe
	 * @param		array	$config  An array that holds the plugin configuration
	 * @since		1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * @return array An array of search areas
	 */
	public function onContentSearchAreas()
	{
		static $areas = array(
				'events' => 'PLG_SEARCH_SIMPLECALENDAR'
		);
		return $areas;
	}


	/**
	 * SimpleCalendar Search method
	 *
	 * The sql must return the following fields that are
	 * used in a common display routine: href, title, section, created, text,
	 * browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if restricted to areas, null if search all
	 */
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$user	 = JFactory::getUser();

		// Exit if the search does not include SimpleCalendar
		if (is_array($areas))
		{
			if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas())))
			{
				return array();
			}
		}

		// Make sure we have something to search for
		$text = JString::trim( $text );
		if ($text == '')
		{
			return array();
		}
		
		// load search limit from plugin params
		$limit = $this->params->def('search_limit', 50);
		$date_format = $this->params->def('date_format', '%d.%m.%Y');

		// Get the component parameters
		jimport('joomla.application.component.helper');
		$scParams = JComponentHelper::getParams('com_simplecalendar');

		$wheres = array();

		// Create the search query
		$db = JFactory::getDBO();

		switch ($phrase)
		{

			case 'exact':
				$text	= $db->quote( '%'.$db->getEscaped( $text, true ).'%', false );

				$where	= "((LOWER(a.name) LIKE $text)" .
				" OR (LOWER(a.venue) LIKE $text)" .
				" OR (LOWER(a.description) LIKE $text))";
				break;

			default:
				$words	= explode( ' ', $text );
				
				$wheres = array();
				foreach ($words as $word)
				{
					$word		= $db->quote( '%'.$db->escape( $word, true ).'%', false );
					$wheres2	= array();
					$wheres2[]	= "LOWER(a.name) LIKE $word";
					$wheres2[]	= "LOWER(a.venue) LIKE $word";
					$wheres2[]	= "LOWER(a.description) LIKE $word";
					$wheres[]	= implode( ' OR ', $wheres2 );
				}
				$where	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		
		// Set up the sorting
		switch ( $ordering )
		{
			case 'oldest':
				$order = 'a.start_dt ASC';
				break;

			case 'newest':
				$order = 'a.start_dt DESC';
				break;

			case 'alpha':
			default:
				$order = 'a.name DESC';
		}

		// Load the permissions functions
		$user = JFactory::getUser();
		$user_levels = implode(',', array_unique($user->getAuthorisedViewLevels()));

		// Construct and execute the query
		$query = $db->getQuery(true);
		$query->select('a.name, a.venue, a.description, a.start_dt, a.created_dt, c.title');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug');
		$query->select('CONCAT( "SimpleCalendar - ", DATE_FORMAT(a.start_dt, "'. $date_format . '"), " in ", c.title) AS section');
		$query->select('CONCAT( a.name, " - ", a.venue) AS title');
		$query->select('"1" AS browsernav');
		$query->from('#__simplecalendar AS a');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$query->where("( $where ) AND a.state = 1");
		$query->where('a.access IN ('.$user_levels.') AND c.access IN ( '. $user_levels .')');
		$query->order($order);
		
		// See if we are done
		//Set query
		$db->setQuery( $query, 0, $limit );
		$rows = $db->loadObjectList();
	
		//The 'output' of the displayed link
		foreach($rows as $key => $row)
		{
			$rows[$key]->created = $row->created_dt;
			$rows[$key]->href = JRoute::_( SimpleCalendarHelperRoute::getEventRoute($row->slug, $row->catslug) );
		}
	
		//Return the search results in an array
		return $rows;
	}

}
