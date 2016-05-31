<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Skyline Software - SliceBox Helper Class.
 *
 * @package		Joomla.Site
 * @subpakage	Skyline.SliceBox
 */
class modSL_SliceBoxHelper {

	public static function getItems($value) {
		$items	= array();

		if (is_array($value)) {
			if (isset($value['image']) && count($value['image'])) {
				for ($i = 0, $n = count($value['image']); $i < $n; $i++) {
					$item			= new stdClass();
					$item->image	= $value['image'][$i];
					$item->link		= $value['link'][$i];
					$item->link_nw	= $value['link_nw'][$i];
					$item->title	= $value['title'][$i];
					$item->text		= $value['text'][$i];

					if ($item->image) {
						$items[]		= $item;
					}
				}
			}
		} else if (is_object($value)) {
			if (isset($value->image) && count($value->image)) {
				for ($i = 0, $n = count($value->image); $i < $n; $i++) {
					$item			= new stdClass();
					$item->image	= $value->image[$i];
					$item->link		= $value->link[$i];
					$item->link_nw	= $value->link_nw[$i];
					$item->title	= $value->title[$i];
					$item->text		= $value->text[$i];

					if ($item->image) {
						$items[]		= $item;
					}
				}
			}
		}


		return $items;
	}
}