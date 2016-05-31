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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'importevents.cancel' || document.formvalidator.isValid(document.id('importevents-form')))
		{
			Joomla.submitform(task, document.getElementById('importevents-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_simplecalendar&task=importevents.save'); ?>" method="post" name="adminForm" id="importevents-form" class="form-validate form-horizontal" enctype="multipart/form-data">
<div class="span10 form-horizontal">

	<fieldset>
			<div class="tab-pane" id="otherparams">
				<?php foreach ($this->form->getFieldset('details') as $field) : ?>
					<div class="control-group">
						<div class="control-label" style="width:250px;">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
