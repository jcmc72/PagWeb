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
defined('_JEXEC') or die('Restricted access');

/**
 * SimpleCalendar Module helper
 *
 * @package Joomla
 * @subpackage SimpleCalendar Module
 * @since		0.5
 */

class modSimpleCalendarHelper
{

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	public static function &getList(&$params)
	{
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$document	= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$keywords	= explode(',', $document->getMetaData('keywords'));
		$config 	= JComponentHelper::getParams('com_simplecalendar');
		$model 		= JModelLegacy::getInstance('Events', 'SimpleCalendarModel', array('ignore_request' => true));
		
		
// 		if (JFactory::getUser()->authorise('core.manage'))
// 		{
// 			$user_gid = (int) 3;
// 		}
// 		else
// 		{
// 			if($user->get('id')) 
// 			{
// 		    	$user_gid = (int) 2;
// 			}
// 			else
// 			{
// 				$user_gid = (int) 1;
// 			}
// 		}
		$sort = $params->get( 'sort', 'ASC');
		
		switch ( $params->get( 'period', '1') )
		{
			case '1':
				// upcoming events
				$where = 'a.state = 1 and a.start_dt >= CURDATE()';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
			case '2':
				// past events
				$where = 'a.state = 1 and a.start_dt <= CURDATE()';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
			case '-1':
				// archived events
				$where = 'a.state = -1';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
			case '3':
				// upcoming, featured events
				$where = 'a.state = 1 and a.start_dt >= CURDATE() AND a.featured = 1';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
			case '4':
				// past, featured events
				$where = 'a.state = 1 and a.start_dt <= CURDATE() AND a.featured = 1';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
			case '5':
				// Ongoing events
				$where = 'a.state = 1 and a.start_dt <= CURDATE() AND a.end_dt > CURDATE()';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort . ', a.end_dt';
				break;
			case '6':
				// Ongoing events
				$where = 'a.state = 1 and a.start_dt <= CURDATE() AND a.end_dt > CURDATE() AND a.featured = 1';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort . ', a.end_dt';
				break;
			case '0':
			default:
				// all published events (default)
				$where = 'a.state = 1';
				$order = 'a.start_dt ' . $sort . ', a.start_time ' . $sort;
				break;
		}
		
		
		// Retrieve the categories
		$catid 	= $params->get('catid');
		
		if ($catid)
		{
			// $ids = explode( ',', $catid );
			JArrayHelper::toInteger( $catid );
			$categories = '(c.id=' . implode( ' OR c.id=', $catid ) . ')';
		} 
		else 
		{
			$categories = '';
		}
		
		// Load the permissions functions
		$user = JFactory::getUser();
		$user_levels = implode(',', array_unique($user->getAuthorisedViewLevels()));

		//get $params->get( 'count', '2' ) nr of datasets
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug');
		$query->from('#__simplecalendar AS a');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$query->where("( $where ) AND a.state = 1");
		$query->where('a.access IN ('.$user_levels.') AND c.access IN ( '. $user_levels .')');
		if ( $catid[0] != 0 || $categories == '' )
		{
			$query->where($categories);
		}
		$query->order($order . ' LIMIT '.(int)$params->get( 'count', '2' ));
	
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$i = 0;
		$list = array();
		foreach ( $rows as $row )
		{
			$fields = $params->get( 'fields', '{NAME} ({START_DATE_SHORT})' );
			$date_format = $params->get( 'date_format', 'd.m.Y');
			$time_format = $params->get( 'date_format', 'H:i');
			$link = JRoute::_( SimpleCalendarHelperRoute::getEventRoute($row->slug, $row->catslug));
// 			$link = JRoute::_('index.php?option=com_simplecalendar&view=event&id='.$row->slug.'&Itemid='.JRequest::getInt('Itemid'));
			if ( $params->get( 'show_link', '1') == '1' )
			{
				$name = '<a href="' . $link . '">' . $row->name . '</a>';
			}
			else
			{
				$name = $row->name;	
			}
			
			$options = array(
				'{NAME}' => $name,		
				'{VENUE}' => SCOutput::decodeColumns('venue', $row),
				'{START_DATE}' => JHTML::_('date', $row->start_dt, $date_format),
				'{START_DATE_SHORT}' => JHTML::_('date', $row->start_dt, $config->get('date_format_short')),
				'{END_DATE}' => JHTML::_('date', $row->end_dt, $date_format),
				'{END_DATE_SHORT}' => JHTML::_('date', $row->end_dt, $config->get('date_format_short')),
				'{START_TIME}' => SCOutput::decodeColumns('start_time', $row), 
				'{END_TIME}' => SCOutput::decodeColumns('end_time', $row), 
				'{RESERVE_DATE}' => JHTML::_('date', $row->reserve_dt, $date_format),
				'{CATEGORY}' => SCOutput::decodeColumns('category', $row),
				'{PRICE}' => SCOutput::decodeColumns('price', $row),
				'{AUTHOR}' => SCOutput::decodeColumns('author', $row),
				'{USERNAME}' => SCOutput::decodeColumns('username', $row),
				'{CUSTOM1}' => SCOutput::decodeColumns('custom1', $row),
				'{CUSTOM2}' => SCOutput::decodeColumns('custom2', $row),
				'{LINK}' => '<a href="' . $link . '">(link)</a>',
			);
			$list[$i] = strtr($fields, $options);
			$i++;
		}
		return $list;
	}
}