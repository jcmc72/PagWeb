<?php
/**
 * @package Xpert Slider
 * @version 1.4
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

abstract class XEFXpertSliderHelper{
    
    public static function getFlickrItems($params, $total_items)
    {
        require_once 'libs/flickr/phpFlickr.php';
        jimport('joomla.filesystem.folder');

        $api_key = '2a4dbf07ad5341b2b06d60c91d44e918';
        $cache_path = JPATH_ROOT. '/cache/mod_xpertslider/flickr';
        $nsid = '';
        $photos = array();

        // create cache folder if not exist
        JFolder::create($cache_path, 0755);

        $f = new phpFlickr($api_key);
        $f->enableCache('fs',$cache_path, $params->get('cache_time',900)); //enable caching

        if( $params->get('flickr_search_from') == 'user')
        {
            $username = $params->get('flickr_user_name');
            if($username != NULL)
            {
                $person = $f->people_findByUsername($username);
                $nsid = $person['id'];
            }else
                return ;

            $photos = $f->people_getPublicPhotos($nsid, NULL, NULL, $total_items);
            $source = $photos['photos']['photo'];
        }

        if( $params->get('flickr_search_from') == 'tags' OR $params->get('flickr_search_from') == 'text' )
        {
            $tags = $params->get('flickr_attrs');
            if(!empty($tags))
            {
                $attrs = '';
                if( $params->get('flickr_search_from') == 'tags' ) $attrs = 'tags';
                if( $params->get('flickr_search_from') == 'text' ) $attrs = 'text';

                $photos = $f->photos_search( array($attrs=>$tags,'per_page'=>$total_items) );
                $source = $photos['photo'];
            }else
                return;
        }

        if($params->get('flickr_search_from') == 'recent'){
            $photos = $f->photos_getRecent( NULL, $total_items );
            $source = $photos['photo'];
        }

        //$extras = 'description,date_upload,owner_name,tags';
        $items = array();
        $i = 0;
        if(count($source)>0){
            foreach ($source as $photo)
            {
                $id = $photo['id'];
                $obj = new stdClass();
                $info = $f->photos_getInfo($id);
                $nsid = $info['owner']['username'];

                $obj->title = $info['title'];
                $obj->image = $f->buildPhotoURL($photo,'large');
                $obj->thumb = $f->buildPhotoURL($photo,'square');
                $obj->link = $info['urls']['url'][0]['_content'];
                $obj->introtext = $info['description'];
                $obj->itemCreationDate = date('Y.M.d : H:i:s A', $info['dateuploaded']);

                $items[$i] = $obj;
                $i++;
            }
        }
        return $items;
    }

    public static function loadScripts($module, $params)
    {
        $doc = JFactory::getDocument();
        // Set moduleid
        $module_id = XEFUtility::getModuleId($module, $params);
        // Load jQuery form framework
        XEFUtility::addjQuery($module, $params);
        // Attribute array
        $attr = array();

        //Controller
        if( $params->get('controller') == 'always' )
        {
            $attr[] = 'navigationHover: false';
        }
        //Navigation Type
        if( $params->get('navigation') == 'dots' )
        {
            $attr[] = 'pagination: true';
            $attr[] = 'thumbnails: true';

        }elseif( $params->get('navigation') == 'thumb' )
        {
            $attr[] = 'pagination: false';
            $attr[] = 'thumbnails: true';

        }else{
            $attr[] = 'pagination: false';
            $attr[] = 'thumbnails: false';
        }

        //loader with auto play settings, if autoplay turned off no need to load the loader
        if($params->get('auto_advance'))
        {
            $attr[] = "loader: '{$params->get('loader')}' ";
            if($params->get('loader') == 'pie')
            {
                //Pie Position
                $attr[] = "piePosition: '{$params->get('pie_position')}' ";
            }else{
                //Bar positions
                $attr[] = "barPosition: '{$params->get('bar_position')}' ";
            }

        }else{

            $attr[] = "autoAdvance: false";
        }

        // Height
        $attr[] = "height: '" . $params->get('image_height','300px') . "'";

        //Effects
        $attr[] = "fx: '". implode(',',$params->get('effects')) ."'";

        if( $params->get('transition') )
        {
            //Easing
            $attr[] = "easing: '{$params->get('transition')}' ";
        }

        //Time
        $attr[] = "time: {$params->get('time')}";
        //Trans Period
        $attr[] = "transPeriod: {$params->get('trans_period')}";

        $js = "jQuery(function(){

        			jQuery('#$module_id').xslider({
                       " . implode(",",$attr) . "
        			});

        		});";

        $doc->addScriptDeclaration($js);

        if(!defined('XPERT_SLIDER'))
        {

            $doc->addScript( JURI::root(true).'/modules/mod_xpertslider/assets/js/jquery.mobile.customized.min.js' );
            $doc->addScript( JURI::root(true).'/modules/mod_xpertslider/assets/js/jquery.easing.1.3.js' );
            $doc->addScript( JURI::root(true).'/modules/mod_xpertslider/assets/js/xslider.js' );

            define('XPERT_SLIDER',1);
        }
    }



    public static function setMeta()
    {

        $doc                = JFactory::getDocument();
        //set viewport meta
        $doc->setMetaData('viewport','width=device-width, initial-scale=1');
        
    }

    public static function getDataAttribute($item, $params)
    {
        $data = '';

        $data .= 'data-thumb="'. $item->thumb . '"'; //Thumb image
        $data .= ' data-src="'. $item->image . '"'; //Image source

        if( $params->get('link',1) )
        {
            $data .= ' data-link="'. $item->link . '"'; //Image link
        }

        //$data .= ' data-portrait="true"'; //Image link


        return $data;

    }
}