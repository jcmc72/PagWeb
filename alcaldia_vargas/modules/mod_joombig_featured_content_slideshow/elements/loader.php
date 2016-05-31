<?php

/*------------------------------------------------------------------------
# "joombig featured content slideshow" Joomla module
# Copyright (C) 2013 All rights reserved by joombig.com
# License: GNU General Public License version 2 or later; see LICENSE.txt
# Author: joombig.com
# Website: http://www.joombig.com
-------------------------------------------------------------------------*/


defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldLoader extends JFormField
{
	protected $type = 'Loader';

	function getInput(){
		$document = JFactory::getDocument();
		
		$document->addScript(JURI::root(1) . '/modules/mod_joombig_featured_content_slideshow/assets/jquery-noconflict.js');
		$header_media = $document->addScript(JURI::root(1) . '/modules/mod_joombig_featured_content_slideshow/assets/jscolor.js');
	}
}