<?php
/**
 * @copyright	@copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

// include the syndicate functions only once
require_once(dirname(__FILE__) . '/helper.php');


$class_sfx	= htmlspecialchars($params->get('class_sfx'));
$items		= modSL_SliceboxHelper::getItems($params->get('slides'));

require(JModuleHelper::getLayoutPath('mod_sl_slicebox', $params->get('layout', 'default')));