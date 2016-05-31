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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SimplecalendarViewEditcss extends JViewLegacy
{

	function display($tpl = null) {
		$app  = JFactory::getApplication();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.keepalive');

		$params 	= JComponentHelper::getParams( 'com_simplecalendar' );
		$document 	= JFactory::getDocument();
		$user 		= JFactory::getUser();
		$option		= $app->input->get('option');
		
		require_once JPATH_COMPONENT.'/helpers/simplecalendar.php';
		
		SimpleCalendarHelper::addSubmenu('editcss');
			
		$path		= JPATH_SITE.DS.'components'.DS.'com_simplecalendar'.DS.'assets'.DS.'css';
		$files 		= $this->getFolderContents($path);
				
		$filename 	= $app->input->get('filename', $files[0]);
		$css_path	= $path.DS.$filename;
		
		$this->addToolbar();
		
		//read the the stylesheet
		jimport('joomla.filesystem.file');
		$content = JFile::read($css_path);
		
		jimport('joomla.client.helper');
//		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');

		if ($content !== false)
		{
			$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');
		}
		else
		{
			$msg = JText::sprintf('COM_SIMPLECALENDAR_EDITCSS_ERROR_FAILED_OPENING_FILE_FOR_WRITING', $css_path);
			$app->redirect('index.php?option=com_simplecalendar', $msg);
		}

		//assign data to template
		$this->path		= $path;
		$this->selectbox= $this->getFileSelectBox($files, $filename);
		$this->css_path = $css_path;
		$this->content 	= $content;
		$this->files 	= $files;
		$this->filename = $filename;
		
		parent::display($tpl);
		
		// Footer
		echo SCOutput::showFooter();
	}
	
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/simplecalendar.php';
		JFactory::getApplication()->input->set('hidemainmenu', true);
		JToolbarHelper::title( JText::_('COM_SIMPLECALENDAR_MANAGER_EDIT_CSS'), 'sc.png');
	
		$user = JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolbarHelper::apply('editcss.applycss');
		JToolbarHelper::save('editcss.savecss');
		JToolbarHelper::cancel('editcss.cancel');
	
	}
	
	protected function getFolderContents($path='') 
	{
		if ( $path == '' )
		{
			$msg = JText::_('COM_SIMPLECALENDAR_EDITCSS_ERROR_NO_PATH');
			$app->redirect('index.php?option=com_simplecalendar', $msg);
		}
		if ( $handle = opendir($path) ) 
		{
			/* This is the correct way to loop over the directory. */
			while (false !== ($entry = readdir($handle))) 
			{
				if ( $entry != '.' && $entry != '..' ) 
				{
					$items[] = $entry;
				}
			}

			closedir($handle);
		}
		return $items;
	}
	
	protected function getFileSelectBox($files=array(), $default='') 
	{
		if ( empty($files) ) 
		{
			return '';
		}
		else 
		{
			$options = array();
			foreach ( $files as $file )
			{
				$options[] = JHtml::_('select.option', $file, $file);
			}
			$js = "function loadCSS(file) {
						document.getElementById('filename').value = file;
						window.location = document.URL + '&filename=' + file;
					}";
			$document 	= JFactory::getDocument();
			$document->addScriptDeclaration($js);
			return JHtml::_('select.genericlist', $options, 'cssfile', 'class="input-small" onchange="loadCSS(this.options[this.selectedIndex].value);"', 'text', 'value', $default);
		}
	}
}
?>