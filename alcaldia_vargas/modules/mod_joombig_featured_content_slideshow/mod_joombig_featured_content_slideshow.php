<?php

/*------------------------------------------------------------------------
# "joombig featured content slideshow" Joomla module
# Copyright (C) 2013 All rights reserved by joombig.com
# License: GNU General Public License version 2 or later; see LICENSE.txt
# Author: joombig.com
# Website: http://www.joombig.com
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Path assignments
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = JURI :: base();
if(substr($mosConfig_live_site, -1)=="/") { $mosConfig_live_site = substr($mosConfig_live_site, 0, -1); }

// get parameters from the module's configuration
$borderWidth = $params->get('borderWidth','0');

$tabNumber = 4;
$enable_jQuery 		= $params->get('enable_jQuery',1);
$enable_jQuery_ui 	= $params->get('enable_jQuery_ui',1);
$auto_load 		= $params->get('auto_load',1);

$style_view 	= $params->get('style_view',0);
$moduleWidth = $params->get('moduleWidth','100');
$moduleHeight = $params->get('moduleHeight','45.5');

$autoplay 	= $params->get('autoplay',1);
$pausetime = $params->get('pausetime','5000');

$display_title 	= $params->get('display_title',1);
$display_des 	= $params->get('display_des',1);
$display_readmore 	= $params->get('display_readmore',1);

for ($loop = 1; $loop <= $tabNumber; $loop += 1) {
$title[$loop] = $params->get('title'.$loop,'');
}

for ($loop = 1; $loop <= $tabNumber; $loop += 1) {
	$image[$loop] = $params->get('image'.$loop,'image'.$loop.'joombig.jpg');
	$imagethumb[$loop] = $params->get('imagethumb'.$loop,'image'.$loop.'joombig.jpg');
	$inforight[$loop] = $params->get('inforight'.$loop,'info right');
	$info[$loop] = $params->get('info'.$loop,'banner images slider .');
	$readmorelink[$loop] = $params->get('readmorelink'.$loop,'');
	$readmoretext[$loop] = $params->get('readmoretext'.$loop,'read more');
}

// get the document object
$doc =  JFactory::getDocument();

require(JModuleHelper::getLayoutPath('mod_joombig_featured_content_slideshow'));