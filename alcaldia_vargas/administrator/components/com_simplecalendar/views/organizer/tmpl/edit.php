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
// var_dump($this->item);
// exit;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		// Check for correct start and end dates 
		if (task == 'organizer.cancel' || document.formvalidator.isValid(document.id('organizer-form')))
		{
			Joomla.submitform(task, document.getElementById('organizer-form'));
		}
	}
//	window.addEvent('domready', function()
//	{
//		document.id('jform_type0').addEvent('click', function(e){
//			document.id('image').setStyle('display', 'block');
//			document.id('url').setStyle('display', 'block');
//			document.id('custom').setStyle('display', 'none');
//		});
//		document.id('jform_type1').addEvent('click', function(e){
//			document.id('image').setStyle('display', 'none');
//			document.id('url').setStyle('display', 'block');
//			document.id('custom').setStyle('display', 'block');
//		});
//		if (document.id('jform_type0').checked==true)
//		{
//			document.id('jform_type0').fireEvent('click');
//		}
//		else
//		{
//			document.id('jform_type1').fireEvent('click');
//		}
//	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_simplecalendar&task=organizer.edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="organizer-form" class="form-validate form-horizontal">
<div class="span10 form-horizontal">

	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_ORGANIZER_TAB_DETAILS');?></a></li>
			<li><a href="#other" data-toggle="tab"><?php echo JText::_('COM_SIMPLECALENDAR_ORGANIZER_TAB_ADDITIONAL_INFORMATION');?></a></li>
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
						<?php echo $this->form->getLabel('abbr'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('abbr'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('contact_name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('contact_name'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('contact_email'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('contact_email'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('contact_website'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('contact_website'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('contact_telephone'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('contact_telephone'); ?>
					</div>
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
			<div class="tab-pane" id="other">
				<?php foreach ($this->form->getFieldset('other') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
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
			</div>
			<div class="tab-pane" id="extended">
			
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
						<?php echo $this->form->getValue('name'); ?>
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
		</fieldset>
	</div>
	<!-- End Sidebar -->
</form>
