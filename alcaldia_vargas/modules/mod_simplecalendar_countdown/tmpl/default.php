<?php
/**
 * Module SimpleCalendar Countdown
 * (c) 2008-2016 Fabrizio Albonico All Rights Reserved
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$itemid = '';

$html = '<p>';
if ($item)
{
	if ($params->get('link_to_event', true))
	{
		$link      = JRoute::_(SimpleCalendarHelperRoute::getEventRoute($item->slug, $item->catslug));
		$eventName = ' <a href="' . $link . '">' . $item->name . '</a>';
	}
	else
	{
		$eventName = $item->name;
	}

	$format = modSimpleCalendarCountDownHelper::getDateformat($params);

	// get the date part descriptions corresponding to the date parts
	// in the differences array ("days" or "day", "hours" or "hour", etc.)
	$desc = modSimpleCalendarCountDownHelper::getDatePartDescription($item->diff, $params);

	// turn the array into a string
	$html .= implode(', ', $desc);

	$show_date = intval($params->get('show_date', 1));
	$html .= ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_UNTIL') . ':<br/>';
	$html .= $eventName;

	if ($show_date)
	{
		$html .= ' (' . JHTML::date($item->start_dt, $format) . ')';
	}
}
$html .= '</p>';

// output
echo $html;

?>