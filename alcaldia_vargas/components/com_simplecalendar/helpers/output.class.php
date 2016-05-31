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
 * Helper class for all output related issues in SimpleCalendar
 */

class SCOutput {

	// --------------------------------------------------------------------------------
	// Buttons and Icons
	// --------------------------------------------------------------------------------

	/**
	 * Returns the URI string of the page we are looking at.
	 *
	 * @return URI string
	 */
	public static function _getUriString() {
		//TODO: refactor this part to correct SEO/SEF behaviour - no absolute urls, only relative!
		$uri = JFactory::getURI();
		$uriString = $uri->toString();
		return $uriString;
	}

	/**
	 * Shows the button corresponding to the action.
	 * 
	 * @param $type of action
	 * @param $item to be handled (url, etc.)
	 * @return $html string
	 */
	public static function showIcon($type, $item = '', $itemID='', $uri='') {
// 		$params  = $item->params;
		$html = '';
		switch ( strtolower($type) ) {
			case 'back':
				$html = '<a href="javascript:history.back()" title="' . JText::_('COM_SIMPLECALENDAR_BACK_LABEL') . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/arrow_redo.png', JText::_( 'COM_SIMPLECALENDAR_BACK_LABEL' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_BACK_LABEL' ))).'</a>';	
				break;
			
			case 'new':
				$link = JRoute::_( $item );
				$html = '<a href="'. $link .'" title="' . JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ) . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/date_add.png', JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ))).'</a>';
				break;
				
			case 'edit':
				$link = JRoute::_( $item );
				$html = '<a href="'. $link .'" title="' . JText::_('COM_SIMPLECALENDAR_FORM_EDIT_EVENT') . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/date_edit.png', JText::_( 'COM_SIMPLECALENDAR_FORM_EDIT_EVENT' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_FORM_EDIT_EVENT' ))).'</a>';
				break;
			
			case 'printpreview':
				$link = JRoute::_($item);
				$html = '<a href="' . $link . '" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" title="' . JText::_('COM_SIMPLECALENDAR_PRINT_LABEL') . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/printer.png', JText::_( 'COM_SIMPLECALENDAR_PRINT' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_PRINT_LABEL' ), 'class'=>'hidden-phone')).'</a>';
				break;
			
			case 'print':
				$html = '<a href="#" onclick="window.print();return false;" title="' . JText::_('COM_SIMPLECALENDAR_PRINT_LABEL') . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/printer.png', JText::_( 'COM_SIMPLECALENDAR_PRINT_LABEL' ), array()).'</a>';
				break;
				
			case 'rss':
			case 'atom':
				$link = JRoute::_($item);
				$html = '<a href="' . $link . '" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" title="' .  JText::_( 'COM_SIMPLECALENDAR_'.strtoupper($type).'_LABEL' ) . '">';
				$html .= JHTML::_('image', 'images/M_images/livemarks.png', JText::_( 'COM_SIMPLECALENDAR_'.strtoupper($type) ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_'.strtoupper($type) ))).'</a>';
				break;
				
			case 'email':
				$link = JRoute::_('index.php?option=com_mailto&tmpl=component&link='.base64_encode( SCOutput::_getUriString() ));
				$html = '<a href="' . $link . '" onclick="window.open(this.href,\'win2\',\'width=400,height=350,menubar=yes,resizable=yes\'); return false;" title="E-Mail">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/email_go.png', JText::_( 'COM_SIMPLECALENDAR_SEND_EMAIL_LABEL' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_SEND_EMAIL_LABEL' ))).'</a>';
				break;
	
// 			case 'pdf':
// 				$link = JRoute::_($item);
// 				$html  = '<a href="' . $link . '" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" title="PDF">';
// 				$html .= JHTML::_('image', 'images/M_images/pdf_button.png', JText::_( 'PDF' ), array('title'=>JText::_( 'PDF' ))).'</a>';
// 				break;
				
			case 'vcal':
				$link = JRoute::_($item);
				$html = '<a href="' . $link . '" title="' . JText::_( 'COM_SIMPLECALENDAR_VCAL_ICAL_LABEL' ) . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/logo_16.png', JText::_( 'COM_SIMPLECALENDAR_VCAL_ICAL_LABEL' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_VCAL_ICAL_LABEL' ))).'</a>';
				break;
				
			case 'new':
				$link = JRoute::_($item);
				$html = '<a href="'. $link .'" title="' . JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ) . '">';
				$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/date_add.png', JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_ADD_NEW_LABEL' ))).'</a>';
				break;
				
			default:	
		}
		return $html;
	}


	/**
	 * Returns a checkbox
	 * @param $name
	 * @param $value
	 * @return HTML checkbox
	 */
	public static function checkbox($name, $value, $javascript) {
		if ( $value == 1 ) {
			$html = "<input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $value . "\" checked=\"checked\" " . $javascript  . " />";
		} else if ( $value == 0 ) {
			$html = "<input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $value . "\" " . $javascript  . " />";
		} else {
			$html = JText::_('JERROR');
		}
		return $html;
	}

	// --------------------------------------------------------------------------------
	// Header / Footer methods
	// --------------------------------------------------------------------------------

	/**
	 * Reads the component name and version from the XML installation file, and returns a string.
	 * @return string component version;
	 */
	public static function getComponentNameAndVersion()
	{
		$string 	= '';
		$string 	= SCOutput::getComponentName();
		$version 	= SCOutput::getComponentVersion();
		
		if ( substr($version, (count($version)*-1)) == 'a' )
		{
			$string .= ' ' . $version . ' - development release';
		} 
		elseif ( substr($version, (count($version)*-1)) == 'b' )
		{
			$string .= ' ' . $version;
		}
		$texts = array(
			'Powered by <a href="http://software.albonico.ch/" target="_blank">'. $string  .'</a>',
		);
		return '<small>' . $texts[0] . '</small>';
	}
	
	public static function getComponentName()
	{
		$xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_simplecalendar' . DS . 'simplecalendar.xml';
		$data = JApplicationHelper::parseXMLInstallFile($xmlFile);
		$name = $data['name'];
		return JText::_(strtoupper($name));
	}
	
	public static function getComponentVersion()
	{
		$xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_simplecalendar' . DS . 'simplecalendar.xml';
		$data = JApplicationHelper::parseXMLInstallFile($xmlFile);
		$version = $data['version'];
		return $version;
	}
	
	/**
	 * Checks if the component is in development stage
	 * @return boolean true if it is in development, false otherwise
	 */
	public static function isDevelopment() {
		$version 	= SCOutput::getComponentVersion();
		
		if ( substr($version, (count($version)*-1)) == 'a' 
				|| substr($version, (count($version)*-1)) == 'b' )
		{
			return true;
		}
		return false;
	}


	/**
	 * Shows the footer (with credits)
	 * Please do not hack ;)
	 * More information: http://software.albonico.ch/support-my-work
	 *
	 * @return html string
	 */
	public static function showFooter() {
		$html = '';
		if ( !SCOutput::_validateInstall2() )
		{
			$html .= '<span class="sc-footer">';
			$html .= SCOutput::getComponentNameAndVersion();
			$html .= '</span>';
		}
		return $html;
	}

	// --------------------------------------------------------------------------------
	// Date manipulation methods
	// --------------------------------------------------------------------------------

	/**
	 * Returns the singular or plural of "DATE" according to the dates set.
	 *
	 * @param date $date1
	 * @param date $date2
	 * @param date $date3
	 * @return string "date" or "dates"
	 */
	public static function getDatesType($date1, $date2, $date3) {
		$dateText = JText :: _('COM_SIMPLECALENDAR_DATE');
		if (strtotime($date2) > strtotime($date1) || strtotime($date3) > strtotime($date1)) {
			$dateText = JText :: _('COM_SIMPLECALENDAR_DATES');
		}
		return $dateText;
	}

	/**
	 * Returns a formatted date
	 *
	 * @param date $date1
	 * @param date $date2
	 * @param date $date3
	 * @param string $longFormat
	 * @param string $shortFormat
	 * @return date string
	 */
	public static function getFormattedDate($date1, $date2, $date3, $longFormat='', $shortFormat='') {
		$params = JComponentHelper::getParams('com_simplecalendar');
		if ( $longFormat == '' )
		{
			$longFormat = $params->get('date_format_long', 'd.m.Y');
		}
		if ( $shortFormat == '' )
		{
			$shortFormat = $params->get('date_format_short', 'd.m.');
		}
		$dateString = '';
		$strDate1 = $strDate2 = $strDate3 = '';
		if (strtotime($date2) > strtotime($date1))
		{
			$strDate1 = JHTML::_('date', $date1, $shortFormat);
			$strDate2 = JHTML::_('date', $date2, $longFormat);
			$dateString .= $strDate1 . ' - ' . $strDate2;
		}
		else
		{
			$strDate1 = JHTML::_('date', $date1, $longFormat);
			$dateString = $strDate1;
		}
		if (strtotime($date3) >= strtotime($date1))
		{
			$strDate3 = JHTML ::_('date', $date3, $longFormat);
			$dateString .= '<br />('.JText::_('COM_SIMPLECALENDAR_RESERVEDATE').': ' . $strDate3 . ')';
		}
		return $dateString;
	}

	/**
	 * Returns a formatted time
	 *
	 * @param time $from_time
	 * @param time $to_time
	 * @param string $format
	 * @param boolean $short date format
	 * @return time string
	 * @author edited by lostsoul
	 */
	public static function getFormattedTime($from_time, $to_time, $format = '%H:%M', $short = true ) {
		$timeString = '';
		if ( $from_time != NULL && $from_time != '50:00:00') {
			$timeString = JHTML::_('date', $from_time, $format, 'UTC');
			if ( $to_time != NULL && $to_time != '50:00:00' && $to_time > $from_time ) {
				$timeString .= ($short == true) ? ('-') : (' '.JText::_('COM_SIMPLECALENDAR_TO_TIME_LC').' ');
				$timeString .= JHTML::_('date', $to_time, $format, 'UTC');
			}
		}
		return $timeString;
	}

	/**
	 * Counts the days between today and a date
	 *
	 * @param date $date1
	 * @param time $time1
	 * @return integer string
	 */
	public static function countDays($date1, $time1) {
		// catch null times
		if ($time1 == NULL) {
			$time1 = '00:00:00';
		}
		// get today date
		$date =& JFactory::getDate();
		$now  = $date->toMySQL();

		// get the date array
		$gd_today = getdate(strtotime($now));
		$gd_date1 = getdate(strtotime($date1.' '.$time1));

		// get the timestamp of the array
		$date1_ts = mktime($gd_date1['hours'], $gd_date1['minutes'], 0, $gd_date1['mon'], $gd_date1['mday'], $gd_date1['year'] );
		$today_ts = mktime($gd_today['hours'], $gd_today['minutes'], 0, $gd_today['mon'], $gd_today['mday'], $gd_today['year'] );

		// get the result (difference in seconds between today and date1, divided by # of seconds in a day
		$result = round( ($today_ts - $date1_ts) / 86400 );

		// return the result
		return $result;
	}


	// --------------------------------------------------------------------------------
	// ComboBox helpers
	// --------------------------------------------------------------------------------

	/**
	 * returns a combo box with the organizer list
	 *
	 * @return string HTML
	 */
	function getOrganizerComboBox( $selected , $site) {
		$db =& JFactory::getDBO();
		$query = 'SELECT id, name AS title' .
				' FROM #__simplecalendar_organizers' .
				' ORDER BY name';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('COM_SIMPLECALENDAR_SELECT_ORGANIZER').' -', 'id', 'title'));
		$params = JComponentHelper::getParams('com_simplecalendar');
		// if (!isset($showmap))
			// $showmap = '0';
		// if ($params->use_gmap && $params->gmap_api_key != '' ) {
			// $showmap = '1';
		// } else {
			// $showmap = '0';
		// }
		$showmap = '1';
		$onchangetext = "onchange=\"doAjax('$site', '".JURI::base()."', document.getElementById('entryGroupID').value, '" . JText::_('COM_SIMPLECALENDAR_ASK_OVERWRITE_GROUP_INFO'). "', '$showmap')\"";

		return JHTML::_('select.genericlist', $options , 'gid', 'class="inputbox" size="1" ' . $onchangetext, 'id', 'title', $selected);
	}


	// --------------------------------------------------------------------------------
	// Category helpers
	// --------------------------------------------------------------------------------

	/**
	 * Shows a coloured bar in the list view. Can be either the category colour, or the status colour
	 * @param string $color
	 * @param string $title
	 * @param string $text
	 * @param string $width
	 * @return string html
	 */
	public static function showCategoryColor( $color, $title, $text = '', $width = '5px') {
		JHTML::_('behavior.tooltip');
		if ( $text == '' ) 
		{
			$text = JText::_('COM_SIMPLECALENDAR_CATEGORY');
		}
		$html = '<div style="height:100%; width: '.$width.'; background-color: #'.$color.';">'. JHTML::tooltip($title, $text, '', '&nbsp;') .'</div>';
		return $html;
	}

	// --------------------------------------------------------------------------------
	// File download helper
	// --------------------------------------------------------------------------------

	/**
	 * Helper to return the file size with the most appropriate size unit
	 *
	 * @since 0.7
	 * @param string $size
	 * @return string formatted size with unit
	 */
	function _getFileSize($size) {
		if ( (int) $size < 1024 ) {
			return (int) $size . ' bytes';
		} else if ( (int) $size >= 1024 && (int) $size < 1048576 ) {
			return (int) ($size/1024) . ' kB';
		} else if ( (int) $size >= 1048576 ) {
			return (int) ($size/1048576) . ' MB';
		}
		return '';
	}

	/**
	 * Returns the file Description and download link - checking the extension against its Mime Type
	 *
	 * @since 0.7
	 * @param string $file filename (incl. mime type and size info)
	 * @param string $link full path to file
	 * @param string $description the description of the file
	 * @return string $html the complete download link.
	 */
	function showFileDescription ( $file, $link, $description ) {
		$html = '';
		$filepart = explode('|', $file);
		if ( $description == '' ) {
			$html .= '<a href="'. JRoute::_($link). '">' . $filepart[0] . '</a>';
		} else {
			$html .= '<a href="'. JRoute::_($link). '">' . $description . '</a>';
		}
		$filetype = SCUpload::extensionFromMime($filepart[1]);
		$html .= ' (' . strtoupper($filetype) . ', ' . SCOutput::_getFileSize($filepart[2]) . ')';
		return $html;
	}

	// --------------------------------------------------------------------------------
	// Frontend list view - column helper functions
	// --------------------------------------------------------------------------------

	/**
	 * Decodes the column specifier in the columns list
	 *
	 * @since 0.7.4
	 * @param string $field (can be 'date' or 'time')
	 * @param object $array item
	 * @return string html
	 */
	public static function decodeColumns($field, $array) {
		$params = JComponentHelper::getParams('com_simplecalendar');
		if (!isset($html)) {
			$html = '';
		}
		
		$user = JFactory::getUser($array->created_by);

		switch ($field) {
// 			case 'category_color':
// 				$html .= SCOutput::showCategoryColor($array->category_color, $array->categoryName, 'Category');
// 				break;
			case 'date':
				$html .= SCOutput::getFormattedDate($array->start_dt, $array->end_dt, $array->reserve_dt, $params->get('date_long_format'), $params->get('date_short_format'));
				break;
			case 'time':
				$html .= SCOutput::getFormattedTime($array->start_time, $array->end_time, $params->get('time_format'), true);
				break;
			case 'price':
				if ( $params->get('currency') != '' && $array->price != '' )
				{
					$html .= $params->get('currency') . ' ' . $array->price;
				}
				else if ( $array->price != '' ) 
				{
					$html .= $array->price;
				} 
				break;
			case 'name':
				$link = JRoute::_(SimpleCalendarHelperRoute::getEventRoute($array->slug, $array->catid));
				$html .= '<a href="'.$link.'">' . $array->name . '</a>';
				// add unpublished / trashed event icons for logged-in administrators
				if ( $array->state == '0' )
				{
					$html .= '&nbsp;' . JHTML::_(
						'image',
						'components/com_simplecalendar/assets/images/delete.png',
						JText::_( 'COM_SIMPLECALENDAR_EVENT_UNPUBLISHED' ),
						array('title' => JText::_( 'COM_SIMPLECALENDAR_EVENT_UNPUBLISHED' ))
					);
				} 
				else if ( $array->state != '0' && $array->state != '1' )
				{
					$html .= '&nbsp;' . JHTML::_(
							'image',
							'components/com_simplecalendar/assets/images/bin_closed.png',
							JText::_( 'COM_SIMPLECALENDAR_EVENT_DELETED_OR_ARCHIVED' ),
							array('title' => JText::_( 'COM_SIMPLECALENDAR_EVENT_DELETED_OR_ARCHIVED' ))
					);
				}
				break;
			case 'name_nolink':
				$html .= $array->name;
				// add unpublished / trashed event icons for logged-in administrators
				if ( $array->state == '0' )
				{
					$html .= '&nbsp;' . JHTML::_(
							'image',
							'components/com_simplecalendar/assets/images/delete.png',
							JText::_( 'COM_SIMPLECALENDAR_EVENT_UNPUBLISHED' ),
							array('title' => JText::_( 'COM_SIMPLECALENDAR_EVENT_UNPUBLISHED' ))
					);
				}
				else if ( $array->state != '0' && $array->state != '1' )
				{
					$html .= '&nbsp;' . JHTML::_(
							'image',
							'components/com_simplecalendar/assets/images/bin_closed.png',
							JText::_( 'COM_SIMPLECALENDAR_EVENT_DELETED_OR_ARCHIVED' ),
							array('title' => JText::_( 'COM_SIMPLECALENDAR_EVENT_DELETED_OR_ARCHIVED' ))
					);
				}
				break;
			case 'status':
				$html .= '<small><span style="color:#'. $array->status_color . '">';
				$html .= $array->status_name;
				$html .= '</span></small>';
				break;
			case 'author':
				$html .= $user->name;
				break;
			case 'username':
				$html .= $user->username;
				break;
			case 'attachment':
				if ( file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'attachments.php') ) {
					$db	= JFactory::getDBO();
					$currentUser = JFactory::getUser();
					$query = "SELECT id FROM #__attachments WHERE parent_type = 'com_simplecalendar' AND parent_id = " . $array->id ;
					if ( $currentUser->guest ) {
						$query .= " AND published = '1'";
					} else {
						if ( $currentUser->gid < 23 ) {
							$query .= " AND uploader_id = " . (int) $user->id;
						}
					}
					$db->setQuery( $query );
					$data = $db->loadObject();
					if ( sizeof($data) > 0 ) {
						$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/attach.png', JText::_( 'COM_SIMPLECALENDAR_ATTACHMENT' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_ATTACHMENT' )));
					}
				}
				break;
			case 'status_color':
				$html .= SCOutput::showCategoryColor($array->status_color, $array->status_name, JText::_('COM_SIMPLECALENDAR_STATUS_LABEL'));
				break;
			case 'custom1':
				if ( $params->get('customfield1_label') != '' ) {
					$html .= $array->customfield1;
				} 
				break;
			case 'custom2':
				if ( $params->get('customfield2_label') != '' ) {
					$html .= $array->customfield2;
				} 
				break;
			case 'featured':
				if ( $array->featured == 1 ) {
					$html .= JHTML::_('image', 'components/com_simplecalendar/assets/images/star_on.png', JText::_( 'COM_SIMPLECALENDAR_FAVOURITE' ), array('title'=>JText::_( 'COM_SIMPLECALENDAR_FEATURED' )));
				}
				break;
			case 'organizer':
				$db	= JFactory::getDBO();
				$currentUser = JFactory::getUser();
				$query = "SELECT name FROM #__simplecalendar_organizers WHERE id = " . $array->organizer_id ;
				$db->setQuery( $query );
				$data = $db->loadObject();
				if ( sizeof($data) > 0 ) {
					$html .= $data->name;
				}
				break;
			default:
				if ( array_key_exists((string)$field, get_object_vars($array)) ) 
				{
					if ( $field == 'start_dt' || $field == 'end_dt' || $field == 'reserve_dt' ) 
					{
						if ( $array->$field != '' && $array->$field != '0000-00-00')
						{
							$html .= JHTML::_('date', $array->$field, $params->get('date_long_format'));
						}
					} 
					else if ( $field == 'start_time' || $field == 'end_time' )  
					{
						if (  $array->$field != NULL && $array->$field != '50:00:00' && $array->$field != '' ) 
						{
							$html .= JHTML::_('date', $array->$field, $params->get('time_format'), 'UTC');
						}
					} 
					else 
					{
						$html .= $array->$field;
					}
				} 
				else 
				{
					$html .= "Error!\n";
				}
				break;
		}
		return $html;
	}
	
	/**
	 * Shows the categories and their colours on the frontend calendar list view.
	 * 
	 * @since 0.7.11b
	 * @return html data
	 */
	public static function showCategoryColors($categories)
	{
		$html = '';
		$i = 0;
		// if ( sizeof($categories) != 0 ) {
		$html .= JText::_('COM_SIMPLECALENDAR_CATEGORIES') . ': ';
		foreach ( $categories as $category ) {
			$link = JRoute::_(SimpleCalendarHelperRoute::getCategoryRoute($item->catid));
// 			$link = JRoute::_( 'index.php?option=com_simplecalendar&view=events&catid='. $category->catslug );
			$html .= '<a href="' . $link . '" style="color:#'.$category->category_color.'">' . $category->title . '</a>';
			if ( $i < sizeof($categories)-1 ) {
				$html .= ' | ';
			}
			$i++;
		}
		//} //endif
		return $html;
	}
	
	/**
	 * Function that validates the install. Please do not hack ;)
	 * @return boolean
	 */
	private static function _validateInstall2()
	{
		$params = JComponentHelper::getParams('com_simplecalendar');
		$password = ( '7803hcheh837362za463j' !== 'csad3984') ? true : false;
		$validateKey = $params->get('validation_key', '282sd38471uo402316123k');
		$validateDomain = $params->get('validation_key_domain', 'localhost');
		$serverBaseUrl = SCOutput::getServerBaseUrl();
		$serverBaseUrlNoPort = explode(':', $serverBaseUrl);
		$serverBaseUrlNoPort = $serverBaseUrlNoPort[0];
		if ( (string)md5(strrev(strrev('54n3xx3454j')) . 
				md5($validateDomain)) == $validateKey
				&& substr($serverBaseUrlNoPort, strlen($serverBaseUrlNoPort)-strlen($validateDomain), strlen($serverBaseUrlNoPort)) == $validateDomain ) 
		{
			return (string)md5(strrev(strrev('54n3xx3454j')) .
					md5($validateDomain)) == $validateKey;
		}
		else
		{
			//var_dump($serverBaseUrlNoPort, $validateDomain, substr($serverBaseUrlNoPort, strlen($serverBaseUrlNoPort)-strlen($validateDomain), strlen($serverBaseUrlNoPort)));
		}
		return $password===$serverBaseUrlNoPort;
	}
	
	/**
	 * Checks wheter an installation is validated or not. Please do not hack ;)
	 * @return boolean true if validated
	 */
	public static function isValidated()
	{
		return SCOutput::_validateInstall2() ? true : false;
	}
	
	/**
	 * Function that returns the non-validated installation text. Please do not hack ;)
	 */
	public static function validateInstallText() 
	{
		if ( !SCOutput::_validateInstall2() ) 
		{
			echo '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">x</button><span aria-hidden="true" class="icon-cancel"></span> ' . JText::_('COM_SIMPLECALENDAR_NOT_VALIDATED_WARNING') . '</div>';		
		}
		
	}
	
	/**
	 * Returns the server base url.
	 * @return string
	 */
	private static function getServerBaseUrl()
	{
		$uri = JFactory::getURI();
		$parts = parse_url($uri->root());
		return $parts['host'];
	}
		
	
}
?>