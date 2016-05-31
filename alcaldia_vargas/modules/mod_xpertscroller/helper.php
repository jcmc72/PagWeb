<?php
/**
 * @package XpertScroller
 * @version 3.10-1-GFF3CA2D
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die;

abstract class XEFXpertScrollerHelper{

   /*
    * Load add script settings
    * and push it to document head
    */
    public static function load_script($module, $params)
    {
        $doc = JFactory::getDocument();

        // Set moduleid
        $module_id = XEFUtility::getModuleId($module, $params);

        // Load jQuery
        XEFUtility::addjQuery($module, $params);
        
        $animationMode = ($params->get('animation_style') == 'animation_h') ? 'false' : 'true';
        $speed = (int)$params->get('animation_speed');
        $repeat = ( (int)$params->get('repeat') ) ? 'true' : 'false';
        $keyboardNav = ( (int)$params->get('keyboard_navigation') ) ? 'true' : 'false';
        
        //auto scroll plugin config
        $autoScroll = '';
        if ( (int)$params->get('auto_play') ){
            $autoPlay   = ( (int)$params->get('auto_play') ) ? 'true' : 'false';
            $interval   = (int)$params->get('interval');
            $autoPause  = ( (int)$params->get('auto_pause') ) ? 'true' : 'false';
            
            $autoScroll = ".autoscroll({ autoplay: {$autoPlay} , interval: {$interval}, autopause:{$autoPause} })";
        }
        //navigator plugin config
        $navigator='';
        if($params->get('navigator')) $navigator = ".navigator()";

        $js = "
            jQuery(document).ready(function(){
                jQuery('#{$module_id}').scrollable({
                    vertical: {$animationMode},
                    speed: {$speed},
                    circular : {$repeat},
                    keyboard: {$keyboardNav}
                }){$autoScroll}{$navigator};
            });
        ";
        $doc->addScriptDeclaration($js);

        if(!defined('XPERT_SCROLLER')){
            //add scroller js file
            $doc->addScript(JURI::root(true).'/modules/mod_xpertscroller/assets/js/xpertscroller.js');
            $doc->addScript(JURI::root(true).'/modules/mod_xpertscroller/assets/js/script.js');
            define('XPERT_SCROLLER',1);
        }
    }
    
    /*
    * Load necesery style.
    * take all css settings and push it on document head
    */
    
    public static function load_style($module, $params)
    {
        $doc = JFactory::getDocument();
        //set moduleid
        $module_id = XEFUtility::getModuleId($module, $params);
        $moduleId = '#'.$module_id;
        $moduleClass = '.'. $module_id;
        
        $scrollerLayout = $params->get('scroller_layout');
        
        /*
        * module unique id will only assign on horizontl style. 
        * this unique class will only use for navigation arrow styling
        * vertical style will auto adjuct arrow position to middle using css file.
        */
        $selectorClass = ($scrollerLayout == 'basic_h') ? '.' . $moduleId : '';
        
        //scroller wrapper widtha nd height. this width and height will effect on .pane class also.
        $moduleWidth = $paneWidth = (int)$params->get('module_width');
        $moduleHeight = (int)$params->get('mod_height');
        
        /*
        * In horizontal style item width will calculated by persentage value
        * In vertical style item height will calculate on module height and num of columns
        */
        if($scrollerLayout == 'basic_h') $itemDimensions = 'width:'. 100 / (int)$params->get('col_amount') . '%';
        else $itemDimensions = 'width: 100%; height:' . $moduleHeight / $params->get('col_amount') .'px' ;

        $controlMargin = $params->get('control_margin');
        
        //items div always higher value thats way we will check animatin style and determine the proper css property
        $animationStyle = ($params->get('animation_style') == 'animation_h') ? 'width' : 'height';

        //preaper all css settings
        $css = "
            {$moduleId} {height: {$moduleHeight}px;}

            {$moduleId} .items { {$animationStyle}:20000em; }
            {$moduleId} .pane .item{{$itemDimensions}; overflow:hidden; }

            {$moduleClass} a.browse{ margin:{$controlMargin}; }
            
        ";
        //push this css on document head
        $doc->addStyleDeclaration($css);
        
    }
}