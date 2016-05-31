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

defined('JPATH_BASE') or die;

/**
 * Supports a date and time combo box
 *
 * @package     com_simplecalendar
 * @subpackage  settings
 * @since       3.0
 */
class JFormFieldCustomfield extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'customfield';
	
	/**
	 * Method to get the field label markup.
	 *
	 * @return  string	The field label markup.
	 * @since   3.0
	 */
	protected function getLabel()
	{
		$params = JComponentHelper::getParams('com_simplecalendar');
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'helpers' . DS . 'output.class.php');
		
		$html = array();
		
		$fieldNo = substr($this->id, strlen($this->id)-1, strlen($this->id));
		$paramValue = $params->get('customfield' . $fieldNo .'_label', '');
		if ( $paramValue != '' )
		{
			$html[] = $paramValue;
		}
		else
		{
			$html[] = sprintf(JText::_('COM_SIMPLECALENDAR_CONFIG_CUSTOMFIELD_NOT_DEFINED'), $fieldNo);
		}
		return implode($html);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		JHtml::_('behavior.tooltip');
		
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'helpers' . DS . 'output.class.php');
		
		$html = array();
		
		$fieldNo = substr($this->id, strlen($this->id)-1, strlen($this->id));
		$paramValue = $params->get('customfield' . $fieldNo .'_label', '');
		if ( $paramValue != '' ) 
		{
			$html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
		}
		else
		{
			$html[] = '';
		}
		return implode($html);
	}
}