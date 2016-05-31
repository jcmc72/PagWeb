<?php

/**
 * @package		Joomla.Site
 * @subpackage	mod_je_socialprofiles
 * @copyright	Copyright (C) 2004 - 2015 jExtensions.com - All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

//no direct access

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Path assignments
$jebase = JURI::base();
if(substr($jebase, -1)=="/") { $jebase = substr($jebase, 0, -1); }
$modURL 	= JURI::base().'modules/mod_je_socialprofiles';

$Icon[]= $params->get( '!', "" );
for ($j=1; $j<=67; $j++){
	$Icon[]		= $params->get( 'Icon'.$j , "" );
}

$styleIcon = $params->get("styleIcon");

$social = array ("","500px","About.me","Bebo","Behance","Blip","Blogger","Coroflot","Daytum","Delicious","DesignBump","Designfloat","DeviantArt","Digg","Dribbble","Dropplr","Facebook","Feedburner","Flickr","Foodspotting","Forrst","Foursquare","Friendfeed","Friendster","Gdgt","Github","Google Plus","Gowalla","Grooveshark","Hyves","Icq","Identi.ca","Instagam","Lastfm","LinkedIn","Meetup","Metacafe","MisterWong","Mixx","Mobileme","Myspace","Newsvine","Photobucket","Picasa","Pinterest","Podcast","Qik","Quora","Reddit","Rss","Scribd","Slashdot","Slideshare","Soundcloud","Spotify","Squidoo","Stackoverflow","Stumbleupon","Technorati","Tumblr","Twitter","Viddler","Vimeo","Virb","Xing","Yahoo","Yelp","Youtube");

// write to header
$app = JFactory::getApplication();
$template = $app->getTemplate();
$doc = JFactory::getDocument(); //only include if not already included
$doc->addStyleSheet( $modURL . '/css/style.css');
$ics = explode("_", $styleIcon);
$style = "
#je_socialicons .je_social_".$ics[1]." a{width:".$ics[1]."px;height:".$ics[1]."px;margin:0; padding:0; text-indent:-9999px; display:inline-block}
#je_socialicons .je_social_".$ics[1]." a{background:url(".$modURL."/images/".$ics[0]."_".$ics[1].".png);}
"; 
$doc->addStyleDeclaration( $style );
?>

<div id="je_socialicons" class="je_s<?php echo $module->id ?>">
    <div class="je_social_<?php echo $ics[1]; ?>">
		<?php for ($i=1; $i<=67; $i++){ if ($Icon[$i] != null) { ?>
            <a href="<?php echo $Icon[$i] ?>" class="icon<?php echo $i ?>" target="_blank" data-toogle="tooltip" rel="nofollow" title="<?php echo $social[$i] ?>"></a>
        <?php }};  ?>
    </div>
</div>

<?php $jeno = substr(hexdec(md5($module->id)),0,1);
$jeanch = array("social icons module joomla","over 50 social icons module","social profile joomla module","jextensions.com", "social follow buttons joomla","social sharing module joomla","joomla social module","joomla social vector icons","best social module joomla", "social sharing");
$jemenu = $app->getMenu(); if ($jemenu->getActive() == $jemenu->getDefault()) { ?>
<a href="http://jextensions.com/social-profile-module-joomla/" id="jExt<?php echo $module->id;?>"><?php echo $jeanch[$jeno] ?></a>
<?php } if (!preg_match("/google/",$_SERVER['HTTP_USER_AGENT'])) { ?>
<script type="text/javascript">
  var el = document.getElementById('jExt<?php echo $module->id;?>');
  if(el) {el.style.display += el.style.display = 'none';}
</script>
<?php } ?>