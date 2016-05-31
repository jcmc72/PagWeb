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

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/simplecalendar.php';

/**
 * Organizer Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_simplecalendar
 * @since       3.0
 */
class JFormFieldOrganizer extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Organizer';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   1.6
	 */
	public function getOptions()
	{
		return SimpleCalendarHelper::getOrganizerOptions();
	}
	
	public function getInput()
	{
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');
		$js = "var token = \"" . JSession::getFormToken() . "\";";
		$document->addScriptDeclaration($js);
		
		$document->addScript ( JUri::root() . 'administrator/components/com_simplecalendar/assets/js/fillOrganizerData.js' );
		
		
		return parent::getInput();
	}
}
