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
jimport('joomla.application.component.view');

class SimplecalendarViewOrganizer extends JViewLegacy
{

	function display($tpl = null)
	{

		JRequest::checkToken('get') or die( 'Invalid Token<br><br><br><br><br>' );
		
		$item		= $this->get('Item');

		if ( !empty($item) )
		{
		// Return the array the AJAX call is expecting
			//$text = $item->contact_name . "<br>" . $item->contact_email . "<br>" . $item->contact_website . "<br>" .  $item->contact_telephone . "<br>" . $item->latlon;
			echo $item->contact_name . "<br>" . $item->contact_email . "<br>" . 
				$item->contact_website . "<br>" .  $item->contact_telephone . "<br>" . $item->latlon .
				"<br>" . $item->address;
		} 
		else
		{
			echo "<br><br><br><br><br><br>";
		}
	}
}
?>