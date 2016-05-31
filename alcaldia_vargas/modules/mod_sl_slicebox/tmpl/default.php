<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

JHtml::_('script', 'mod_sl_slicebox/modernizr.custom.46884.js', array(), true);
JHtml::_('script', 'mod_sl_slicebox/jquery.slicebox.js', array(), true);
JHtml::_('stylesheet', 'mod_sl_slicebox/slicebox.css', array(), true);

// Initalize variables
$orientation	= $params->get('orientation', 'v');
$cuboidsCount	= $params->get('cuboidsCount', 5);
$cuboidsRandom	= $params->get('cuboidsRandom', 0) ? 'true' : 'false';
$speed			= $params->get('speed', 600);
$interval		= $params->get('interval', 5000);
$textColor		= $params->get('textColor', '#fff');
$customCss		= $params->get('customCss');

$live_site		= JUri::base(true);
$document		= JFactory::getDocument();

$document->addStyleDeclaration('.sb-description {color: ' . $textColor . ';}');

if ($customCss) {
	$document->addStyleDeclaration($customCss);
}

?>
<script type="text/javascript">
jQuery(function($) {
	slidebox = $('#sb-slider').slicebox({
		orientation : '<?php echo $orientation; ?>',
		cuboidsCount: <?php echo $cuboidsCount; ?>,
		maxCuboidsCount: <?php echo $cuboidsCount; ?>,
		cuboidsRandom : <?php echo $cuboidsRandom; ?>,
		speed: <?php echo $speed; ?>,
		interval: <?php echo $interval; ?>
	});

	slidebox.play();
});
</script>

<ul id="sb-slider" class="sb-slider">
	<?php foreach ($items as $item) : ?>

		<li>
			<?php if ($item->link) : ?>
			<a href="<?php echo JRoute::_($item->link); ?>"<?php echo $item->link_nw ? ' target="_blank"' : ''; ?>>
				<img src="<?php echo $live_site . '/' . $item->image; ?>" />
			</a>
			<?php else : ?>
			<img src="<?php echo $live_site . '/' . $item->image; ?>" />
			<?php endif; ?>

			<?php if ($item->title || $item->text) : ?>

			<div class="sb-description">
				<?php if ($item->title) : ?>
				<h3><?php echo $item->title; ?></h3>
				<?php endif; ?>
				<?php if ($item->text) : ?>
				<p><?php echo $item->text; ?></p>
				<?php endif; ?>
			</div>

			<?php endif; ?>
		</li>

	<?php endforeach; ?>
</ul>
