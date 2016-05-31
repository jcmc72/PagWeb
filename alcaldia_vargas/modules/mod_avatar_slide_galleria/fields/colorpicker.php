<?php
/**
 * @copyright	amazing-templates.com
 * @author		Tran Nam Chung
 * @mail		chungtn2910@gmail.com
 * @link		http://www.amazing-templates.com
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldcolorpicker extends JFormField
{
    protected $type = 'colorpicker';

    protected function getInput()
    {
        $document = JFactory::getDocument();
        $base_path = JURI::root(true).'/modules/mod_avatar_slide_galleria/';
        $color_code = '[255,0,0]';
		
        $document->addScript($base_path.'assets/js/colpick.js');
        $document->addStyleSheet($base_path.'assets/css/colpick.css');
        
        if(strlen($this->value) == 7 && substr($this->value, 0, 1) == '#') 
        {
            $color_code = '['
                          .hexdec(substr($this->value, 1, 2))
                          .','
                          .hexdec(substr($this->value, 3, 2))
                          .','
                          .hexdec(substr($this->value, 5, 2))
                          .']';
        }
        
        $control_code =  'jQuery.noConflict();'
        					.'(function($){'
	        					.'$(document).ready(function(){'
	        						.'$("#'.$this->id.'").colpick({'.'layout:"hex",'.'submit:0,'.'colorScheme:"dark",'.'onChange:function(hsb,hex,rgb,el,bySetColor) {'
										.'$(el).css("border-color","#"+hex);'
										.'if(!bySetColor) $(el).val(hex);'
									.'}'
									.'}).keyup(function(){'
									.'$(this).colpickSetColor(this.value);'
									.'});'
								.'});'
							.'})(jQuery);';

        $document->addScriptDeclaration($control_code);
            
        $html_code = '<input id="'.$this->id.'" name="'.$this->name.'" type="text" class="text_area at-color-picker-field" size="9" value="'.$this->value.'" style="border-color:#'.$this->value.'"/>';
		 
        return $html_code;
    }
}

?>