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

JLoader::register('SimpleCalendarHelper', JPATH_COMPONENT.'/helpers/sch.php');

/**
 * View to edit an event.
 *
 * @subpackage  com_simplecalendar
 */
class SimpleCalendarViewEvent extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;
	
	protected $canDo;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo 	= SimpleCalendarHelper::getActions($this->state->get('category'));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		if ( $this->item->end_dt == '0000-00-00' || $this->item->end_dt == '1970-01-01' ) 
		{
			$this->item->end_dt = null;
		}
		if ( $this->item->reserve_dt == '0000-00-00' || $this->item->reserve_dt == '1970-01-01' )
		{
			$this->item->reserve_dt = null;
		}
		
		$this->addToolbar();
		$this->_formJS();
		
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

		JToolbarHelper::title($isNew ? JText::_('COM_SIMPLECALENDAR_EVENT_NEW_EVENT') : JText::_('COM_SIMPLECALENDAR_EVENT_EDIT_EVENT'), 'sc.png');

		if ( ($this->canDo->get('core.edit') || count($user->getAuthorisedCategories('com_simplecalendar', 'core.create')) > 0))
		{
			JToolbarHelper::apply('event.apply');
			JToolbarHelper::save('event.save');

			if ($this->canDo->get('core.create'))
			{
				JToolbarHelper::save2new('event.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('event.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('event.cancel');
		}
		else
		{
			JToolbarHelper::cancel('event.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_SIMPLECALENDAR_EDIT_EVENT');
	}
	
	
	private static function _formJS()
	{
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');
		$array = array('End', 'Reserve');
		foreach ( $array as $field )
		{
			$script = "
					function show" . $field . "Date(curr) {
						document.getElementById('span_" . strtolower($field) . "_dt').style.display='inline';
						document.getElementById('span_" . strtolower($field) . "_dt_checkbox').style.display='none';
						document.getElementById('jform_" . strtolower($field) . "_dt').value = document.getElementById('jform_start_dt').value;";
			if ( $field == 'End' )
			{
				$script .= "
						var sel = document.getElementById('jform_end_time_hours');
						var val = document.getElementById('jform_start_time_hours').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_hours' ).trigger( 'liszt:updated' );
					           break;
					        }
							 
					    }
						var sel = document.getElementById('jform_end_time_minutes');
						var val = document.getElementById('jform_start_time_minutes').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_minutes' ).trigger( 'liszt:updated' );
					           break;
					        }
							 
					    }";
				if ( $params->get('use_24h', '1') == '0' )
				{
					$script .= "var sel = document.getElementById('jform_end_time_ampm');
						var val = document.getElementById('jform_start_time_ampm').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_ampm' ).trigger( 'liszt:updated' );
					           break;
					        }
							 
					    }";
				}
			}
			$script .= "}";
			$document->addScriptDeclaration($script);
		}
	}
}
