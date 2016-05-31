<?php
/**
 * @package 	Module JQuery Daily Pop
 * @version 	1.6
 * @author 		Yannick Tanguy
 * @copyright 	Copyright (C) 2013 - yannick Tanguy
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
 
 
// no direct access
defined('_JEXEC') or die;

function recursiveDelete($str){
	if(is_file($str)){ return @unlink($str); }
	elseif(is_dir($str)){
		$scan = glob(rtrim($str,'/').'/*');
		foreach($scan as $index=>$path){ recursiveDelete($path); }
		return @rmdir($str);
	}
}

?>