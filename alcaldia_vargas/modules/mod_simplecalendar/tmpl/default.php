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
 ?>
<div class="scevents<?php echo $moduleclass_sfx ?>">
	<?php if ( sizeof($list) > 0 ): ?>
	<ul>
	<?php foreach ($list as $item) :  ?>
		<li><?php echo $item; ?></li>
	<?php endforeach; ?>
	</ul>
	<?php else: ?>
	<span><?php echo JText::_('MOD_SIMPLECALENDAR_ERROR_NO_EVENTS')?></span>
	<?php endif; ?>
</div>