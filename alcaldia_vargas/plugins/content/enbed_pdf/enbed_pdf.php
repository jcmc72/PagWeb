<?php
/**
* @version		$Id: Enbed pdf v1.8.5 2013-08-21 16:48:33Z $
* @package		Joomla 3.0
* @copyright	Copyright (C) 2005 - 2013 Maik Heinelt. All rights reserved.
* @author		Maik Heinelt (www.heinelt.info)
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
 
 
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

jimport('joomla.plugin.plugin');



class plgContentenbed_pdf extends JPlugin
{
	function plgContentenbed_pdf( &$subject, $params ) 
		{
			parent::__construct( $subject, $params );
		}




	function onContentPrepare($context, &$row, &$params, $limitstart)
    	{

// [[[ 2010-12-13 Check for used PHP version, cause browser.php doesn't work with PHP4 !!!
    		$phpver = phpversion();

    		if ($phpver < 5)
	    		{
	    			echo "<font color=\"red\"><b><br /><br /><center>
	    			Your server use PHP $phpver.<br />
	    			It is necessary to use at least PHP 5 to run Enbed_pdf. Please contact your webhoster to upgrade!
	    			<b></font></center><br /><br /><br />";
	    			return 0;
	    		}	
/// ]]] 2010-12-13 Check for used PHP version, cause browser.php doesn't work with PHP4 !!!
    		
    		
    		require_once(JPATH_PLUGINS.DS.'content'.DS.'enbed_pdf'.DS.'browser.php');

        	$output = $row->text;
        	$regex = "#{pdf}(.*?){/pdf}#s";
			$found = preg_match_all($regex, $output, $matches);
			
			//$plugin = & JPluginHelper::getPlugin('content', 'enbed_pdf');

			// [[[ Load plugin params info
			$mode = $this->params->def('mode', 1);
			$proxy = $this->params->def('proxy', 1);
			$height = $this->params->def('dheight', 300);
			$width =  $this->params->def('dwidth', 400);
			$alt_link = $this->params->def('alt_link', 1);
			$link_comment = $this->params->def('link_comment', 'Can\'t see this Document?');
			$placeholder = $this->params->def('placeholder');
			
			// !! Adobe Reader default settings !!
			$ar_toolbar = $this->params->def('ar_toolbar', 1);
			$ar_navpanes = $this->params->def('ar_navpanes', 1);
			$ar_scrollbar = $this->params->def('ar_scrollbar', 1);
			$ar_searchbar = $this->params->def('ar_searchbar', 0);
			// ]]] Load plugin params info


// [[[ Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
			$ar_param = null;
			$o_tb = $ar_toolbar;
			$o_np = $ar_navpanes;
			$o_scb = $ar_scrollbar;
			$o_sb = $ar_searchbar;
			
			if (!function_exists("acroreadparams")) 
				{ 
				function acroreadparams($var1, $var2, $var3, $var4)
					{
						$ar_search = null;
						if ($var4 == 1)
						{
							$ar_search = '&search="foo"';
						}
						$ar_param = "#toolbar=$var1&navpanes=$var2&scrollbar=$var3$ar_search";
						
						return $ar_param;
					}
				}
// ]]] Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10


// [[[ 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed
if (!function_exists("showpdfpath")) 
	{
		function showpdfpath($alt_link, $link_comment, $placeholder, $enpdf)
				{
					if (!$alt_link)
						{
							// [[[ 2010-11-12 Added placeholder for link to pdf
							if (empty($placeholder))
								{
									$placeholder = $enpdf;
								}
							// [[[ 2010-11-12 Added placeholder for link to pdf
							$alink = "<br/>$link_comment <a href=\"$enpdf\" target=\"_blank\">$placeholder</a><br/>";
							
							return $alink;
						}
				}	
	}
	
// ]]] 2010-11-05 pdf link entry under each pdf file, in case it will not be displayed


// [[[ Browser identification (2010-11-08 Browser class changed, cause of similar class names in other Joomla extensions)
			$isIE = 0;
			$Enbedbrowser = new EnbedBrowser();
			
			if ( $Enbedbrowser->getBrowser() == EnbedBrowser::BROWSER_IE ) 
				{
					$isIE = 1;
				}
// ]]] Browser identification (2010-11-08 Browser class changed, cause of similar class names in other Joomla extensions)
	

	
// [[[ 2010-10-29 Use the proxy.php to avoid blocking the google page in IE8 >.
			$googlepath = "http://docs.google.com/viewer";
			if ( $proxy == 0 && $isIE == 1 )
				{
					$googlepath = JURI::base().DS.'plugins'.DS.'content'.DS.'enbed_pdf'.DS.'proxy.php';  // 2011-11-29 FIX! Thanks to Antonino Migliore !!
				}
// ]]] 2010-10-29 Use the proxy.php to avoid blocking the google page in IE8 >.

			$mcount = 0;
			$alink = null;
			
			
			if ( $found )
				{
					foreach ( $matches[0] as $value ) 
					{
						
					$enpdf = $value;
					$find = '|';
					
					if( strstr($enpdf, $find) )
						{
							$arr = explode('|',$enpdf);
							$enpdf = str_replace('{pdf}','', $arr[0]);				//		<< 2010-12-20 replaced in version 1.8 !! Bug, if ftp:// path is used!
							
							foreach ( $arr as $phrase )
								{
// Parse for PDF-height
									if ( strstr(strtolower($phrase), 'height:') )	
										{
											$tpm1 = explode(':',$phrase);
											$height = trim($tpm1[1], '"');
										}
										
// Parse for PDF-width
									if ( strstr(strtolower($phrase), 'width:') )
										{
											$tpm1 = explode(':',$phrase);
											$width = trim($tpm1[1], '"');
										} 
									
// Parse for adobe reader toolbar 2010-11-10
									if ( strstr(strtolower($phrase), 'toolbar:') )
										{
											$tpm1 = explode(':',$phrase);
											$ar_toolbar = trim($tpm1[1], '"');
										}
									
// Parse for adobe reader navpanel 2010-11-10
									if ( strstr(strtolower($phrase), 'nav:') )
										{
											$tpm1 = explode(':',$phrase);
											$ar_navpanes = trim($tpm1[1], '"');
										}
									
// Parse for adobe reader scrollbar 2010-11-10
									if ( strstr(strtolower($phrase), 'scroll:') )
										{
											$tpm1 = explode(':',$phrase);
											$ar_scrollbar = trim($tpm1[1], '"');
										}
									
// Parse for adobe reader searchpanel 2010-11-10
									if ( strstr(strtolower($phrase), 'search:') )
										{
											$tpm1 = explode(':',$phrase);
											$ar_searchbar = trim($tpm1[1], '"');
										}
									
// Parse for google reader page-Setting 2010-12-15 << THANKS TO Andreas Seifert (as[at]nsi[dot]de)
									if ( strstr(strtolower($phrase), 'page:') )
										{
											$ar_gpage = null;
											$tpm1 = explode(':',$phrase);
											$ar_gpage = trim($tpm1[1], '"');
											
											if ( !$ar_gpage < 1 )
												{
													$ar_gpage = $ar_gpage - 1;	
												}
											else
												{
													$ar_gpage = 0; 
												}
												
										}
									else
										{
											$ar_gpage = 0;	
										}
									
									
// [[[ Parse for the PDF-app
									if ( strstr(strtolower($phrase), 'app:') )
										{
											$tpm1 = explode(':',$phrase);
											$tpm1[1] = rtrim($tpm1[1], "{/pdf}");
											$app = trim(strtolower($tpm1[1]), '"');
											
											$alink = showpdfpath($alt_link, $link_comment, $placeholder, $enpdf);
											
											if ( $app == "acrobat" || $app == "adobe" )
												{
													// 2010-10-10 Fix for disabled acrobat plugin in IE
													if ( $isIE == 1 ) 
														{
															$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" 
															width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />
															<param name=\"wmode\" value=\"transparent\"> PDF plugin is deactivated! Please click the link</br>
															<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
															</object>".$alink;	
														}
													else
														{
															$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" 
															wmode="transparent" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'" hidden="false">'.$alink;
														}
												}
												
											if ( $app == "google" )
												{
													$enpdf = urlencode($enpdf);
													$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$enpdf.'&embedded=true
													'.($ar_gpage != null ? '#:0.page.'.$ar_gpage : '' ).'" width="'.$width.'" height="
													'.$height.'" style="border: none;"></iframe>'.$alink;
												}
											
// [[[ 2010-12-13 Let's show doc from an Google Docs account, too !!
											if ( $app == "gdoc" )
												{
													$directlink = 'https://docs.google.com/document/pub?id='.$enpdf;
													$alink = showpdfpath($alt_link, $link_comment, $placeholder, $directlink);
													$replacement[$mcount] = '<iframe width="'.$width.'" 
													height="'.$height.'" style="border: medium none;" 
													src="https://docs.google.com/document/pub?id='.$enpdf.'&amp;embedded=true">
													</iframe>'.$alink;
												}	
// ]]] 2010-12-13 Let's show doc from an Google Docs account, too !!
											
											
// [[[ 2010-12-13 Added Zoho viewer to display documents embedded.
											if ( $app == "zoho" )
												{
													$enpdf = urlencode($enpdf);
													$replacement[$mcount] = '<iframe src="http://viewer.zoho.com/docs/urlview.do?url='.$enpdf.'&embed=true" 
													width="'.$width.'" height="'.$height.'" frameborder="0" displayfilename="false"></iframe>'.$alink;	
												}
											//]]] 2010-12-13 Added Zoho viewer to display documents embedded.
											
											
// [[[ 2010-12-17 Added Flapdf viewer (http://www.looky-look.net) to display documents embedded.
											if ( $app == "flash" )
												{
													$replacement[$mcount] = '<iframe src="http://looky-look.net/flapdf/?url='.$enpdf.'" 
													width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" allowtransparency="true"></iframe>'.$alink;	
												}
//]]] 2010-12-17 Added Flapdf viewer (http://www.looky-look.net) to display documents embedded.
											
											
										}
									// ]]] Parse for the PDF-app
									else
									{
										$ar_param = acroreadparams($ar_toolbar, $ar_navpanes, $ar_scrollbar, $ar_searchbar); // Let's check for Acrobat Reader options !
										
										if ( $mode == 1 ) // If app:google
											{
												$genpdf = urlencode($enpdf);
												$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$genpdf.'&embedded=true
												'.($ar_gpage != null ? '#:0.page.'.$ar_gpage : '' ).'" width="'.$width.'"  height="'.$height.'" 
												style="border: none;"></iframe>'.$alink;
											}
										else
											{
// [[[ 2010-10-10 Fix for disabled acrobat plugin in IE
												if ( $isIE == 1 ) 
													{
														$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" 
														width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />
														<param name=\"wmode\" value=\"transparent\"> PDF plugin is deactivated! Please click the link</br>
														<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
														</object>";	
													}
												else
													{
														$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" 
														wmode="transparent" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'">'.$alink;	
													}
// ]]] 2010-10-10 Fix for disabled acrobat plugin in IE
											}	
									}
								}
						}
					else // If there are no settings at the string in article, this code will be used.
						{
							$enpdf1 = ltrim($enpdf, "{pdf}");
							$enpdf1 = rtrim($enpdf1, '/pdf}');
							$enpdf = rtrim($enpdf1, '{');
							
							$alink = showpdfpath($alt_link, $link_comment, $placeholder, $enpdf);
							
							
							if ( $mode == 1 ) // If app|google
								{
									$enpdf = urlencode($enpdf);
									$replacement[$mcount] = '<iframe src="'.$googlepath.'?url='.$enpdf.'&embedded=true" 
									width="'.$width.'" height="'.$height.'" style="border: none;"></iframe>'.$alink;
								}
							else
								{
									$ar_param = acroreadparams($ar_toolbar, $ar_navpanes, $ar_scrollbar, $ar_searchbar); // Let's check for Acrobat Reader options !
									
									// 2010-10-10 Fix for disabled acrobat plugin in IE
									if ( $isIE == 1 ) 
										{
											$replacement[$mcount] = "<object classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\" 
											width=\"$width\" height=\"$height\"><param name=\"src\" value=\"$enpdf$ar_param\" />
											<param name=\"wmode\" value=\"transparent\">PDF plugin is deactivated! Please click the link</br>
											<a href=\"$enpdf\" target=\"_blank\">$enpdf</a> 
											</object>".$alink;	
										}
									else
										{
											$replacement[$mcount] = '<embed width="'.$width.'" height="'.$height.'" 
											wmode="transparent" href="'.$enpdf.'" src="'.$enpdf.$ar_param.'" hidden="false">'.$alink;
										}
								}
						}
						
// Re-reset of Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
						$o_tb = $ar_toolbar = $o_tb;
						$ar_navpanes = $o_np;
						$ar_scrollbar = $o_scb;
						$ar_searchbar = $o_sb;
// Re-reset of Adobe Reader default settings (Toolbar, Navpanel, Scrollbar) 2010.11.10
						
				    	$mcount = $mcount + 1;
					}
					
					
// [[[ Replace the original content with the added pdf content of article.
					for( $i = 0; $i < count($replacement); $i++ )
						{
						    $row->text = preg_replace( $regex, $replacement[$i], $row->text,1);
						}
		        	// ]]] Replace the original content with the added pdf content of article.
				}
				
			return true;
				
    	}
}
?>