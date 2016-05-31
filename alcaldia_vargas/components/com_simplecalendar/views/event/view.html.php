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

jimport( 'joomla.application.component.view' );

class SimpleCalendarViewEvent extends JViewLegacy
{
	protected $item;
	
	protected $canDo;
	
	protected $print;
	
	protected $user;
	
	protected $uri;
	
	function display($tmpl = null)
	{
		$app		= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$dispatcher	= JEventDispatcher::getInstance();
		
		$this->item  = $this->get('Item');
		$this->print = $app->input->getBool('print');
		$this->state = $this->get('State');
		$this->user  = $user;
		$this->uri	= JURI::getInstance();
		
		// Add the stylesheet for the frontend
		$document->addStyleSheet( 'components/com_simplecalendar/assets/css/common.css' );
		$document->addStyleSheet( 'components/com_simplecalendar/assets/css/event.css' );
		
		if ( !isset($activeMenu->name) || $activeMenu->name  == '' ) {
			$activeMenu = new JMenu();
			$activeMenu->id = 0;
			$activeMenu->name = JText::_('COM_SIMPLECALENDAR_CALENDAR');
			$this->activeMenu = $activeMenu;
		}
		
		// Merge article params. 
		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$menuParams	= clone ($this->params);
		
		if ( $this->print ) {
			$document->setMetaData('robots', 'noindex, nofollow');
		}
		
		$groups = $user->getAuthorisedViewLevels();
		
		if ( !$this->item )
		{
			$html = '<h2>' . JText::_('COM_SIMPLECALENDAR_WARNING') . '</h2>';
			$html .= '<p>' . JText::_('COM_SIMPLECALENDAR_THERE_IS_NO_SUCH_ITEM_TO_DISPLAY') . '!&nbsp;&nbsp;&nbsp;';
			$html .= '<a href="javascript:history.back()">' . JText::_('COM_SIMPLECALENDAR_BACK_LABEL') . '</a></p>';
			$html .= '<div class="sc-footer">';
			$html .= SCOutput::showFooter();
			$html .= '</div>';
			echo $html;
		}
		else
		{
			// What Access Permissions does this user have? What can (s)he do?
			$this->canDo = SimpleCalendarHelper::getActions($this->item->catid, $this->item->id);
			
			// Check the view access to the event (the model has already computed the values).
			if ($this->item->params->get('access-view', false) != true /* && (($this->item->params->get('show_noauth') != true &&  $user->get('guest') ))*/)
			{
				JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				return;
			}
				
			
			// Check to see which parameters should take priority
			if ( $active )
			{
				$currentLink = $active->link;
				// If the current view is the active item and an article view for this article, then the menu item params take priority
				if (strpos($currentLink, 'view=event') && (strpos($currentLink, '&id='.(string) $this->item->id)))
				{
					// $this->item->params are the event params, $menuParams are the menu item params
					// Merge so that the menu item params take priority
					$this->item->params->merge($menuParams);
					// Load layout from active query (in case it is an alternative menu item)
					if (isset($active->query['layout']))
					{
						$this->setLayout($active->query['layout']);
					}
				}
				else {
					// Current view is not a single article, so the article params take priority here
					// Merge the menu item params with the article params so that the article params take priority
					$menuParams->merge($this->item->params);
					$this->item->params = $menuParams;
			
					// Check for alternative layouts (since we are not in a single-article menu item)
					// Single-article menu item layout takes priority over alt layout for an article
					if ($layout = $this->item->params->get('event_layout'))
					{
						$this->setLayout($layout);
					}
				}
			}
			else
			{
				// Merge so that event params take priority
				$menuParams->merge($this->item->params);
				$this->item->params = $menuParams;
				// Check for alternative layouts (since we are not in a single-article menu item)
				// Single-article menu item layout takes priority over alt layout for an article
				if ($layout = $this->item->params->get('event_layout'))
				{
					$this->setLayout($layout);
				}
			}
			
			$this->item->tags = new JHelperTags;
			$this->item->tags->getItemTags('com_simplecalendar.event', $this->item->id);
			
			// Process the simplecalendar plugins.
			
			$offset = $this->state->get('list.offset');
			JPluginHelper::importPlugin('content');
			JPluginHelper::importPlugin('simplecalendar');
			
			$this->item->text = $this->item->description;
			$this->item->link = JRoute::_(SimpleCalendarHelperRoute::getEventRoute($this->item->slug, $this->item->catslug));
			
			
			$results = $dispatcher->trigger('onContentPrepare', array ('com_simplecalendar.event', &$this->item, &$this->params, $offset));
			
// 			$this->item->event = new stdClass;
// 			$results = $dispatcher->trigger('onContentAfterTitle', array('com_simplecalendar.event', &$this->item, &$this->params, $offset));
// 			$this->item->event->afterDisplayTitle = trim(implode("\n", $results));
			
// 			$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_simplecalendar.event', &$this->item, &$this->params, $offset));
// 			$this->item->event->beforeDisplayContent = trim(implode("\n", $results));
			
// 			$results = $dispatcher->trigger('onContentAfterDisplay', array('com_simplecalendar.event', &$this->item, &$this->params, $offset));
// 			$this->item->event->afterDisplayContent = trim(implode("\n", $results));
				
			$this->item->description = $this->item->text;
			
			$this->app = $app;
			
			// Check whether we're dealing with an iCal/vCal request
			$vcal = $app->input->get('vcal');
			if ($vcal)
			{
				$tmpl = 'vcal';
				$document->setMetaData('robots', 'noindex, nofollow');
				$this->assignRef('item', $this->item);
				$this->assignRef('params', $params);
				
				parent::display($tmpl);
			}
			
			// Increment the hit counter of the event.
			$model = $this->getModel();
			$model->hit();
	
			$this->_prepareDocument();
			parent::display($tmpl);
		}
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;
	
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SIMPLECALENDAR_CALENDAR'));
		}
	
		$title = $this->params->get('page_title', '');
	
		$id = (int) @$menu->query['id'];
	
		// if the menu item does not concern this event
		if ($menu && ($menu->query['option'] != 'com_simplecalendar' || $menu->query['view'] != 'event' || $id != $this->item->id))
		{
			// If this is not a single event menu item, set the page title to the event title
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->item->name, 'link' => ''));
			$category = JCategories::getInstance('SimpleCalendar')->get($this->item->catid);
			while ($category && ($menu->query['option'] != 'com_simplecalendar' || $menu->query['view'] == 'event' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => SimpleCalendarHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}
	
		// Check for empty title and add site name if param is set
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
		if (empty($title))
		{
			$title = $this->item->name;
		}
		$this->document->setTitle($title);
	
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
	
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	
		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->created_by);
		}
	
		$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}
	
		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}
?>