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
defined( '_JEXEC' ) or die( 'Restricted access');
// jimport( 'joomla.application.component.model');

// Base this model on the backend version.
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/event.php';

class SimplecalendarModelForm extends SimplecalendarModelEvent
{

	/**
	 * Get the return URL.
	 *
	 * @return  string	The return URL.
	 * @since   1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
	
		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('event.id', $pk);
	
		$this->setState('event.catid', $app->input->getInt('catid'));
	
		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));
	
		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);
	
		$this->setState('layout', $app->input->get('layout'));
		$this->setState('view', $app->input->get('view'));
// 		$this->setState('view', 'form');
	}
	
	/**
	 * Method to get event data.
	 *
	 * @param   integer	The id of the event.
	 *
	 * @return  mixed  Content item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('event.id');
	
		// Get a row instance.
		$table = $this->getTable();
	
		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}
	
		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');
	
		// Convert attrib field to Registry.
		$value->params = new JRegistry;
// 		$value->params->loadString($value->attribs);
	
		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_simplecalendar.event.'.$value->id;
	
		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by)
			{
				$value->params->set('access-edit', true);
			}
		}
	
		// Check edit state permission.
		if ($itemId)
		{
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			$catId = (int) $this->getState('event.catid');
	
			if ($catId)
			{
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_simplecalendar.category.'.$catId));
				$value->catid = $catId;
			}
			else {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_simplecalendar'));
			}
		}
	
		return $value;
	}
	
	
	public function getForm($data=array(), $loadData=true) 
	{
		$parentForm = parent::getForm($data, $loadData);

		if ( 1 != 0 ) 
		{
			$ok = $parentForm->removeField('captcha', 'captcha');
		}
		return $parentForm;		
	}
}


?>
