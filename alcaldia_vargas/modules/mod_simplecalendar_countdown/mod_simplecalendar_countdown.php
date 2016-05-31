<?php
/**
 * Module SimpleCalendar Countdown
 * (c) 2008-2016 Fabrizio Albonico All rights reserved
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

if (!file_exists(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'simplecalendar.php'))
{
	// If the SimpleCalendar component is not installed: quit
	echo JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_ERROR_REQUIRES_SIMPLECALENDAR');
}
else
{
	// Include the helper functions only once
	require_once(dirname(__FILE__) . DS . 'helper.php');
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'helpers' . DS . 'route.php');

	$item = modSimpleCalendarCountdownHelper::getEvent($params);

	require(JModuleHelper::getLayoutPath('mod_simplecalendar_countdown'));
}
?>

