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
class JFormFieldDate extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'date';

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
		
		$dateformat = $params->get('date_long_format', 'Y-m-d');
		$dateformat = str_replace("Y", "%Y", $dateformat);
		$dateformat = str_replace("m", "%m", $dateformat);
		$dateformat = str_replace("d", "%d", $dateformat);
		
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		if ( $this->value != null && $this->value != '0000-00-00')
		{
			$date = date($params->get('date_long_format', 'Y-m-d'), strtotime($this->value));
		}
		else
		{
			$date = null;
		}
		$html[] = Jhtml::_('calendar', $date, $this->name, $this->id, $dateformat, 'class="inputbox input-small" size="10" ') . '&nbsp;';
		return implode($html);
	}
}