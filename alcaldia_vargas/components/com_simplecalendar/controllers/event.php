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
 * @subpackage  com_content
 */
class SimplecalendarControllerEvent extends JControllerForm
{
	/**
	 * @since   3.0
	 */
	protected $view_item = 'form';
	
	/**
	 * @since   3.0
	 */
	//protected $view_list = 'events';
	
	/**
	 * Method to add a new record.
	 *
	 * @return  boolean  True if the event can be added, false if not.
	 * @since   3.0
	 */
	public function add()
	{
		JRequest::setVar( 'view', 'form' );
		if (!parent::add())
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}
	
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  An array of input data.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', $this->input->getInt('catid'), 'int');
		$allow		= null;
	
		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_simplecalendar.category.'.$categoryId);
		}
	
		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
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
		parent::cancel($key);
	
		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string	$key	The name of the primary key of the URL variable.
	 * @param   string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  Boolean	True if access level check and checkout passes, false otherwise.
	 * @since   3.0
	 */
	public function edit($key = null, $urlVar = 'id')
	{
		$this->input->set('view', 'form');
		$this->input->set('task', 'edit');
		$this->input->set('return',base64_encode($this->getReturnPage()));
		$result = parent::edit($key, $urlVar);
	
		return $result;
	}
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string	$name	The model name. Optional.
	 * @param   string	$prefix	The class prefix. Optional.
	 * @param   array  $config	Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.5
	 */
	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		
		$model = parent::getModel($name, $prefix, $config);
		
		return $model;
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId	The primary key id for the item.
	 * @param   string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return  string	The arguments to append to the redirect URL.
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		// Need to override the parent method completely.
// 		$view 	= $this->input->get('view');
		$tmpl   = $this->input->get('tmpl');
		$layout = $this->input->get('layout', 'edit');
		$append = '';
	
		// Setup redirect info.
// 		if ( $view ) 
// 		{
// 			$append .= '&view=' . $view;	
// 		}
		if ($tmpl)
		{
			$append .= '&tmpl='.$tmpl;
		}
	
		// TODO This is a bandaid, not a long term solution.
		//		if ($layout) {
		//			$append .= '&layout='.$layout;
		//		}
		$append .= '&layout=edit';
	
		if ($recordId)
		{
			$append .= '&'.$urlVar.'='.$recordId;
		}
	
		$itemId	= $this->input->getInt('Itemid');
		$return	= $this->getReturnPage();
		$catId  = $this->input->getInt('catid', null, 'get');
	
		if ($itemId)
		{
			$append .= '&Itemid='.$itemId;
		}
	
		if ($catId)
		{
			$append .= '&catid='.$catId;
		}
	
		if ($return)
		{
			$append .= '&return='.base64_encode($return);
		}
	
		return $append;
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
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}
	
	/**
	 * Method to save a record.
	 *
	 * @param   string	$key	The name of the primary key of the URL variable.
	 * @param   string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  Boolean	True if successful, false otherwise.
	 * @since   3.0
	 */
	public function save($key = null, $urlVar = 'id')
	{
		// Load the backend helper for filtering.
		require_once JPATH_ADMINISTRATOR.'/components/com_simplecalendar/helpers/simplecalendar.php';
		$app	= JFactory::getApplication();
		
		$data 	=  $app->input->get('jform', array(),'array');
		
		if ( isset($data['id']) && $data['id'] != 0 ) {
			$isNew = false;
		} else {
			$isNew = true;
		}

		$result = parent::save($key, $urlVar);
		
		// If ok, redirect to the return page.
		if ($result)
		{
			if ( $isNew )
			{
				$this->setRedirect(JRoute::_('index.php?option=com_simplecalendar&view=event&id=' . $id));
			} 
			else 
			{
				$this->setRedirect($this->getReturnPage());
			}
		}
		else 
		{
			$msg = JText::_('COM_SIMPLECALENDAR_ERROR_NOT_SAVED');
			$this->setRedirect($this->getReturnPage(), $msg);
		}
	
		return $result;
	}
	
	
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   array         $validData   The validated data.
	 *
	 * @return  void
	 *
	 * @since   3.0.3
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$params	= JComponentHelper::getParams('com_simplecalendar');
		$user 	= JFactory::getUser();
		
		$id = (int)$model->getState($model->getName() . '.id');
// 		$catid = (int)$model->getState($model->getName() . '.catid');
		$isNew = (int)$model->getState($model->getName() . '.new');
		
		$validData['id'] = $id;
		
// 		var_dump($id, $catid, $this->getReturnPage());
// 		exit;
		
		if ( $params->get('use_frontend_submission_email', '0') == '1' && $isNew == '1' )
		{
			$sent = $this->_sendMail($user, $validData);
		}

		// Set the redirect
// 		if ( $isNew )
// 		{
// 			$link = JRoute::_('index.php?option=com_simplecalendar&view=event&id=' . $id . '&catid=' . $catid);
// 		}
// 		else 
// 		{
			$link = $this->getReturnPage();
// 		}
		
		// Done. Redirecting...
		if ( is_object($sent) )
		{
// 			$msg = JText::sprintf('COM_SIMPLECALENDAR_MAIL_ERROR_NOT_SENT %s', $sent->getError());
			$msg = $sent->getError();
			$this->setRedirect($this->getReturnPage(), $msg);
		}
		else
		{
			$this->setRedirect($link);
		}
		return $sent;
	}
	
	/**
	 * Sends an e-mail to all super admins and notifies them of a new post.
	 * @param user $user
	 * @param event $event
	 * @return void
	 * @since 0.7.16b
	 */
	private static function _sendMail($user, $event)
	{
		require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'output.class.php');
		$app 			= JFactory::getApplication();
		$db				= JFactory::getDBO();
		
		$name			= '';
		$email			= '';
		$subject		= '';
		$body 			= '';
		$body2			= '';
		
		$params			= JComponentHelper::getParams( 'com_simplecalendar' );
		
		$sitename 		= $app->getCfg( 'sitename' );
		
		$mailfrom 		= $app->getCfg( 'mailfrom' );
		$fromname 		= $app->getCfg( 'fromname' );
		$siteURL		= JURI::base();
		
		$sendMailTo 	= $params->get('send_notifications_to', '');
	
		$link = JURI::root() . 'index.php?option=com_simplecalendar&view=event&id=' . $event['id'];
		$link = JRoute::_($link);
		
		$subject 	= sprintf ( JText::_( 'COM_SIMPLECALENDAR_MAIL_NEW_EVENT_POSTED_ON' ), $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);
			
		$footer = '<p>---<br/>';
		$footer .= '<a href="' . $siteURL . '">' . JText::_('COM_SIMPLECALENDAR_MAIL_SIMPLECALENDAR_ON') . ' ' . $sitename . '</a>';
		if ( !SCOutput::isValidated() )
		{
			$footer .= ' - ' . JText::_('COM_SIMPLECALENDAR_SIMPLECALENDAR_NON_VALIDATED_VERSION');
		}
		$footer .= '</p>';
		
		$sent = false;
		
		if ( $user->get('id') != 0 )
		{ 	// only for logged-in users
			$name 		= $user->get('name');
			$email 		= $user->get('email');
			$username 	= $user->get('username');

				
			$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
                 <html>
                    <head>
                       <title>'.$subject.'</title>
                       <base href="'.JURI::root().'" />
                    </head>
                    <body>';
			/* 	Name
				Event name
				Start date
				VEnue
				URL (if available)
				Site name
				Site URL
				*/
			if ( $params->get('frontend_approve', '1') == '1' )
			{
				$body .= '<p>' . sprintf ( JText::_( 'COM_SIMPLECALENDAR_MAIL_SEND_TO_AUTHOR_APPROVE' ), 
							$name, 
							$event['name'],
							$event['start_dt'],
							$event['venue'],
							$link,
							$sitename,
							$siteURL
				) . '</p>';
			} 
			else 
			{
				$body .= '<p>' . sprintf ( JText::_( 'COM_SIMPLECALENDAR_MAIL_SEND_TO_AUTHOR' ),
							$name, 
							$event['name'],
							$event['start_dt'],
							$event['venue'],
							$link,
							$sitename,
							$siteURL
				) . '</p>';
			}
	
			$body .= $footer . '</body></html>';
			$body = html_entity_decode($body, ENT_QUOTES);
	
			// Send email to user
			if ( !$mailfrom  || !$fromname )
			{
				$fromname = $user->name;
				$mailfrom = $user->email;
			}
			
			$mail = JFactory::getMailer();
			$mail->IsHTML(true);
			$mail->addRecipient(array($email, $name));
// 			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($subject);
			$mail->setBody($body);
			$sent = $mail->Send();
			unset($mail);

		}
		
		if ( $sendMailTo != '' && $sendMailTo != $email ) {
			$name = JText::_('COM_SIMPLECALENDAR_MAIL_UNREGISTERED_USER');
			$email = JText::_('COM_SIMPLECALENDAR_MAIL_NO_EMAIL_ADDRESS');
			$subject2 = JText::_( 'COM_SIMPLECALENDAR_MAIL_NEW_EVENT_POSTED_ON') . ' ' . $sitename;
			$subject2 = html_entity_decode($subject2, ENT_QUOTES);
		
			$body2 = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	                 <html>
	                    <head>
	                       <title>'.$subject.'</title>
	                       <base href="'.JURI::root().'" />
	                    </head>
	                    <body>';
		
			$body2 .= '<p>' . sprintf (
					JText::_( 'COM_SIMPLECALENDAR_MAIL_SEND_TO_ADMIN' ),
					$sitename,
					$event['name'],
					$event['start_dt'],
					$event['venue'],
					$name,
					$email,
					$link
			) . '</p>';
			
			$body2 .= $footer . '</body></html>';
			$body2 = html_entity_decode($body2, ENT_QUOTES);

			$mail = JFactory::getMailer();
			$mail->IsHTML(true);
			$mail->addRecipient($sendMailTo);
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($subject2);
			$mail->setBody($body2);
			$sent = $mail->Send();
			unset($mail);
		}
		return $sent;
	}
}
?>