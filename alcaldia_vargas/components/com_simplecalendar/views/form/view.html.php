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

// JHTML::stylesheet('simplecal_front.css','components/com_simplecalendar/assets/css/');

// require(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'recaptchalib.php');

class SimplecalendarViewForm extends JViewLegacy
{
	protected $form;
	
	protected $item;
	
	protected $return_page;
	
	protected $state;
	
	protected $canDo;

	function display($tpl = null)
	{
		
	  	$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		
		$this->state		= $this->get('State');
// 		var_dump($this->state);
// 		exit;
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
		
		$this->canDo = SimpleCalendarHelper::getActions();
		
		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_simplecalendar') || (count($user->getAuthorisedCategories('com_simplecalendar', 'core.create')));
		}
		else
		{
// 			$authorised = $this->item->params->get('access-edit');
			$authorised = $this->canDo->get('core.create');
			//$this->form->removeField('captcha');
		}
		
		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		
		$this->params = $params;
		$this->user   = $user;
		
		// What Access Permissions does this user have? What can (s)he do?
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'simplecalendar.php');
		$this->canDo = SimpleCalendarHelper::getActions($this->item->id);
		
// 		if ($params->get('enable_category') == 1)
// 		{
// 			$this->form->setFieldAttribute('catid', 'default', $params->get('catid', 1));
// 			$this->form->setFieldAttribute('catid', 'readonly', 'true');
// 		}
		$this->_prepareDocument();
// 		var_dump($this);
// 		exit;

		$this->_formJS();
		
		parent::display($tpl);
		
	}
	
	/**
	 * Prepares the document
	 * @since 3.0
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;
	
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SIMPLECALENDAR_FORM_EDIT_EVENT'));
		}
	
		$title = $this->params->def('page_title', JText::_('COM_SIMPLECALENDAR_FORM_EDIT_EVENT'));
		if ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);
	
		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');
	
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
	
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
	
	private static function _formJS()
	{
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_simplecalendar');
		$array = array('End', 'Reserve');
		foreach ( $array as $field )
		{
			$script = "
					function show" . $field . "Date(curr) {
						document.getElementById('span_" . strtolower($field) . "_dt').style.display='inline';
						document.getElementById('span_" . strtolower($field) . "_dt_checkbox').style.display='none';
						document.getElementById('jform_" . strtolower($field) . "_dt').value = document.getElementById('jform_start_dt').value;";
			if ( $field == 'End' )
			{
				$script .= "
						var sel = document.getElementById('jform_end_time_hours');
						var val = document.getElementById('jform_start_time_hours').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_hours' ).trigger( 'liszt:updated' );
					           break;
					        }
	
					    }
						var sel = document.getElementById('jform_end_time_minutes');
						var val = document.getElementById('jform_start_time_minutes').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_minutes' ).trigger( 'liszt:updated' );
					           break;
					        }
	
					    }";
				if ( $params->get('use_24h', '1') == '0' )
				{
					$script .= "var sel = document.getElementById('jform_end_time_ampm');
						var val = document.getElementById('jform_start_time_ampm').value;
					    for(var i = 0, j = sel.options.length; i < j; ++i) {
					        if(sel.options[i].value == val) {
							   sel.selectedIndex = i;
								jQuery('#jform_end_time_ampm' ).trigger( 'liszt:updated' );
					           break;
					        }
	
					    }";
				}
			}
			$script .= "}";
			$document->addScriptDeclaration($script);
		}
	}
}
?>