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
 * Banner table
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.5
 */
class SimplecalendarTableEvent extends JTable
{
	/**
	 * Constructor
	 *
	 * @since   1.5
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__simplecalendar', 'id', $_db);
		$date = JFactory::getDate();
		$this->created_dt = $date->toSql();
	}

	/**
	 * Function to increment the hits counter.
	 *
	 * @since 3.0
	 */
	public function hits()
	{
		$query = 'UPDATE #__simplecalendar'
				. ' SET hits = (hits + 1)'
						. ' WHERE id = ' . (int) $this->id;

		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 * @see     JTable::check
	 * @since   1.5
	 */
	public function check()
	{
		// Set name
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);

		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if ( empty($this->alias) )
		{
			$this->alias = JApplication::stringURLSafe($this->name);
		}

		// encode date and time
		$date_st = date('Y-m-d', strtotime($this->start_dt));
		$this->start_dt = date('Y-m-d', strtotime($date_st));

		$hours_st = JRequest::getVar('jform_start_time_hours');
		$minutes_st = JRequest::getVar('jform_start_time_minutes');
		$ampm_st = JRequest::getVar('jform_start_time_ampm');

		if ( $hours_st != '' && $minutes_st != '' )
		{
			if ( strtolower($ampm_st) == 'pm' )
			{
				$hours_st = (int)$hours_st + 12;
			}
			$this->start_time = $hours_st . ':' . $minutes_st . ':00';
		}
		else
		{
			$this->start_time = '50:00:00';
		}


		// Check end date for values
		$date_end = date('Y-m-d', strtotime($this->end_dt));
		if ( $date_end == '1970-01-01' || $date_end == '0000-00-00' || $this->end_dt == '' || $this->end_dt == null )
		{
			$this->end_dt = $this->_db->getNullDate();
		}
		else
		{
			$this->end_dt = $date_end;
		}
		// Check end time (if available)
		$hours_end = JRequest::getVar('jform_end_time_hours');
		$minutes_end = JRequest::getVar('jform_end_time_minutes');
		$ampm_end = JRequest::getVar('jform_end_time_ampm');

		if ( $hours_end != '' && $minutes_end != '' )
		{
			if ( strtolower($ampm_end) == 'pm' )
			{
				$hours_end = (int)$hours_end + 12;
			}
			if ( $this->start_dt == $this->end_dt ) {
				if ( $hours_st > $hours_end ) {
						
					$hours_end = $hours_st;
					$minutes_end = $minutes_st;
				}
			}
			$this->end_time = $hours_end . ':' . $minutes_end . ':00';
		}
		else
		{
			$this->end_time = '50:00:00';
		}

		// Check for consistent times


		// Check the reserve date for values
		$reserve_date = date('Y-m-d', strtotime($this->reserve_dt));
		if ( $reserve_date == '1970-01-01' || $reserve_date == '0000-00-00' ||  $this->reserve_dt == '' || $this->reserve_dt == null )
		{
			$this->reserve_dt = $this->_db->getNullDate();
		}

		// Add http:// to website URL if available
		if ( $this->contact_website != '' && 
			substr($this->contact_website, 0, 7) != 'http://' && 
			substr($this->contact_website, 0, 8) != 'https://' )
		{
			$this->contact_website = 'http://' . $this->contact_website;
		}
		return true;
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $hash named array
	 * @return  null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = array())
	{
// 		var_dump('bind in table',$array);
// 		exit;
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
		return parent::bind($array, $ignore);

	}
	/**
	 * Method to store a row
	 *
	 * @param boolean $updateNulls True to update fields even if they are null.
	 */
	public function store($updateNulls = true)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$params	= JComponentHelper::getParams('com_simplecalendar');
		
		$task = JRequest::getVar('task', 'save');
		
		if (empty($this->id))
		{
			// Existing item
			$this->created_dt	= $date->toSql();
			$this->created_by	= $user->get('id');
			if ( $user->get('id') == 0 && $params->get('frontend_approve', '1') == '1' )  
			{
				$this->state = 0;
			}
			
			// Verify that the alias is unique // in case of event copy
			$table = JTable::getInstance('Event', 'SimplecalendarTable');
			if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
			{
				if ( $task == 'save2copy') 
				{
					$this->name 	= $this->name . ' (copy)';
					$this->alias 	= 'copy-of-'.$this->alias;
				}
				else
				{
					$this->alias 	= $this->alias . '-' . substr(md5(date('H:i:s')), 0, 7);
				}
				
			}
			
			
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Existing item
			$this->modified_dt	= $date->toSql();
			$this->modified_by	= $user->get('id');
				
			// Get the old row
			$oldrow = JTable::getInstance('Event', 'SimplecalendarTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Verify that the alias is unique
			$table = JTable::getInstance('Event', 'SimplecalendarTable');
			if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
			{
				$this->setError(JText::_('COM_SIMPLECALENDAR_ERROR_UNIQUE_ALIAS'));
				return false;
			}

			// Store the new row
			parent::store(/* $updateNulls */ false);
			//TODO Joomla error... :( Waiting for fix
			// http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=28322
			// http://forum.joomla.org/viewtopic.php?f=728&t=803071
		}
		return count($this->getErrors()) == 0;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed	An optional array of primary key values to update.  If not
	 *					set the instance property value is used.
	 * @param   integer The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
	 * @param   integer The user id of the user performing the operation.
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Event', 'SimplecalendarTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if (!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			// if ($table->checked_out == 0 || $table->checked_out == $userId)
			// {
			// // Change the state
			$table->state = $state;
			// $table->checked_out = 0;
			// $table->checked_out_time = $this->_db->getNullDate();

			// Check the row
			$table->check();

			// Store the row
			if (!$table->store())
			{
				$this->setError($table->getError());
			}
			// }
		}
		return count($this->getErrors()) == 0;
	}

	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_simplecalendar.event.'.(int) $this->$k;
	}
	
	/**
	 * Redefined asset title, as we support action control
	 */
	protected function _getAssetTitle() {
		$k = $this->_tbl_key;
		return $this->name;
	}

	/**
	 * We provide our global ACL as parent
	 * @see JTable::_getAssetParentId()
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();
		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid))
		{
			// The item has a category as asset-parent
			$assetParent->loadByName('com_simplecalendar.category.' . (int) $this->catid);
		}
		else
		{
			// The item has the component as asset-parent
			$assetParent->loadByName('com_simplecalendar');
		}
		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}
}
