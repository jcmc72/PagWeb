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
 * Import event controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_simplecalendar
 * @since       1.6
 */
class SimplecalendarControllerImportevents extends JControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SIMPLECALENDAR_IMPORTEVENTS';

	/**
	 * Imports events from CSV files
	 * @since 0.8.9b
	 */
	function importEvents() {
	
		JRequest::setVar( 'view', 'importevents' );
// 		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model = $this->getModel('entry');
	
		$view =& $this->getView('importevents', 'html');
		$view->setModel($model);
	
		parent::display();
	}
	
	/**
	 * Imports events from a CSV file and saves them to the DB
	 */
	function save($key='', $urlVar='') {
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
// 		$db2 	= JFactory::getDBO();
		$msg 	= '';
		$count 	= 0;
		$today 	= date('Y-m-d');
		$fileType = $_FILES['jform']['type']['csvfile'];
		
		$data =  $app->input->get('jform', array(),'array');
		
		if( !empty($_FILES) && (
				($fileType == "application/octet-stream") ||
				($fileType == "application/vnd.ms-excel") ||
				($fileType == "text/plain") ||
				($fileType == "text/csv"))
		) {
			$filename = $_FILES['jform']['name']['csvfile'];
			$tmpName = $_FILES['jform']['tmp_name']['csvfile'];
			$path = JPATH_SITE . '/cache/';
			if(is_writable($path)) {
				if(!move_uploaded_file($tmpName, $path . $filename)) {
					$msg = "<span class=\"error\">".JText::_('COM_SIMPLECALENDAR_IMPORT_ERROR_UPLOAD_FAILED').": " . $_FILES['jform']['error']['csvfile'] . "</span><br>\n";
				} else {
					$start_dt_column = $data['start_dt'];
					$end_dt_column = $data['end_dt'];
					$reserve_dt_column = $data['reserve_dt'];
					$start_time_column = $data['start_time'];
					$end_time_column = $data['end_time'];
					$event_name_column = $data['name'];
					$event_venue_column =$data['venue'];
					$category_column = $data['category'];
					$latlon_column  = $data['latlon'];
					
					$delim = $data['delimiter'];
					$offset = $data['record_number'];
					$categoryID = $data['categoryID'];
					
					$offset = $offset - 1; // an array starts at 0 instead of 1
					$start_dt_column = $start_dt_column - 1;
					$end_dt_column = $end_dt_column - 1;
					$reserve_dt_column = $reserve_dt_column - 1;
					$start_time_column = $start_time_column - 1;
					$end_time_column = $end_time_column - 1;
					$event_name_column = $event_name_column - 1;
					$event_venue_column = $event_venue_column - 1;
					$category_column = $category_column - 1;
					$latlon_column = $latlon_column - 1;
						
					$content = file($path . $filename);
	
					if ( sizeof($content) > 0 ) {
						for ( $i = $offset; $i < sizeof($content); $i++ ) {
	
							$event = explode($delim, $content[$i]);
	
							$event[$event_name_column] = ltrim(rtrim($event[$event_name_column]));
							$event[$event_venue_column] = ltrim(rtrim($event[$event_venue_column]));
	
							$start_dt = date('Y-m-d', strtotime($event[$start_dt_column]));
							$end_dt = date('Y-m-d', strtotime($event[$end_dt_column]));
							$reserve_dt = date('Y-m-d', strtotime($event[$reserve_dt_column]));
							$event[$start_dt_column] = ltrim(rtrim($start_dt));
							$event[$end_dt_column] = ltrim(rtrim($end_dt));
							$event[$reserve_dt_column] = ltrim(rtrim($reserve_dt));
							$event[$latlon_column] = ltrim(rtrim($event[$latlon_column]));
	
							if ( $event[$start_time_column] != '' &&  $event[$start_time_column] != '0' ) {
								$start_time = date('H:i', strtotime($today . ' ' . $event[$start_time_column]));
							} else {
								$start_time = null;
							}
							if ( $event[$end_time_column] != '' && $event[$end_time_column] != '0' ) {
								$end_time = date('H:i', strtotime($today . ' ' . $event[$end_time_column]));
							} else {
								$end_time = null;
							}
							$event[$start_time_column] = ltrim(rtrim($start_time));
							$event[$end_time_column] = ltrim(rtrim($end_time));
							
							$event[$category_column] = ltrim(rtrim($event[$category_column]));
							
							// Category: if it's not numeric, try to guess the category ID from the description.
							if ( !is_numeric($event[$category_column]) )
							{
								$catQuery = $db->getQuery(true);
								$catQuery->select('MAX(id) AS id')
										->from('#__categories')
										->where('extension = \'com_simplecalendar\'')
										->where('title = \'' . $event[$category_column] . '\'')
										->group('title');
								
								$db->setQuery($catQuery);
								$data = $db->loadRow();
								if ( sizeof($data) > 0 )
								{
									$event[$category_column] = $data[0]; 
								}
							}
							else
							{
								if ( $event[$category_column] == null || $event[$category_column] == '' )
								{
									$event[$category_column] = $categoryID;
								}
							}
							
							$event[$event_name_column] = str_replace('"', '', utf8_encode($event[$event_name_column]));
							$event[$event_venue_column] = str_replace('"', '', utf8_encode($event[$event_venue_column]));
							$event[$start_dt_column] = str_replace('"', '', $event[$start_dt_column]);
							$event[$end_dt_column] = str_replace('"', '', $event[$end_dt_column]);
							$event[$reserve_dt_column] = str_replace('"', '', $event[$reserve_dt_column]);
							$event[$start_time_column] = str_replace('"', '', $event[$start_time_column]);
							$event[$end_time_column] = str_replace('"', '', $event[$end_time_column]);
							$event[$category_column] = str_replace('"', '', utf8_encode($event[$category_column]));
							$event[$latlon_column] = str_replace('"', '', $event[$latlon_column]);
									
							// Formatting dates and times
							if ( $event[$end_dt_column] == '' || $event[$end_dt_column] == date('Y-m-d', strtotime('1970-01-01')) ) {
								$event[$end_dt_column] = null;
							}
							if ( $event[$reserve_dt_column] == '' || $event[$reserve_dt_column] == date('Y-m-d', strtotime('1970-01-01')) ) {
								$event[$reserve_dt_column] = null;
							}
							if ( $event[$start_time_column] == '' || $event[$start_time_column] == date('H:s', strtotime('00:00')) ) {
								$event[$start_time_column] = null;
							}
							if ( $event[$end_time_column] == '' || $event[$end_time_column] == date('H:s', strtotime('00:00')) ) {
								$event[$end_time_column] = null;
							}
								
							jimport( 'joomla.filter.output' );
							$alias 	= $event[$event_name_column];
							$alias	= JFilterOutput::stringURLSafe($alias);
							
							$user	= JFactory::getUser();
	
							if ( !empty($event[$event_name_column]) ) {
								$columnsArray = array(
									'start_dt', 'end_dt',
									'reserve_dt', 'start_time', 'end_time', 
									'name', 'venue', 'latlon', 'catid',
									'alias', 'created_dt', 'modified_dt', 'created_by', 
								);
								
								$valuesArray = array(
										$db->quote($event[$start_dt_column]),
										$event[$end_dt_column] == null || $event[$end_dt_column] == '' || $event[$end_dt_column] == '0' ?  'null' : $db->quote($event[$end_dt_column]),
										$event[$reserve_dt_column] == null || $event[$reserve_dt_column] == '' || $event[$reserve_dt_column] == '0' ?  'null' : $db->quote($event[$reserve_dt_column]),
										$event[$start_time_column] == null || $event[$start_time_column] == '' || $event[$start_time_column] == '0' ? 'null' : $db->quote($event[$start_time_column]),
										$event[$end_time_column] == null || $event[$end_time_column] == '' || $event[$end_time_column] == '0'? 'null' : $db->quote($event[$end_time_column]),
										$db->quote($event[$event_name_column]),
										$db->quote($event[$event_venue_column]),
										$db->quote($event[$latlon_column]),
										$event[$category_column] == null ?  $db->quote($categoryID) : $db->quote($event[$category_column]),
										$db->quote($alias),
										$db->quote($today),
										$db->quote($today),
										$db->quote($user->get('id'))
								);
								
								$query = $db->getQuery(true);
								
								$query
									->insert($db->quoteName('#__simplecalendar'))
									->columns($db->quoteName($columnsArray))
									->values(implode(',', $valuesArray));
									
								
								
								$db->setQuery($query);
								$db->query();
								$error = $db->getErrorNum();
	
								if ( $error ) {
									if( $error == 1062 ) {
										$msg = JText::_('COM_SIMPLECALENDAR_IMPORT_ERROR_EVENT_EXISTS'). ": ".$event['name'];
									} else {
										$msg = $db->getErrorMsg() . "<br />\n";
									}
								} else {
									$count++;
								}
							}
						}
					}
					else{
						$msg = JText::_('COM_SIMPLECALENDAR_IMPORT_ERROR_FILE_EMPTY');
					}
	
					if(!unlink($path . $filename)){
						$msg = JText::_('COM_SIMPLECALENDAR_IMPORT_ERROR_DELETING_FILE').": ".$path ." | ". $filename;
					}
				}
			}
			else{
				$msg = JText::_('COM_SIMPLECALENDAR_IMPORT_FOLDER_NOT_WRITABLE');
			}
		}
		else{
			$msg = JText::_('JERROR_AN_ERROR_HAS_OCCURRED');
		}
	
		if ( $msg == '' ) {
			$msg = $count . ' ' . JText::_('COM_SIMPLECALENDAR_IMPORT_N_EVENTS_SUCCESSFULLY_IMPORTED');
		}
		$link = 'index.php?option=com_simplecalendar&view=events';
		$this->setRedirect($link, $msg);
	}
	
	
	public function cancel() 
	{
		$link = 'index.php?option=com_simplecalendar&view=events';
		$this->setRedirect($link, $msg);
	}
}
