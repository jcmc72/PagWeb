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

class jc_com_simplecalendar extends JCommentsPlugin {

	function getObjectTitle( $id ) {
		// Data load from database by given id
		$db =& JFactory::getDBO();
		$db->setQuery( "SELECT name, id FROM #__simplecalendar WHERE id='$id'");
		return $db->loadResult();
	}

	function getTitles($ids) {
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT id, name FROM #__simplecalendar WHERE id IN (' . implode(',', $ids) . ')' );
		return $db->loadObjectList('id');
	}

	function getObjectLink( $id ) {
		// Itemid meaning of our component
		$_Itemid = JCommentsPlugin::getItemid( 'com_simplecalendar' );
		$db =& JFactory::getDBO();

		$query = 'SELECT a.id, a.start_dt, a.end_dt, a.name, a.venue, b.title, ' .
	    	 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug, '.
			 'CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END AS catslug '.
	         'FROM #__simplecalendar AS a ' .
	    	 'INNER JOIN #__categories AS b ON a.catid = b.id '
	    	 .'WHERE a.id = ' . (int) $id;
	    	 $db->setQuery($query);
	    	 $row = $db->loadObject();

	    	 require_once(JPATH_SITE.DS.'components'.DS.'com_simplecalendar'.DS.'helpers'.DS.'route.php');

	    	 $link = JRoute::_( SimpleCalendarHelperRoute::getEventRoute($row->slug, $row->catslug) );

	    	 // url link creation for given object by id
	    	 return $link;
	}

	function getObjectOwner( $id ) {
		$db = & JFactory::getDBO();
		$db->setQuery( 'SELECT userid, id FROM #__simplecalendar WHERE id = ' . $id );
		return $db->loadResult();
	}
}
?>