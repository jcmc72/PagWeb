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
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Events class for the SimpleCalendar Component
 */
class SimpleCalendarViewEvents extends JViewLegacy 
{
	protected $state;
	
	protected $items;
	
	protected $params;
	
	protected $config;
	
	protected $pagination;
	
	protected $columns = 1;
	
	protected $canDo;
	
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$app 		= JFactory::getApplication();
		$params		= $app->getParams();
		$state		= $this->get('State');
		// Get data from the model
		$items		= $this->get('Items');
		$category	= $this->get('Category');
		$children	= $this->get('Children');
		$parent 	= $this->get('Parent');
		$pagination	= $this->get('Pagination');
		$this->uri	= JURI::getInstance();
		$columns	= json_decode(
			$params->get(
				'columns', 
				'[{"colname":"name","cssclass":"","style":"","caption":"Name"},{"colname":"venue","cssclass":"hidden-phone","style":"","caption":"Venue"},{"colname":"date","cssclass":"","style":"","caption":"Date"}]'
			)
		);

		$document 		= JFactory::getDocument();
		
		$config 		= JComponentHelper::getParams('com_simplecalendar');
		
		$params 		= $app->getParams();
		$uri 			= JFactory::getURI();
		$pathway 		= $app->getPathWay();
		$menu			= $app->getMenu();
		$activeMenu		=  $menu->getActive();
		
		$this->print	= $app->input->get('print', '0');
		
		// Get list ordering default from the parameters
		$menuParams = new JRegistry;
		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}
		$mergedParams = clone $params;
		$mergedParams->merge($menuParams);

		//add css file
		$document->addStyleSheet($this->baseurl.'/components/com_simplecalendar/assets/css/common.css');
		$document->addStyleSheet($this->baseurl.'/components/com_simplecalendar/assets/css/events.css');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		if ($category == false)
		{
			return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}
		
// 		if ($parent == false)
// 		{
// 			return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
// 		}
	
		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		$this->canDo = SimpleCalendarHelper::getActions();
		
		if (!in_array($category->access, $groups))
		{
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		// Check whether we're dealing with an iCal/vCal request
		$vcal = $app->input->get('vcal');
		if ($vcal)
		{
			$tmpl = 'vcal';
			$document->setMetaData('robots', 'noindex, nofollow');
			$this->items = $items;
			$this->params = $params;
			parent::display($tmpl);
		}
		

		$search 	= $app->getUserStateFromRequest( 'com_simplecalendar.search','search','','string' );
		$search		= JString::strtolower( $search );
		$lists['search'] = $search;
		//var_dump($category);
		// Assign data to the view
		$this->items = $items;
		$this->category = $category;
		$this->state = $state;
		$this->pagination = $pagination;
		$this->params = $mergedParams;
 		$this->config = $config;
 		$this->lists = $lists;
		$this->user = $user;
		$this->app = $app;
		$this->columns = $columns;
		
		$this->list_style = $mergedParams->get('list_style', '') != '' ? $mergedParams->get('list_style', '') : $app->input->get('list_style', 'list');
		
		// Display the template
		$this->_prepareDocument();
		parent::display($tpl); 
	}
	
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;
		$subtitle 	= null;
		$this->children	= $this->get('Children');
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		
		if ( $menu )
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SIMPLECALENDAR_DEFAULT_PAGE_TITLE'));
		}

		$id = (int) @$menu->query['id'];
// 		$id = (int) $app->input->get('catid', '');

		$path = array();
		$subtitle = array();
		
		if ($menu && ($menu->query['option'] != 'com_simplecalendar' || $menu->query['view'] == 'events' || $id != $this->category->id))
		{
			$category = JCategories::getInstance('SimpleCalendar')->get($this->category->id);

			$currentCategory = $this->category;
			while ( $category && ($menu->query['option'] != 'com_simplecalendar' || $menu->query['view'] == 'events' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $currentCategory->title, 'link' => SimpleCalendarHelperRoute::getCategoryRoute($currentCategory->id));
				$category = $currentCategory->getParent();
				$subtitle[] = $currentCategory->title;
				$currentCategory = $category;
			}
			$path = array_reverse($path);
			$subtitle = array_reverse($subtitle);
			
			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}
		
		$title = $this->params->get('page_title', '');
		$subtitleText = implode(' > ', $subtitle);
		
		if ( !empty($subtitleText) )
		{
			$title .= ' / ' . $subtitleText;
		}
		$this->params->set('page_subtitle', $subtitleText);
		$this->params->set('page_heading', $title);
		
		
		
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
		$this->document->setTitle($title);

		if ($this->category->metadesc)
		{
			$this->document->setDescription($this->category->metadesc);
		}
		elseif (!$this->category->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->category->metakey)
		{
			$this->document->setMetadata('keywords', $this->category->metakey);
		}
		elseif (!$this->category->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

// 		if ($this->params->get('robots'))
// 		{
// 			$this->document->setMetadata('robots', $this->params->get('robots'));
// 		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			if ($this->params->get('robots'))
			{
				$this->document->setMetadata('robots', $this->params->get('robots'));
			}
		}

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->category->getMetadata()->get('author'));
		}

		$mdata = $this->category->getMetadata()->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}

		// Add alternative feed link
		if ($this->params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}