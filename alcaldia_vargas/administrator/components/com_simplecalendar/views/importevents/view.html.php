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

JLoader::register('SimpleCalendarHelper', JPATH_COMPONENT.'/helpers/simplecalendar.php');

/**
 * View to edit an event.
 *
 * @subpackage  com_simplecalendar
 */
class SimpleCalendarViewImportevents extends JViewLegacy
{
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		
		parent::display($tpl);
	}

	
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/simplecalendar.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		JToolbarHelper::title( JText::_('COM_SIMPLECALENDAR_MANAGER_IMPORT_EVENTS'), 'sc.png');
	
		$user = JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
	
		JToolbarHelper::apply('importevents.save');
		JToolbarHelper::cancel('importevents.cancel');
	}
}
