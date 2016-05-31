<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');

/**
 * Plugin Select HTML for SL_Menu.
 *
 * @package		Joomla.Module
 * @subpakage	Skyline.Menu
 */
class JFormFieldSlides extends JFormField {
	/** @var string Field type */
	protected $type		= 'slides';

	/**
	 * Method to get the name used for the field input tag.
	 *
	 * @param   string  $fieldName  The field element name.
	 * @return  string  The name to be used for the field input tag.
	 */
	protected function getBaseName($item = '') {
		$fieldName	= $this->fieldname;
		$name		= '';

		// If there is a form control set for the attached form add it first.
		if ($this->formControl) {
			$name .= $this->formControl;
		}

		// If the field is in a group add the group control to the field name.
		if ($this->group) {
			// If we already have a name segment add the group control as another level.
			$groups = explode('.', $this->group);
			if ($name) {
				foreach ($groups as $group) {
					$name .= '[' . $group . ']';
				}
			} else {
				$name .= array_shift($groups);
				foreach ($groups as $group) {
					$name .= '[' . $group . ']';
				}
			}
		}

		// If we already have a name segment add the field name as another level.
		if ($name) {
			$name .= '[' . $fieldName . ']';
		} else {
			$name .= $fieldName;
		}

		if ($item) {
			$name .= '[' . $item . '][]';
		}

		return $name;
	}

	/**
	 * Method to get the id used for the field input tag.
	 *
	 * @param	int		$index	The id of sub item of field.
	 * @param	string	$item	The field element name.
	 *
	 * @return	string	The id to be used for the field input tag.
	 */
	protected function getBaseId($index = '', $item = '') {
		$id = $this->id;

		if ($index !== null) {
			$id .= '_' . $index;
		}

		if ($item) {
			$id .= '_' . $item;
		}

		return $id;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput() {
		require_once(dirname(__FILE__) . '/../helper.php');

		// add asset
		JHtml::_('jquery.ui', array('core', 'sortable'));
		JHtml::_('script', 'jui/sortablelist.js', false, true);
		JHtml::_('behavior.modal');
		JHtml::_('script', 'mod_sl_slicebox/adminscript.js', array(), true);
		JHtml::_('stylesheet', 'mod_sl_slicebox/template.css', array(), true);

		// Initialize variables
		$html	= array();
		$items	= modSL_SliceboxHelper::getItems($this->value);

		$document	= JFactory::getDocument();
		$document->addScriptDeclaration('Skyline.Slicebox.slideCount = ' . count($items) . ';');

		$html[]	= '<ul id="' . $this->getBaseId('container', '') . '" class="slides-container">';

		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item	= $items[$i];

			$html[]	= '<li>';
			$html[]	= '<a class="slide-delete icon-cancel" href="javascript:void(0)"></a>';
			$html[]	= '<div class="slide-sortable"></div>';
			$html[]	= '<div class="slide-container">';
			$html[]	= '<div class="control-group">';
			$html[]	= '<div class="input-append">';
			$html[]	= '<input type="text" id="' . $this->getBaseId($i, 'image') . '" name="' . $this->getBaseName('image') . '" placeholder="' . JText::_('MOD_SL_SLICEBOX_IMAGE') . '" value="' . $item->image . '" />';
			$html[]	= '<a class="modal btn" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_media&view=images&tmpl=component&fieldid=' . $this->getBaseId($i, 'image') . '">' . JText::_('MOD_SL_SLICEBOX_SELECT_IMAGE') . '</a>';
			$html[]	= '</div>';
			$html[]	= '</div>';
			$html[]	= '<div class="control-group">';
			$html[]	= '<div class="input-append">';
			$html[]	= '<input type="text" name="' . $this->getBaseName('link') . '" placeholder="' . JText::_('MOD_SL_SLICEBOX_LINK') . '" value="' . $item->link . '" />';
			$html[]	= '<input type="hidden" name="' . $this->getBaseName('link_nw') . '" value="' . $item->link_nw . '" />';
			$html[]	= '<button type="button" class="chk_link_nw btn' . ($item->link_nw ? ' active' : '') . '" data-toggle="button">' . JText::_('MOD_SL_SLICEBOX_LINK_NEW_WINDOW') . '</button>';
			$html[]	= '</div>';
			$html[]	= '</div>';
			$html[]	= '<div class="control-group">';
			$html[]	= '<input type="text" name="' . $this->getBaseName('title') . '" placeholder="' . JText::_('MOD_SL_SLICEBOX_TITLE') . '" value="' . $item->title . '" />';
			$html[]	= '</div>';
			$html[]	= '<div class="control-group">';
			$html[]	= '<textarea rows="3" name="' . $this->getBaseName('text') . '" placeholder="' . JText::_('MOD_SL_SLICEBOX_TEXT') . '">' . $item->text . '</textarea>';
			$html[]	= '</div>';
			$html[]	= '</div>';

			$html[]	= '</li>';
		}

		$html[]	= '</ul>';

		$html[]	= '<div class="control-group">';
		$html[]	= '<button type="button" class="btn btn-success" onclick="Skyline.Slicebox.addSlide(\'' . $this->getBaseId('container', '') . '\');">' . JText::_('MOD_SL_SLICEBOX_ADD_SLIDE') . '</button>';
		$html[]	= '</div>';

		return implode("\n", $html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel() {
		return '';
	}
}