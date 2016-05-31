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


// ------------------------------------------------------------
// Routing examples
// ------------------------------------------------------------
/*
 * [menualias]/[category]  					--> category view (events list)
 * [menualias]/[category]/[event] 				--> event view (single event)
 * [menualias]/[event] 							--> event view (single event) // NOT YET HANDLED!
 * [menualias]/[category]/[event]/[layout]	--> specific layout (es. vCal)
 */

defined('_JEXEC') or die;

/**
 * Build the route for the com_simplecalendar component
 *
 * @return  array  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 */
function SimplecalendarBuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$params	= JComponentHelper::getParams('com_simplecalendar');
// 	$advanced = $params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}
	
	// check again
	if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_simplecalendar')
	{
		$menuItemGiven = false;
		unset($query['Itemid']);
	}
	
	if (isset($query['view']))
	{
		$view = $query['view'];
	}
	else
	{
		// we need to have a view in the query or it is an invalid URL
		return $segments;
	}
	
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	// are we dealing with a event that is attached to a menu item?
	if (isset($view) && ($mView == $view) and (isset($query['id'])) and ($mId == (int) $query['id']))
	{
// 		unset($query['view']);
		unset($query['catid']);
		unset($query['id']);
		return $segments;
	}
	
	if ( isset($view) && ($view == 'events' || $view == 'event') )
	{
		unset($query['view']);
		if ( $view == 'events' )
		{
			if ( isset($query['catid']) && is_array($query['catid']) )
			{
				$catid = $query['catid'][0];
			}
			else
			{
				$catid = $query['catid'];
			}
			
			$categories = JCategories::getInstance('SimpleCalendar');
			$category = $categories->get($catid);
			
			if ($category && $category->id != 'root')
			{
				//TODO Throw error that the category either not exists or is unpublished
				$path = array_reverse($category->getPath());
			
				$array = array();
				$array[] = $path[0]; //TODO manage subcategories!!!
				$segments = array_merge($segments, array_reverse($array));
			
			}
		}
		else 
		{
			if ($mId != (int) $query['id'] || $mView != $view)
			{
				if ($view == 'event' && isset($query['catid']))
				{
					if ( is_array($query['catid']) )
					{
						$catid = $query['catid'][0];
					}
					else
					{
						$catid = $query['catid'];
					}
				} 
				elseif (isset($query['id']))
				{
					$catid = $query['id'];
				}
				$menuCatid = $mId;
				$categories = JCategories::getInstance('SimpleCalendar');
				$category = $categories->get($catid);
				if ($category)
				{
					//TODO Throw error that the category either not exists or is unpublished
					$path = array_reverse($category->getPath());
	
					$array = array();
	// 				foreach ($path as $id)
	// 				{
	// 					if ((int) $id == (int) $menuCatid)
	// 					{
	// 						break;
	// 					}
	// 					$array[] = $id;
	// 				}
					$array[] = $path[0]; //TODO manage subcategories!!!
					$segments = array_merge($segments, array_reverse($array));
	
				}
				if ($view == 'event')
				{
					$id = $query['id'];
					$segments[] = $id;
				}
			}
		}
		unset($query['id']);
		unset($query['catid']);
	}
	
	// if the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout']))
	{
		if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
		{
			if ($query['layout'] == $menuItem->query['layout'])
			{

				unset($query['layout']);
			}
		}
		else
		{
			if ($query['layout'] == 'default')
			{
				unset($query['layout']);
			}
		}
	}
	
	return $segments;
}
/**
 * Parse the segments of a URL.
 *
 * @return  array  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 */
function SimpleCalendarParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_simplecalendar');
// 	$advanced = $params->get('sef_advanced_link', 0);

	// Count route segments
	$count = count($segments);
	
	switch ( $count ) {
		case 3:
			$vars['view'] = 'event';
			$vars['catid'] = $segments[0];
			$vars['id'] = $segments[1];
			$vars['format'] = $segments[2];
			break;
		case 2:
			$vars['view'] = 'event';
			$vars['catid'] = $segments[0];
			$vars['id'] = $segments[1];
			break;
		case 1:
		default:
			$vars['view'] = 'events';
			$vars['catid'] = $segments[0];
			break;
		case 0:
			$vars['view'] = 'events';	
			break;
	}

	return $vars;
}
