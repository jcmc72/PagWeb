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
 * Banner model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class SimplecalendarModelOrganizer extends JModelItem
{
	
	/**
	 * Returns a JTable object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate. [optional]
	 * @param   string  $prefix  A prefix for the table class name. [optional]
	 * @param   array   $config  Configuration array for model. [optional]
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Organizer', $prefix = 'SimplecalendarTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the event item
	 * @return object The event item to be displayed to the user
	 * @since 3.0
	 */
	public function &getItem($id = null)
	{
		$app		= JFactory::getApplication();
		$id = $app->input->get('id');
		$id = (!empty($id)) ? $id : (int) $this->getState('organizer.id');
		$db = $this->getDbo();
		if ($this->_item === null)
		{
			$this->_item = array();
		}
	
		if (!isset($this->_item[$id]))
		{
			try
			{
				$db = $this->getDbo();
	
				$db->setQuery($this->_db->getQuery(true)
						->select('a.*')
						->from('#__simplecalendar_organizers as a')
						->where('a.id=' . (int) $id));
	
				if ( !$data = $db->loadObject() )
				{
					$this->setError($db->getError());
				}
				else
				{
					$this->_item[$id] = $data;
				}
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$id] = false;
				}
			}
		}
	
		return $this->_item[$id];
	}
}
