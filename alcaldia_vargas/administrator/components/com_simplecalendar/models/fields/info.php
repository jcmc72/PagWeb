<?php
use GCore\Extensions\Chronoforums\Helpers\Elements;
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
 * displays the information panel for SimpleCalendar
 *
 * @package     com_simplecalendar
 * @subpackage  settings
 * @since       3.0
 */
class JFormFieldInfo extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'info';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'helpers' . DS . 'output.class.php');
		$document = JFactory::getDocument();
		
		$html = array();
		
		$html[] = '<h2>' . SCOutput::getComponentName() . ' ' . SCOutput::getComponentVersion() . '</h2>';
		if ( SCOutput::isDevelopment() )
		{
			$html[] = '<h3>Development version - Please do not use in a productive environment!</h3>';
			$html[] = '<p></p>';
		}
		$html[] = '<p><a href="http://software.albonico.ch/" target="_blank">http://software.albonico.ch/</a></p>';
		$html[] = '<p><a href="http://www.facebook.com/Simplecalendar" target="_blank">' .
				JHTML::_('image', 'components/com_simplecalendar/assets/images/like_fb.png', JText::_( 'COM_SIMPLECALENDAR_PLEASE_LIKE_US' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_PLEASE_LIKE_US' )))
				.'</a>';
		$uri 	= JFactory::getUri();
		preg_match('/http(s)*:\/\/(.*?)\//i', $uri->root(), $matches);
		$domainWithoutPort = explode(':', $matches[2]);
		if ( !SCOutput::isValidated() )
		{
			echo SCOutput::validateInstallText();
			$html[] = '<p>Validation key available <a href="http://software.albonico.ch/shop/product/view/1/2.html">here</a>.</p>';
		}
		else 
		{
			$html[] = '<p>This installation is valid - thank you!</p>';
		}
		return implode($html);
	}
}