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

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_simplecalendar
 */
class SimplecalendarControllerEditcss extends JControllerLegacy
{
	/**
	 * @since   3.0
	 */
	protected $view_list = 'editcss';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('applycss', 'savecss');
	}
	
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array  $data	An array of input data.
	 * @param   string	$key	The name of the key for the primary key.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$asset		= 'com_simplecalendar.event.'.$recordId;
	
		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}
	
		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);
	
				if (empty($record))
				{
					return false;
				}
	
				$ownerId = $record->created_by;
			}
	
			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
	
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string	$key	The name of the primary key of the URL variable.
	 *
	 * @return  Boolean	True if access level checks pass, false otherwise.
	 * @since   3.0
	 */
	public function cancel($key = 'id')
	{
		// Redirect to the return page.
		$this->setRedirect(
			'index.php?option=com_simplecalendar&view=events',
			JText::_('COM_SIMPLECALENDAR_EDIT_CSS_ABORT'),
			'warning'
		);
	}
	

	
	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return  string	The return URL.
	 * @since   1.6
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');
	
		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JURI::base();
		}
		else
		{
			return base64_decode($return);
		}
	}
	
	
	/**
	 * Saves the css
	 *
	 */
	public function savecss()
	{
		$app = JFactory::getApplication();

		JRequest::checkToken() or die( 'Invalid Token' );

		// Initialize some variables
		$option			= $app->input->get('option');
		$filename		= $app->input->get('filename', '', 'post', 'cmd');
		$filecontent	= $app->input->get('filecontent', '', '', '', JREQUEST_ALLOWRAW);

		if (!$filecontent) {
			$app->redirect('index.php?option='.$option.'&view=events', JText::_('COM_SIMPLECALENDAR_CSS_OPERATION_FAILED').': '.JText::_('COM_SIMPLECALENDAR_EDIT_CSS_ERROR_CONTENT_EMPTY'));
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		$file = JPATH_SITE.DS.'components'.DS.'com_simplecalendar'.DS.'assets'.DS.'css'.DS.$filename;

		// Try to make the css file writeable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
			JError::raiseNotice('SOME_ERROR_CODE', 'COM_SIMPLECALENDAR_EDIT_CSS_ERROR_COULD_NOT_MAKE_CSS_FILE_WRITABLE');
		}

		jimport('joomla.filesystem.file');
		$return = JFile::write($file, $filecontent);

		// Try to make the css file unwriteable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
			JError::raiseNotice('SOME_ERROR_CODE', 'COM_SIMPLECALENDAR_EDIT_CSS_ERROR_COULD_NOT_MAKE_CSS_FILE_UNWRITABLE');
		}

		if ($return)
		{
			$task = JRequest::getVar('task');
			switch($task)
			{
				case 'applycss' :
					$app->redirect(
						'index.php?option=com_simplecalendar&view=editcss',
					JText::_('COM_SIMPLECALENDAR_EDIT_CSS_FILE_SUCCESSFULLY_EDITED')
					);
					break;

				case 'savecss'  :
				default         :
					$app->redirect(
						'index.php?option=com_simplecalendar&view=events',
					JText::_('COM_SIMPLECALENDAR_EDIT_CSS_FILE_SUCCESSFULLY_EDITED')
					);
					break;
			}
		} else {
			$app->redirect(
				'index.php?option=com_simplecalendar&view=events',
				JText::_('OPERATION FAILED').': '.JText::sprintf('FAILED TO OPEN FILE FOR WRITING', $file)
			);
		}
	}
}
?>