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
 * View class for a list of banners.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_simplecalendar
 * @since       1.6
 */
class SimpleCalendarViewStatuses extends JViewLegacy
{
	protected $categories;

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		//$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		require_once JPATH_COMPONENT.'/helpers/simplecalendar.php';
		
		SimpleCalendarHelper::addSubmenu('statuses');

		$this->addToolbar();
		// require_once JPATH_COMPONENT . '/models/fields/bannerclient.php';

		// Include the component HTML helpers.
		// JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/simplecalendar.php';

		$canDo = SimpleCalendarHelper::getActions($this->state->get('filter.status_id'));
		$user = JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_SIMPLECALENDAR_MANAGER_STATUSES'), 'sc.png');
		if (count($user->getAuthorisedCategories('com_simplecalendar', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('status.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('status.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::publish('statuses.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('statuses.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1)
			{
				if ($this->state->get('filter.state') != 2)
				{
					JToolbarHelper::archiveList('statuses.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolbarHelper::unarchiveList('statuses.publish');
				}
			}
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'statuses.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('statuses.trash');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_simplecalendar');
		}
		JToolbarHelper::help('JHELP_COMPONENTS_SIMPLECALENDAR_STATUSES');

		JHtmlSidebar::setAction('index.php?option=com_simplecalendar&view=statuses');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
		);

// 		JHtmlSidebar::addFilter(
// 			JText::_('JOPTION_SELECT_CATEGORY'),
// 			'filter_category_id',
// 			JHtml::_('select.options', JHtml::_('category.options', 'com_simplecalendar'), 'value', 'text', $this->state->get('filter.category_id'))
// 		);

		// JHtmlSidebar::addFilter(
			// JText::_('JOPTION_SELECT_LANGUAGE'),
			// 'filter_language',
			// JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		// );
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_SIMPLECALENDAR_STATUS_HEADING_NAME'),
			'a.color' => JText::_('COM_SIMPLECALENDAR_STATUS_HEADING_COLOR'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
