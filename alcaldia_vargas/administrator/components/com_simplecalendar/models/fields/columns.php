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

defined('JPATH_BASE') or die;

if ( !defined('DS') ) 
{
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * Supports a color-picker widget
 *
 * @package     com_simplecalendar
 * @subpackage  settings
 * @since       3.0
 */
class JFormFieldColumns extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'columns';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_simplecalendar' . DS . 'helpers' . DS . 'output.class.php');
		$document = JFactory::getDocument();
		$order = $this->value;
		
		$availableColumns = array(
				'name' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_NAME'),
				'name_nolink' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_NAME_NO_LINK'),
				'venue' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_VENUE'),
				'start_dt' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_START_DATE'),
				'end_dt' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_END_DATE'),
				'date' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_DATE_COMBINED'),
				'start_time' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_START_TIME'),
				'end_time' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_END_TIME'),
				'time' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_TIME_COMBINED'),
				'catname' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_CATEGORY_NAME'),
				'status' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_STATUS'),
				'status_color' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_STATUS_COLOR'),
				'price' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_PRICE'),
				'custom1' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_CUSTOMFIELD1'),
				'custom2' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_CUSTOMFIELD2'),
				'organizer' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_ORGANIZER'),
				'hits' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_HITS'),
				'featured' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_FEATURED'),
				'author' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_AUTHOR'),
				'username' => JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_COLUMN_USERNAME'),
		);
		$options = array();
		foreach ( $availableColumns as $column=>$name ) {
			$options[] = JHtml::_('select.option', $column, $name);
		}
		$columnSelectBox =  JHtml::_('select.genericlist', $options, 'avc', 'class="availableColumns input-small"');
		
		$i = 0;
		$data = json_decode($this->value);
		
		if ( sizeof($data) == 0 )
		{
			$colname[$i] 	= 'name';
			$class[$i] 	= '';
			$style[$i] 	= '';
			$caption[$i] 	= 'Name';
			$this->value = json_encode("[{'colname':'name','cssclass':'','style':'','caption':'Name'}]");
		}
		else 
		{
			foreach ( $data as $row )
			{
				$colname[$i] 	= $row->colname;
				$class[$i] 		= $row->cssclass;
				$style[$i] 		= $row->style;
				$caption[$i] 	= $row->caption;
				$i++;
			}
		}
		
		$document->addScript(JUri::base() . 'components/com_simplecalendar' . 
				'/assets/js/html5sortable/jquery.sortable.min.js');
		$document->addStyleSheet(JUri::base() . 'components/com_simplecalendar' .
				'/assets/js/html5sortable/html5sortable.css');
		$html = array();
		$html[] = "
	<a href=\"#\" id=\"btnAddnew\" class=\"btn btn-inverse\">" . JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_BTN_ADD_NEW') . "</a><br />
	<ul id=\"sortable-list\" class=\"sortable\">";
	for ($j = 0; $j < $i; $j++ )
	{
		$columnSelectBox2 =  JHtml::_('select.genericlist', $options, 'available_columns_'.$j, 'class="availableColumns input-small"', 'value', 'text', $colname[$j]);
		$html[] = '<li class="sortable-item" title="'.$j.'">' . $columnSelectBox2;
		$html[] = JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_CLASS') . ':<input type="text" name="cssclass" class="colclass input-small" value="'.$class[$j].'" />';
		$html[] = JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_STYLE') . ':<input type="text" name="style" class="colwidth input-small" value="'.$style[$j].'"/><br/>';
		$html[] = JText::_('COM_SIMPLECALENDAR_CONFIG_COLUMNS_CAPTION') . ':<input type="text" name="caption" class="colcaption" value="'.$caption[$j].'" />';
		if ( $j > 0 ) 
			$html[] = '<span class="btn-remove">x</span></li>';
	}
	$html[] = "</ul>
	<script>
	jQuery(document).ready(function() {
		var i = 0;
		var list = jQuery('#sortable-list');
	    jQuery('.sortable').sortable().bind('sortupdate', function() {
			updateParam();
		});
		var sortInput = jQuery('#" . $this->id ."');
		
		var updateParam = function() {
			var arr = [];
			list.children('li').each(function(i) {
				var current = jQuery(this);
				var text = '';
				var jsonData = {};
				jsonData['colname'] = current.children('select').val();
				current.children('input').each(function(j) {
					jsonData[jQuery(this).attr('name')] = jQuery(this).val();
				}); 
				arr.push(JSON.stringify(jsonData));
			});
			arrJoin = arr.join(',');
			if ( arr.length > 1 )  {
				var arrText = '[' + arrJoin.replace('[', '{').replace(']', '}') + ']'
			} else {
				var arrText = '' + arrJoin.replace('[', '{').replace(']', '}') + '';
			}
			jQuery('#" . $this->id . "').val(arrText);
		};
		
		var btnRemove = function() {
			jQuery('.btn-remove').click(function() {
				var parentLi = jQuery(this).parent('li'); 
				parentLi.fadeOut(300, function() { 
					jQuery(parentLi).remove();
					updateParam();
				 });
			});
		};
					
		jQuery('#btnAddnew').click(function() {
			var el = jQuery('li.sortable-item:last').clone()
			el.hide().appendTo(list).slideToggle(300);
			var newid = parseInt(el.attr('title')) + 1;
			el.attr({
				id: newid,
				style: '',
				title: newid,
				value: ''
			});
			var select = el.children('select');
			select.attr('id', 'available_columns_' + newid);
			select.attr('name', 'available_columns');
			select.attr('class','availableColumns input-small');
			el.children('div').remove();
			select.chosen();
			select.trigger(\"liszt:updated\");
			jQuery('.sortable').sortable('destroy');
			jQuery('.sortable').sortable();
			btnRemove();
			updateParam();
		});
				
		list.children('li').each(function(i) {
			if ( i > 0 ) {
				btnRemove();
			}
		});
					
		jQuery('input[type=\"text\"]').keyup(function() {
			updateParam();
		});
					
		jQuery('select').change(function() {
			updateParam();
		});
		
		//http://davidwalsh.name/mootools-drag-ajax
	});
	</script>";
		$html[] = '<input type="hidden" name="columnCount" id="columnCount" value="" />';
		$html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value) . '" />';
		if ( !SCOutput::isValidated() )
		{
			echo SCOutput::validateInstallText();
		}
		return implode($html);
	}

}