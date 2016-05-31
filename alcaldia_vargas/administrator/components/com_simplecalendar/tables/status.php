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
 * @subpackage  com_simplecalendar
 * @since       3.0
 */
class SimplecalendarTableStatus extends JTable
{
	/**
	 * Constructor
	 *
	 * @since   1.5
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__simplecalendar_statuses', 'id', $_db);
		$date = JFactory::getDate();
		$this->created_dt = $date->toSql();
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
		if (empty($this->alias))
		{
			$this->alias = JApplication::stringURLSafe($this->name);
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
		
		if (empty($this->id))
		{
			// Existing item
			$this->created_dt	= $date->toSql();
			$this->created_by	= $user->get('id');
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Existing item
			$this->modified_dt	= $date->toSql();
			$this->modified_by	= $user->get('id');
			
			// Get the old row
			$oldrow = JTable::getInstance('Status', 'SimplecalendarTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Verify that the alias is unique
			$table = JTable::getInstance('Status', 'SimplecalendarTable');
			if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
			{
				$this->setError(JText::_('COM_SIMPLECALENDAR_ERROR_UNIQUE_ALIAS'));
				return false;
			}

			// Store the new row
			parent::store($updateNulls);
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
		$table = JTable::getInstance('Status', 'SimplecalendarTable');
		
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
}
