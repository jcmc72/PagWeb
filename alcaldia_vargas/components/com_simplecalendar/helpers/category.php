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
 * Contact Component Category Tree
 *
 * @package     Joomla.Site
 * @subpackage  com_simplecalendar
 * @since       1.6
 */
class SimplecalendarCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__simplecalendar';
		$options['extension'] = 'com_simplecalendar';
		$options['statefield'] = 'state';
		parent::__construct($options);
	}
}
