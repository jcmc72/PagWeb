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

/**
 * Supports the Google Maps location selector widget
 *
 * @package     com_simplecalendar
 * @subpackage  settings
 * @since       3.0
 */
class JFormFieldGmap extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.0
	 */
	protected $type = 'gmap';

	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getLabel()
	{
		$html = array();
		$html[] = JText::_('COM_SIMPLECALENDAR_ORGANIZER_FIELD_ADDRESS_LABEL');
		return implode($html);
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   3.0
	 */
	protected function getInput()
	{
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');

// 		Initialize to a valid value
		if ( $this->value == null || $this->value == '' )
		{
			$center = $params->get('gmap_std_latlon', '46,9');
		}
		else
		{
			$center = $this->value;
		}
				
		// Add the necessary Javascript
		$document->addScript( 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBITX-gc9VZTAJHmfJaRgQKA3pHTOA5yWE&libraries=places&sensor=false' );
		
		// Set the autocomplete input field
		if ( !isset($this->element['searchfield']) || $this->element['searchfield'] == '' )
		{
			$input = "document.getElementById('map_search')";
		} else {
			$input = "document.getElementById('jform_" . $this->element['searchfield'] . "')";
		}
		
		$script = "
var geocoder = new google.maps.Geocoder();
var map;
var marker;
var updateaddressfield = 'jform_address'; //TODO
var autocomplete;
var input;

function initialize() {
	var mapCenter = new google.maps.LatLng(" . $center . ");
	var mapOptions = {
    		center: mapCenter,
			zoom: 10,
			scrollwheel: false,
	    	streetViewControl: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
	map = new google.maps.Map(document.getElementById(\"scmap-canvas\"), mapOptions);
	marker = new google.maps.Marker({
	    map: map,
	    draggable: true,
	    position: mapCenter
	});
	input = " . $input . ";
    autocomplete = new google.maps.places.Autocomplete(input);
	autocomplete.bindTo('bounds', map);
	google.maps.event.addListener(autocomplete, 'place_changed', function() {
          marker.setVisible(false);
          input.className = '';
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // Inform the user that the place was not found and return.
            input.className = 'notfound';
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
		  geocodePosition(place.geometry.location);
          marker.setVisible(true);
        });
	google.maps.event.addListener(marker, 'dragend', function(evt) {
		document.getElementById('" . $this->id . "').value = evt.latLng.lat().toFixed(5) + ',' + evt.latLng.lng().toFixed(5);
		geocodePosition(marker.getPosition());
	});			
}";
if ( $this->element['class'] != 'inside' ) {			 
	$script .= "google.maps.event.addDomListener(window, 'load', initialize);";
}
$script .= "
function codeAddress() {
    var address = input.value;
    geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
    		marker.setPosition(results[0].geometry.location);
			document.getElementById('" . $this->id . "').value = results[0].geometry.location.lat().toFixed(5) + ',' + results[0].geometry.location.lng().toFixed(5);
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}
				
function geocodePosition(pos) {
	geocoder.geocode({
		latLng: pos
	}, function(responses) {
	if (responses && responses.length > 0) {
		document.getElementById(updateaddressfield).value = responses[0].formatted_address;
		document.getElementById('" . $this->id . "').value = responses[0].geometry.location.lat().toFixed(5) + ',' + responses[0].geometry.location.lng().toFixed(5);
	} else {
		document.getElementById(updateaddressfield).value = '*****';
		alert('Cannot determine address at this location.');
	}
	});
}";
		$document->addScriptDeclaration($script);
		
		if ( $this->element['class'] == 'inside' )
		{
			$script = "function showMap(dir) {
				if ( dir == true ) {
					document.getElementById('maparea').style.display='block';
					document.getElementById('scmap-canvas').style.display='block';
					document.getElementById('showmapbutton_on').style.display='none';
					document.getElementById('showmapbutton_off').style.display='inline';
					document.getElementById('btn_removemapdata').style.display='inline';
					initialize();
					google.maps.event.trigger(map, 'resize');
				} else {
					document.getElementById('maparea').style.display='none';
					document.getElementById('scmap-canvas').style.display='none';
					document.getElementById('showmapbutton_on').style.display='inline';
					document.getElementById('showmapbutton_off').style.display='none';
					document.getElementById('btn_removemapdata').style.display='none';
				}
			}
					
			function removeMapData() {
				document.getElementById('jform_" . $this->element['searchfield']. "').value='';
				document.getElementById('" . $this->id . "').value='';
				showMap(false);
			}";
			$document->addScriptDeclaration($script);
		}

		$css = "#scmap-canvas label { width: auto; display:inline; }
				#scmap-canvas img { max-height: none; max-width: none; }
				#scmap-canvas { width:320px; height:150px; display: block; }";
		$document->addStyleDeclaration($css);
		
		$html = array();
		$attr = '';
			
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		if ( $this->element['show'] == '1' )
		{
			$html[] = '<input type="text" '. $attr . ' name="'. $this->name . '" id="'. $this->id . '" value="'.$this->value.'" style="margin-bottom: 10px;" />';
		}
		else
		{
			$html[] = '<input type="hidden" '. $attr . ' name="'. $this->name . '" id="'. $this->id . '" value="'.$this->value.'" style="margin-bottom: 10px;" />';
		}
		if ( $this->element['class'] == 'inside' )
		{
			$html[] = '&nbsp;';
			
			$html[] = '<input type="button" onclick="showMap(true);" id="showmapbutton_on" value="[' . JText::_('COM_SIMPLECALENDAR_SETTINGS_SHOW_MAP') . ']" />' .
					'<input type="button" onclick="showMap(false);" id="showmapbutton_off" value="[' . JText::_('COM_SIMPLECALENDAR_SETTINGS_HIDE_MAP') . ']" style="display:none;" />' .
					'<input type="button" onclick="removeMapData();" id="btn_removemapdata" value="[' . JText::_('COM_SIMPLECALENDAR_SETTINGS_REMOVE_MAP_DATA') . ']" style="display:none;" />';
			
			$html[] = '<div id="maparea" style="display:none;">';
			if ( !isset($this->element['searchfield']) || $this->element['searchfield'] == '' )
			{
				$html[] = '<input type="text" '. $attr . ' name="map_search" id="map_search" value="" style="margin-bottom: 10px;" />';
			}
			$html[] = '<div id="scmap-canvas" style="display:none;"> </div>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<div id="maparea">';
			if ( !isset($this->element['searchfield']) || $this->element['searchfield'] == '' )
			{
				$html[] = '<input type="text" '. $attr . ' name="map_search" id="map_search" value="" style="margin-bottom: 10px;" />';
			}
			$html[] = '<div id="scmap-canvas"> </div>';
			$html[] = '</div>';
		}	
		return implode($html);
	}
}