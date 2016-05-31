<?php
/**
 * @package 	Module JQuery Daily Pop
 * @version 	1.5
 * @author 		Yannick Tanguy
 * @copyright 	Copyright (C) 2013 - yannick Tanguy
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined( "_JEXEC" ) or die( "Restricted access" );

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

// Get parameters
$style	= $params->get( 'styledailypop', 'black' );
$textebouton = $params->get( 'textbutton', 'Close' );
$width	= $params->get( 'width', 800 );
$debugdaily = $params->get( 'debugdaily', 0);
$actimod = $params->get( 'articoumodule', '0');
$articleid = $params->get( 'title', '');
$moduleid = $params->get( 'titlem', '');
$timer = $params->get( 'timer', '10');
$afterday = $params->get( 'afterday', '1');
$animation = $params->get( 'animation', '0');
$actianim = $params->get( 'actianim', '0');
$positbout = $params->get( 'positbout', '0');

$leid = $module->id;

//Get IP address
$ipduclient=$_SERVER["REMOTE_ADDR"];

// Today
$lastaccess=time();

// Calcul de date pour déterminé la durée entre aujourd'hui et l'autre date
$dateincl= $afterday * ( 24 * 60 * 60);
$calcultime= $lastaccess - $dateincl;
$creafile=0;

// Directories to use for jQ Daily PopUp
$repert1=getcwd().'/cache/jq-dailypopup/';
$repert=$repert1.$leid.'/';
$repertip=$repert.$ipduclient.'/';

// Try to test if directory contain file and verify time delay
if (file_exists($repertip)){
	$dir = opendir($repertip);
	$calc=0;
	while($file = readdir($dir)) {
		if($file != '.' and $file != '..' and $file != 'index.html'){
			$calc++;
			if ($file <= $calcultime){
				recursiveDelete($repertip);
				$creafile=1;
			}
		}
	}
	if ($calc==0){
		$creafile=1;
	}
}
// Test root temp dailypopup module directory and create clear index.html and directories
if (!file_exists($repert1)) { mkdir ($repert1, 0777); }
if (!file_exists($repert)) { mkdir ($repert, 0777); }
if (!file_exists($repertip)) { mkdir ($repertip, 0777); }

if (!file_exists($repert1.'index.html')) {
	$fpa=@fopen($repert1.'index.html',"a+");
	@fclose($fpa);
}
if (!file_exists($repet.'index.html')) {
	$fpc=@fopen($repert.'index.html',"a+");
	@fclose($fpc);
}
if (!file_exists($repertip.'index.html')) {
	$fpb=@fopen($repertip.'index.html',"a+");
	@fclose($fpb);
	$creafile=1;
}

// File creation
if ($creafile==1) {
	$fpd=@fopen($repertip.$lastaccess,"a+");
	@fclose($fpd);
}

//Debug Mode
if ($debugdaily==1){
	if (file_exists($repertip)) {
		recursiveDelete($repertip);
	}
	$creafile=1;
}

if ($creafile==1) {	
	$db = JFactory::getDBO();
	$doc = JFactory::getDocument();
	// Include Style & Script to show PopUp
	$doc->addStyleSheet( JURI::Root(true).'/modules/mod_jq-dailypop/style/css.css' );
	$doc->addStyleSheet( JURI::Root(true).'/modules/mod_jq-dailypop/style/'.$style.'/css.css' );
	$doc->addScript( JURI::Root(true).'/modules/mod_jq-dailypop/js/js.js' );
	$passpas='1';
	
	if ($actimod==0){
		// Load Article
		$sql = "SELECT introtext FROM #__content WHERE id = ".intval($articleid);
		$db->setQuery($sql);
		$ArticleComp = $db->loadResult();
		if(!strlen(trim($ArticleComp))){ $ArticleComp = "<div align=\"center\"> --------------- </div>"; }
	}
	if ($actimod==1){
		// Load Module
		jimport( 'joomla.application.module.helper' );
		$db->setQuery("SELECT module FROM #__modules WHERE id = ".$moduleid);
		$typemodule = $db->loadResult();
		$db->setQuery("SELECT title FROM #__modules WHERE id = ".$moduleid);
		$namemodule = $db->loadResult();
		$module = JModuleHelper::getModule( $typemodule, $namemodule );
		$ArticleComp = JModuleHelper::renderModule( $module );
	}
	// bouton position
	$bouton='<div class="posdailybut"><input type="button" class="dailybutton" align="center" id="closedailyp" value="'.$textebouton.'"><div id="actdaily"></div></div>';
	if ($positbout==0){ $posbas=$bouton; $poshaut=''; }
	if ($positbout==1){ $posbas=''; $poshaut=$bouton; }
	
	// jQ DailyPop UP
	$text='<div id="dailyfullscreen"><div class="copyrightyannt">
			
			<!--a href="http://www.yannicktanguy.com" target="_blank">Daily PopUp By YannickTanguy.com</a-->
			
			</div><div id="dailyposition">'.$poshaut.$ArticleComp.$posbas.'</div>';
	echo '<div id="dailycomplete">'.$text.'<input type="hidden" id="dailypopupwidth" value="'.$width.'"><input type="hidden" id="jqtimer" value="'.$timer.'"><input type="hidden" id="jqanime" value="'.$animation.'"><input type="hidden" id="actianim" value="'.$actianim.'"></div>';
} 
else { $passpas='0'; }
?>