<?php
/*------------------------------------------------------------------------
# mod_newscalendar - News Calendar
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# Copyright (C) 2010 www.joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');

abstract class modNewsCalendarHelper
{ 
    public static function getCal(&$params)
    {
		$cal = new JObject();
		
		$curmonth=(int) JRequest::getVar('month',($params->get("defmonth")?$params->get("defmonth"):date('n')));
		$curyear=(int) JRequest::getVar('year',($params->get("defyear")?$params->get("defyear"):date('Y')));
		 
		$dayofmonths=array(31,(!($curyear%400)?29:(!($curyear%100)?28:(!($curyear%4)?29:28)) ), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		
		$dayofmonth = $dayofmonths[$curmonth-1];
		$day_count = 1;
		$num = 0;

		$weeks = array();
		for($i = 0; $i < 7; $i++)
		{
			$a=floor((14-$curmonth)/12);
			$y=$curyear-$a;
			$m=$curmonth+12*$a-2;
			$dayofweek=($day_count+$y+floor($y/4)-floor($y/100)+floor($y/400)+floor((31*$m)/12)) % 7;
			$dayofweek = $dayofweek - 1 - $params->get("firstday");
			if($dayofweek <= -1) $dayofweek =$dayofweek + 7;


			if($dayofweek == $i)
			{
				$weeks[$num][$i] = $day_count.' 0';
				$day_count++;
			}
			else
			{
				$weeks[$num][$i] = ($dayofmonths[$curmonth!=1?($curmonth-2):(11)]-($dayofweek-1-$i)).' 1';
			}
		}

		while(true)
		{
			$num++;
			for($i = 0; $i < 7; $i++)
			{
				if ($day_count > $dayofmonth) {
					$weeks[$num][$i] = ($day_count-$dayofmonths[$curmonth-1]).' 1';
				} elseif ($day_count <= $dayofmonth) {
					$weeks[$num][$i] = $day_count.' 0';
				}
				$day_count++;
	  
				if($day_count > $dayofmonth && $i==6) break;
			}
			if($day_count > $dayofmonth && $i==6) break;
		}
		
		if (!$params->get('ajaxed')) {
			$ajaxed = 0;	
		} else {
			$ajaxed = 1;	
		}
		
		$monthname = 'MOD_NEWSCALENDAR_MONTHNAME_' . $params->get( "submonthname" ) . '_' . $curmonth;
		$monthname = modNewsCalendarHelper::encode($monthname,$params->get('encode'),$ajaxed);
		
		$cal->items = modNewsCalendarHelper::getList($params, $curmonth, $curyear );
		$cal->weeks = $weeks;
		$cal->curmonth = $curmonth;
		$cal->curyear = $curyear;
		$cal->monthname = $monthname;
		$cal->dayofmonths = $dayofmonths;
		$cal->ajaxed = $ajaxed;
		
		return $cal;
    }
	
	public static function getList(&$params, $curmonth, $curyear)
	{
		$db = JFactory::getDbo();

		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		$limit = (int) $params->get('count', 0);
		if ( $limit ) {
			$model->setState('list.start', 0);
			$model->setState('list.limit', $limit);
		}

		$model->setState('filter.published', 1);

		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		$catids  = $params->get('catid', array());
		$usedate = $params->get('usedate', 'created');
		$state   = $params->get('state', 1);
		
		if ( $catids ) {
		
			if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();
	
				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);
	
					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition) {
								$additional_catids[] = $category->id;
							}
	
						}
					}
				}
	
				$catids = array_unique(array_merge($catids, $additional_catids));
			}
			
			$model->setState('filter.category_id', $catids);
			
		}

		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}

		$model->setState('filter.language',$app->getLanguageFilter());

		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
		);
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
		$dir = 'DESC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);
	
		$nullDate	= $db->Quote($db->getNullDate());
		
		$startDateRange = $curyear . '-' . $curmonth . '-01 00:00:00';
		$endDateRange   = $curyear . '-' . ($curmonth + 1) . '-01 00:00:00';
		if ( $curmonth == 12 ) {
			$endDateRange = ($curyear + 1) . '-01-01 00:00:00';
		}
		
		if ( $state == 3 ) {
			$published = array(1,2);	
		} else {
			$published = $state;
		}
			
		$model->setState('filter.date_field', 'a.'.$usedate);	
		$model->setState('filter.date_filtering', 'range');
		$model->setState('filter.start_date_range', $startDateRange);
		$model->setState('filter.end_date_range', $endDateRange);
		$model->setState('filter.published', $published);

		$items = $model->getItems();
		
		$calitems = array();

		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				$link = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug);
				if ($params->get('remmonth',0)) {
					$link .= '&month='.$curmonth.'&year='.$curyear;	
				}
			} else {
				$link = 'index.php?option=com_users&view=login';
			}
			
			$item->link = JRoute::_($link);
			
			switch ($usedate) {
				case 'publish_up':
					$item->day = JHtml::_('date',$item->publish_up, 'j');
				break;
				case 'modified':
					$item->day = JHtml::_('date',$item->modified, 'j');
				break;
				case 'publish_down':
					$item->day = JHtml::_('date',$item->publish_down, 'j');
				break;
				default:
					$item->day = JHtml::_('date',$item->created, 'j');
			}
			
			$calitems[$item->day][] = $item;
		}
		
		return $calitems;
	}

	public static function encode($text,$encode,$ajaxed)
    {
		if ($encode!='UTF-8' && $ajaxed) { 
			$text=iconv("UTF-8", $encode, JText::_($text));
		}
		else {
			$text=JText::_($text);
		}
		return $text;
    }
}
