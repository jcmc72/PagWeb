<?php
/**
 * Module SimpleCalendar Countdown
 * (c) 2008-2016 Fabrizio Albonico All Rights Reserved
 */

jimport('joomla.application.module.helper');

class modSimpleCalendarCountDownHelper
{

	public static function getDateFormat($params)
	{
		$format = $params->get('date_format', 'd.m.Y');

		return $format;
	}

	/**
	 * Returns the date part descriprion (singular/plural form) according to the
	 * value in the date diff array.
	 *
	 * @param $diff_array date diff array
	 *
	 * @return array of descriptions
	 */
	public static function getDatePartDescription($diff_array, $params)
	{
		// 	select singular or plural forms.
		$ret = array();

		if (isset($diff_array['day']))
		{
			if ($params->get('precision', 4) >= 1)
			{
				if ($diff_array['day'] == 1)
				{
					$ret['day'] = $diff_array['day'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_DAY');
				}
				else
				{
					$ret['day'] = $diff_array['day'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_DAYS');
				}
			}
		}

		if (isset($diff_array['hour']))
		{
			if ($params->get('precision', 4) >= 2)
			{
				if ($diff_array['hour'] == 1)
				{
					$ret['hour'] = $diff_array['hour'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_HOUR');
				}
				else
				{
					$ret['hour'] = $diff_array['hour'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_HOURS');
				}
			}
		}

		if (isset($diff_array['minute']))
		{
			if ($params->get('precision', 4) >= 3)
			{
				if ($diff_array['minute'] == 1)
				{
					$ret['minute'] = $diff_array['minute'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_MINUTE');
				}
				else
				{
					$ret['minute'] = $diff_array['minute'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_MINUTES');
				}
			}
		}

		if (isset($diff_array['second']))
		{
			if ($params->get('precision', 4) == 4)
			{
				if ($diff_array['second'] == 1)
				{
					$ret['second'] = $diff_array['second'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_SECOND');
				}
				else
				{
					$ret['second'] = $diff_array['second'] . ' ' . JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_SECONDS');
				}
			}
		}

		return $ret;
	}

	private static function _removeEmptyParts($array, $anchor = 0)
	{
		$compare = array(
			'day'    => $anchor,
			'hour'   => $anchor,
			'minute' => $anchor,
			'second' => $anchor
		);
		$result  = array_diff_assoc($array, $compare);

		return $result;
	}

	/**
	 * Calculates the difference between the event and today's date.
	 *
	 * @param $targetDate the event date (date 1)
	 * @param $targetTime the event time (start_time, if available)
	 * @param $precision  the required precision, from the module parameter (defaults to seconds)
	 *
	 * @return array of the remaining time units, false if the event has already expired.
	 */
	private static function _calculateDateDiff($params, $targetDate, $targetTime = null, $precision = null)
	{

		if (!$targetTime)
		{
			$target_hour = 0;
			$target_min  = 0;
			$target_sec  = 0;
		}
		else
		{
// 			var_dump();
// 			exit;
			list($target_hour, $target_min, $target_sec) = preg_split('/[\:]/', $targetTime);
		}
		list($target_year, $target_month, $target_day) = preg_split('/[\-]/', $targetDate);

		$targetDate = mktime($target_hour, $target_min, $target_sec, $target_month, $target_day, $target_year);

		$today = time();

		$secondsDiff = $targetDate - $today;

		if ($secondsDiff > 0)
		{
			$remainingDay     = floor($secondsDiff / 60 / 60 / 24);
			$remainingHour    = floor(($secondsDiff - ($remainingDay * 60 * 60 * 24)) / 60 / 60);
			$remainingMinutes = floor(($secondsDiff - ($remainingDay * 60 * 60 * 24) - ($remainingHour * 60 * 60)) / 60);
			$remainingSeconds = floor(($secondsDiff - ($remainingDay * 60 * 60 * 24) - ($remainingHour * 60 * 60)) - ($remainingMinutes * 60));

			$remainingDay = abs($remainingDay);
			$array        = array(
				'day'    => $remainingDay,
				'hour'   => $remainingHour,
				'minute' => $remainingMinutes,
				'second' => $remainingSeconds
			);

			$result = array();
			if ($params->get('remove_empty_parts', 0) == 1)
			{
				$result = modSimpleCalendarCountDownHelper::_removeEmptyParts($array);
			}
			else
			{
				$result = $array;
			}

			return $result;
		}
		else
		{
			return false;
		}
	}

	public static function getEvent($params)
	{
		$app = JFactory::getApplication();

		// Load the permissions functions
		$user        = JFactory::getUser();
		$user_levels = implode(',', array_unique($user->getAuthorisedViewLevels()));

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END AS slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug');
		$query->from('#__simplecalendar AS a');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$query->where("a.state = 1");
		$query->where('a.access IN (' . $user_levels . ') AND c.access IN ( ' . $user_levels . ')');

		if ($params->get('count_to_next', 0) == 1)
		{
			$query->where('CASE WHEN a.start_time = \'50:00:00\' THEN CAST(CONCAT( a.start_dt, \'-00:00:00\') AS datetime) ' .
				' WHEN a.start_time IS NULL THEN CAST(CONCAT( a.start_dt, \'-00:00:00\') AS datetime) ' .
				' ELSE CAST(CONCAT( a.start_dt, \'-\', a.start_time) AS datetime) END >= CAST(now() AS datetime)');
			$query->order('a.start_dt ASC, a.start_time ASC LIMIT 1');
		}
		else
		{
			if ($params->get('id', 0) != 0)
			{
				$query->where('a.id = ' . $params->get('id', 0));
			}
			$query->order('a.start_dt ASC, a.start_time ASC');
		}

		$db->setQuery($query);
		$data = $db->loadObjectList();

		if (sizeof($data) == 0)
		{
			echo JText::_('MOD_SIMPLECALENDAR_COUNTDOWN_ERROR_NO_EVENTS');

			return;
		}
		else
		{
			$precision     = $params->get('precision', 0);
			$diff          = modSimpleCalendarCountDownHelper::_calculateDateDiff($params, $data[0]->start_dt, $data[0]->start_time, $precision);
			$data[0]->diff = $diff;

			return $data[0];
		}
	}
}
?>
