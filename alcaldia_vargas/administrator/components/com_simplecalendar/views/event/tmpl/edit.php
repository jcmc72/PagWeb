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
		// Check for correct start and end dates
		var start_dt = document.getElementById('jform_start_dt' );
		var end_dt = document.getElementById('jform_end_dt' );
		
		var n = start_dt.value.indexOf('.');
		//alert(n);
		if ( n == -1 || isNaN(n) ) {
			var dateSplit1 = start_dt.value.split('-');
			var dateSplit2 = end_dt.value.split('-');
		} else {
			var dateSplit1 = start_dt.value.split('.');
			var dateSplit2 = end_dt.value.split('.');
		}
		if ( dateSplit1[0].length == 4 ) {
			var d1 = new Date(dateSplit1[0], dateSplit1[1]-1, dateSplit1[2]);
			var d2 = new Date(dateSplit2[0], dateSplit2[1]-1, dateSplit2[2]);
		} else {
			var d1 = new Date(dateSplit1[2], dateSplit1[1]-1, dateSplit1[0]);
			var d2 = new Date(dateSplit2[2], dateSplit2[1]-1, dateSplit2[0]);
		} 
		//alert(d1+" "+d2);
		if ( task != 'event.cancel' && 	end_dt.value != '' && d1 > d2 )
		{
			
			alert('<?php echo JText::_('COM_SIMPLECALENDAR_EVENT_WARNING_END_DATE_PRIOR_TO_START_DATE'); ?>');
			document.getElementById('jform_end_dt' ).setAttribute('class', 'input-small invalid');
			document.getElementById('jform_end_dt' ).focus();
			return false;
		}
		else
		{
			if (task == 'event.cancel' || document.formvalidator.isValid(document.id('simplecalendar-form')))
			{
				<?php echo $this->form->getField('description')->save(); ?>
				Joomla.submitform(task, document.getElementById('simplecalendar-form'));
			}
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_simplecalendar&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="simplecalendar-form" class="form-validate form-horizontal">
<div class="span10 form-horizontal">

	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_EVENT_TAB_DETAILS');?></a></li>
			<li><a href="#organizer" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_EVENT_TAB_ORGANIZER');?></a></li>
			<li><a href="#extended" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_EVENT_TAB_EXTINFO');?></a></li>
			<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_EVENT_TAB_PUBLISHING');?></a></li>
			<?php if ( $this->canDo->get('core.admin') ): ?>
			<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_EVENT_TAB_PERMISSIONS'); ?></a></li>
			<?php endif; ?>
			<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('name'); ?>		
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('alias'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('alias'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('venue'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('venue'); ?><br />
						<?php echo $this->form->getInput('address'); ?><br />
						<?php echo $this->form->getInput('latlon'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('start_dt'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('start_dt'); ?>&nbsp;
						<?php echo $this->form->getInput('start_time'); ?>
					</div>
				</div>
				<div class="control-group">
					<?php if ( $this->item->end_dt != null && $this->item->end_dt != '' && $this->item->end_dt != '0000-00-00' ): ?>
					<div class="control-label">
						<?php echo $this->form->getLabel('end_dt'); ?>
					</div>
					<div class="controls">
						<span id="span_end_dt"><?php echo $this->form->getInput('end_dt'); ?>&nbsp;
						<?php echo $this->form->getInput('end_time'); ?></span>
					</div>
					<?php else: ?>
					<div class="control-label">
						<?php echo $this->form->getLabel('end_dt'); ?>
					</div>
					<div class="controls">
						<span id="span_end_dt_checkbox"><small><?php echo '<input type="checkbox" id="show_end_dt" onclick="showEndDate(this)" value="" />' . ' ' . JText::_('COM_SIMPLECALENDAR_EVENT_FIELD_ADD_DATE'); ?></small></span>
						<span id="span_end_dt" style="display:none;"><?php echo $this->form->getInput('end_dt'); ?>&nbsp;
						<?php echo $this->form->getInput('end_time'); ?></span>
					</div>
					<?php endif;?>
				</div>
				<div class="control-group">
					<?php if ( $this->item->reserve_dt != null && $this->item->reserve_dt != '' && $this->item->reserve_dt != '0000-00-00' ): ?>
					<div class="control-label">
						<?php echo $this->form->getLabel('reserve_dt'); ?>
					</div>
					<div class="controls">
						<span id="span_reserve_dt"><?php echo $this->form->getInput('reserve_dt'); ?></span>
					</div>
					<?php else: ?>
					<div class="control-label">
						<?php echo $this->form->getLabel('reserve_dt'); ?>
					</div>
					<div class="controls">
						<span id="span_reserve_dt_checkbox"><small><?php echo '<input type="checkbox" id="show_reserve_dt" onclick="showReserveDate(this)" value="" />' . ' ' . JText::_('COM_SIMPLECALENDAR_EVENT_FIELD_ADD_DATE'); ?></small></span>
						<span id="span_reserve_dt" style="display:none;"><?php echo $this->form->getInput('reserve_dt'); ?></span>
					</div>
					<?php endif; ?>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="organizer">
				<?php foreach ($this->form->getFieldset('organizer') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="tab-pane" id="extended">
			<div id="image">
						<?php foreach ($this->form->getFieldset('image') as $field) : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endforeach; ?>
				</div>
			<?php foreach ($this->form->getFieldset('extended') as $field) : ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="tab-pane" id="publishing">
				<?php foreach ($this->form->getFieldset('publish') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="tab-pane" id="otherparams">
				<?php foreach ($this->form->getFieldset('otherparams') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ($this->canDo->get('core.admin')) : ?>
			<div class="tab-pane" id="permissions">
				<fieldset>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<?php endif; ?>
			<div class="tab-pane" id="metadata">

				<?php foreach ($this->form->getFieldset('metadata') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
	<!-- Begin Sidebar -->
	<div class="span2">
		<h4><?php echo JText::_('JDETAILS');?></h4>
		<hr />
		<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getValue('name') . ', ' . $this->form->getValue('venue'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('catid'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('catid'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('statusid'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('statusid'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('state'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('state'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
		</fieldset>
	</div>
	<!-- End Sidebar -->
</form>
