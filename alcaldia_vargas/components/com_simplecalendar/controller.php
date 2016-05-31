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
 
// import Joomla controller library
jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'output.class.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'route.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'simplecalendar.php');
 
/**
 * Ola Component Controller
 */
class SimpleCalendarController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable	= true;	// Huh? Why not just put that in the constructor?
		$user		= JFactory::getUser();
	
		// Set the default view name and format from the Request.
		// Note we are using w_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id    = $this->input->getInt('id');
		$vName = $this->input->get('view', 'events');
		$lName = $this->input->get('layout', 'list');
		$this->input->set('view', $vName);
		$this->input->set('layout', $lName);
	
		if ($user->get('id') || ($this->input->getMethod() == 'POST' && $vName = 'events'))
		{
			$cachable = false;
		}
	
		$safeurlparams = array(
				'id'				=> 'INT',
				'limit'				=> 'UINT',
				'limitstart'		=> 'UINT',
				'filter_order'		=> 'CMD',
				'filter_order_Dir'	=> 'CMD',
// 				'lang'				=> 'CMD'
		);
	
		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_simplecalendar.edit.event', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}
	
		return parent::display($cachable, $safeurlparams);
	}
}