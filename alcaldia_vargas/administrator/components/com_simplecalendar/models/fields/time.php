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
class JFormFieldTime extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'time';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		JHtml::_('behavior.calendar');
		JHtml::_('behavior.tooltip');
		
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');
		
		$ampm = 'am';
		
		// Prepare the hours drop-down. Read 12/24-hour setting from main params
		$hourOptions = array();
		$i = 0;
		$limit = $params->get('use_24h', '1') == '0' ? 12 : 24;
		for ($i = 0; $i < $limit; $i++ )
		{
			$value = (string)$i;
			if ( strlen($i) < 2 )
			{
				$value = '0' . $i;
			}
			$hourOptions[] = JHTML::_('select.option', $value, $value);
		}
		array_unshift($hourOptions, JHTML::_('select.option', '', '--'));
		
		// Prepare the minute drop-down
		$minuteOptions = array();
		$minuteSplit = $params->get('minute_split', '4') == '0' ? 4 : $params->get('minute_split', '4');
		for ($i = 0; $i < $params->get('minute_split', '4'); $i++ )
		{
			$value = (string)((60 / $minuteSplit) * $i);
			if ( strlen($value) < 2 )
			{
				$value = '0' . $value;
			}
			$minuteOptions[] = JHTML::_('select.option', $value, $value);
		}
		array_unshift($minuteOptions, JHTML::_('select.option', '', '--'));
		
		// If it's 12-hours format, prepare the AM/PM drop-down
		if ( $params->get('use_24h') == '0' )
		{
			$ampmOptions = array();
			$ampmOptions[] = JHTML::_('select.option', 'am', JText::_('COM_SIMPLECALENDAR_SETTINGS_AM'));
			$ampmOptions[] = JHTML::_('select.option', 'pm', JText::_('COM_SIMPLECALENDAR_SETTINGS_PM'));
		}
		
		$timeformat = $params->get('time_format', 'H:i');
		$timeformat = str_replace("H", "%H", $timeformat);
		$timeformat = str_replace("i", "%M", $timeformat);
		$timeformat = str_replace("s", "%S", $timeformat);
// 		$format = str_replace("%", "", $format);
		
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		if ( $this->value != null && $this->value != '50:00:00' )
		{
			$hours = date('H', strtotime($this->value));
			$minutes = date('i', strtotime($this->value));
		}
		else
		{
			$hours = null;
			$minutes = null;
		}
		
		if ( $hours != null )
		{
			if ( $params->get('use_24h') == '0' )
			{
				if ( (int)$hours < 12 )
				{
					$ampm = 'am';
				}
				else
				{
					$ampm = 'pm';
				}
				if ( (int)$hours > 12 )
				{
					$hours = (int)((int)$hours - 12);
				}				
			}
			if ( strlen($hours) < 2 ) {
				$hours = '0' . $hours;
			} 
		}
// 		var_dump($hours);
		$html[] = JHTML::_('select.genericlist', $hourOptions, $this->id.'_hours', 'class="inputbox input-small"', 'value', 'text', $hours);
		$html[] = JHTML::_('select.genericlist', $minuteOptions, $this->id.'_minutes', 'class="inputbox input-small"', 'value', 'text', $minutes);
		if ( $params->get('use_24h') == '0' )
		{
			$html[] = JHTML::_('select.genericlist', $ampmOptions, $this->id.'_ampm', 'class="inputbox input-small"', 'value', 'text', $ampm);
		}
		return implode($html);
	}
}