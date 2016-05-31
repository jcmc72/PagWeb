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
 * HTML View class for the Content component
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class SimplecalendarViewEvents extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app       = JFactory::getApplication();
		$doc       = JFactory::getDocument();
		$params    = $app->getParams();
		$feedEmail = $app->getCfg('feed_email', 'author');
		$siteEmail = $app->getCfg('mailfrom');

		// Get some data from the model
		$app->input->set('limit', $app->getCfg('feed_limit'));
		$category = $this->get('Category');
		$rows     = $this->get('Items');

		$doc->link = JRoute::_(SimpleCalendarHelperRoute::getCategoryRoute($category->id));

		foreach ($rows as $row)
		{
			// strip html from feed item title
			$title = $this->escape( $row->name );
			$title = html_entity_decode( $title );
			
			// strip html from feed item category
			$category = $this->escape( $row->catname );
			$category = html_entity_decode( $category );
			
			//Format date
			$date = date( $params->get('date_long_format'), strtotime( $row->start_dt ));
			
			if ( $row->end_dt == null && !$row->end_dt ) {
				$displaydate = $date;
			} else {
				$enddate 	=  date( $params->get('date_long_format'), strtotime( $row->end_dt ));
				$displaydate = $date.' - '.$enddate;
			}
			
			//Format time
			$starttime = '';
			if ( $row->from_time != null )
			{
				$starttime = date( $params->get('time_format'), strtotime( $row->from_time ));
				$displaytime = $starttime;
			}
			if ( $row->to_time != null )
			{
				$endtime = date( $params->get('time_format'), strtotime( $row->to_time ));
				$displaytime = $starttime.' - '.$endtime;
			}
			
			$link = 'index.php?view=event&catid=' . $row->catslug . '&id='. $row->slug;
			$link = JRoute::_( $link );
			
			$description = JText::_( 'COM_SIMPLECALENDAR_EVENT' ).': '.$title.'<br />';
			if ( $row->venue != "")
			{
				$description .= JText::_( 'COM_SIMPLECALENDAR_VENUE' ).': '.$row->venue.'<br />';
			}
			$description .= JText::_( 'COM_SIMPLECALENDAR_CATEGORY' ).': '.$category.'<br />';
			$description .= JText::_( 'COM_SIMPLECALENDAR_DATE' ).': '.$displaydate.'<br />';
			$description .= JText::_( 'COM_SIMPLECALENDAR_TIMES' ).': '.$displaytime.'<br />';
			$description .= JText::_( 'COM_SIMPLECALENDAR_ADDITIONAL_INFORMATION' ).': '. $row->description;
			
			@$created = ( $row->created_dt ? date( 'r', strtotime($row->created_dt) ) : '' );
			
			$feed = new JFeedItem();
			$feed->title 		= $title;
			$feed->link 		= $link;
			$feed->description 	= $description;
			$feed->date			= $created;
			$feed->category   	= $category;
			
			$doc->addItem( $feed );
		}
	}
}
