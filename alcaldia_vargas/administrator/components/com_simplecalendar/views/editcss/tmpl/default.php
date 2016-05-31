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

?>
<form action="index.php?option=com_simplecalendar&task=editcss.savecss" method="post" name="adminForm" id="adminForm">
		<table class="adminform">
		<tr>
			<th>
				<?php echo $this->selectbox; ?>
				<?php echo $this->css_path; ?>
			</th>
		</tr>
		<tr>
			<td>
				<textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea>
			</td>
		</tr>
		</table>

		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="filename" id="filename" value="<?php echo $this->filename; ?>" />
		<input type="hidden" name="option" value="com_simplecalendar" />
		<input type="hidden" name="task" value="" />
		<?php
echo JHTML::_( 'form.token' );

?>
</form>