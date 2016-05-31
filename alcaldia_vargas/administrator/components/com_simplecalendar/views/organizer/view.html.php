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
class SimpleCalendarViewOrganizer extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		//$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= SimpleCalendarHelper::getActions($this->item->id, 0);

		JToolbarHelper::title($isNew ? JText::_('COM_SIMPLECALENDAR_ORGANIZER_NEW_ORGANIZER') : JText::_('COM_SIMPLECALENDAR_ORGANIZER_EDIT_ORGANIZER'), 'banners.png');

		// If not checked out, can save the item.
		if (/* !$checkedOut && */ ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_simplecalendar', 'core.create')) > 0))
		{
			JToolbarHelper::apply('organizer.apply');
			JToolbarHelper::save('organizer.save');

			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2new('organizer.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('organizer.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('organizer.cancel');
		}
		else
		{
			JToolbarHelper::cancel('organizer.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_SIMPLECALENDAR_EDIT_ORGANIZER');
	}

}
