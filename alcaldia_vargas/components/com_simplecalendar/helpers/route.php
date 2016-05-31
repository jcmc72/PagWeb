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
 * SimpleCalendar Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_simplecalendar
 * @since       3.0
 */
abstract class SimpleCalendarHelperRoute
{
	protected static $lookup;
	/**
	 * @param   integer  The route of the contact
	 */
	public static function getEventRoute($id, $catid, $language = 0)
	{
		$needles = array(
			'event'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_simplecalendar&view=event&id=' . $id;
		if ( $catid > 1 )
		{
			$categories = JCategories::getInstance('SimpleCalendar');
			$category = $categories->get($catid);
			
			if ($category)
			{
				$needles['events'] = array_reverse($category->getPath());
// 				$needles['categories'] = $needles['category'];
				$link .= '&catid[]='.$catid;
			}
		}
// 		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
// 		{
// 			$db		= JFactory::getDBO();
// 			$query	= $db->getQuery(true);
// 			$query->select('a.sef AS sef');
// 			$query->select('a.lang_code AS lang_code');
// 			$query->from('#__languages AS a');

// 			$db->setQuery($query);
// 			$langs = $db->loadObjectList();
// 			foreach ($langs as $lang)
// 			{
// 				if ($language == $lang->lang_code)
// 				{
// 					$link .= '&lang='.$lang->sef;
// 					$needles['language'] = $language;
// 				}
// 			}
// 		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getCategoryRoute($catid, $language = 0)
	{
		$link = '';
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('SimpleCalendar')->get($id);
		}

		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			//Create the link
// 			$link = 'index.php?option=com_simplecalendar&view=events&id='.$id; //test
			$link = 'index.php?option=com_simplecalendar&view=events&catid='.$id;
			$needles = array(
				'events' => array($id),
				'catid=' => $id
			);

// 			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
// 			{
// 				$db		= JFactory::getDBO();
// 				$query	= $db->getQuery(true);
// 				$query->select('a.sef AS sef');
// 				$query->select('a.lang_code AS lang_code');
// 				$query->from('#__languages AS a');

// 				$db->setQuery($query);
// 				$langs = $db->loadObjectList();
// 				foreach ($langs as $lang)
// 				{
// 					if ($language == $lang->lang_code)
// 					{
// 						$link .= '&lang='.$lang->sef;
// 						$needles['language'] = $language;
// 					}
// 				}
// 			}

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid='.$item;
			}
			else
			{
				if ($category)
				{
					$catids = array_reverse($category->getPath());
					$needles = array(
						'category' => $catids,
						'categories' => $catids
					);
					if ($item = self::_findItem($needles))
					{
						$link .= '&Itemid='.$item;
					}
					elseif ($item = self::_findItem())
					{
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}

	public static function getFormRoute($id)
	{
		//Create the link
		if ($id)
		{
			$link = 'index.php?option=com_simplecalendar&view=form&task=event.edit&id='. $id;
		}
		else
		{
			$link = 'index.php?option=com_simplecalendar&view=form&task=event.add';
		}
	
		return $link;
	}
	
	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';
		
		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_simplecalendar');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if ( isset($item->query['id']) )
					{

						// here it will become a bit tricky
						// language != * can override existing entries
						// language == * cannot override existing entries
						if ( is_array($item->query['id']) )
						{
							$item->query['id'] = $item->query['id'][0];
						}
						if ( $item->query['id'] == '' )
						{
							$item->query['id'] = 0;
						}
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		if ( $needles )
		{
			foreach ($needles as $view => $ids)
			{
				if ( isset(self::$lookup[$language][$view]) )
				{
					foreach ($ids as $id)
					{
						// Events list with category id (events of a single or multiple category)
						if ( isset(self::$lookup[$language][$view][(int) $id]) )
						{
							return self::$lookup[$language][$view][(int) $id];
						}
						// Events list without category id (all events)
						if ( isset(self::$lookup[$language][$view][0]) )
						{
							return self::$lookup[$language][$view][0];
						}
					}
				}
			}
		}

		$active = $menus->getActive();
		if ($active && ($active->language == '*' || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// if not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	}
}