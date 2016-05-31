<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2016 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

DPCalendarHelper::loadLibrary(array('jquery' => true, 'dpcalendar' => 'true'));

if ($item == null)
{
	return;
}
ob_start();
?>
<div class="countdown-row">
	{y<}<span class="countdown-section"><span class="countdown-amount">{yn}</span><br />{yl}</span>{y>}
	{o<}<span class="countdown-section"><span class="countdown-amount">{on}</span><br />{ol}</span>{o>}
	{w<}<span class="countdown-section"><span class="countdown-amount">{wn}</span><br />{wl}</span>{w>}
	{d<}<span class="countdown-section"><span class="countdown-amount">{dn}</span><br />{dl}</span>{d>}
	{h<}<span class="countdown-section"><span class="countdown-amount">{hn}</span><br />{hl}</span>{h>}
	{m<}<span class="countdown-section"><span class="countdown-amount">{mn}</span><br />{ml}</span>{m>}
	{s<}<span class="countdown-section"><span class="countdown-amount">{sn}</span><br />{sl}</span>{s>}
	<div style="clear: both">
		<p>
			<a href="<?php echo DPCalendarHelperRoute::getEventRoute($item->id, $item->catid, true)?>" class="dpc-counter-event-link">
				<?php echo htmlspecialchars($item->title)?>
			</a>
			<br /><?php echo JHTML::_('content.prepare', JHtml::_('string.truncate', $item->description, $params->get('description_length')));?>
		</p>
	</div>
</div>
<?php
$layout = preg_replace('#\r|\n#', '', ob_get_contents());
ob_end_clean();
$d = DPCalendarHelper::getDate($item->start_date, $item->all_day);
$targetDate = $d->format('Y', true) . "," . ($d->format('m', true) - 1) . "," . $d->format('d', true) . "," . $d->format('H', true) . "," .
		 $d->format('i', true) . ",0";

$tmp = clone JComponentHelper::getParams('com_dpcalendar');
$tmp->set('event_date_format', $params->get('date_format', 'm.d.Y'));
$tmp->set('event_time_format', $params->get('time_format', 'g:i a'));
$tmp->set('description_length', $params->get('description_length', $tmp->get('description_length')));


$output = $params->get('output_now',
		'{{#events}}<p>Event happening now:<br/>{{date}}<br/><a href="{{{backLink}}}">{{title}}</a>{{#maplink}}<br/>Join us at [<a href="{{{maplink}}}" target="_blank">map</a>]{{/maplink}}</p>{{/events}}{{^events}}{{emptyText}}{{/events}}');
$expiryText = preg_replace('#\r|\n#', "", DPCalendarHelper::renderEvents(array($item), $output, $tmp));

$document = JFactory::getDocument();
$document->addScript(JURI::base() . 'components/com_dpcalendar/libraries/jquery/ext/jquery.countdown.min.js');
$document->addStyleSheet(JURI::base() . 'components/com_dpcalendar/libraries/jquery/ext/jquery.countdown.css');

$targetId = "dpcountdown-" . $module->id;

$labels = array(
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_YEARS'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_MONTHS'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_WEEKS'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_DAYS'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_HOURS'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_MINUTES'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_SECONDS')
);
$labels1 = array(
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_YEAR'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_MONTH'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_WEEK'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_DAY'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_HOUR'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_MINUTE'),
		JText::_('MOD_DPCALENDAR_COUNTER_LABEL_SECOND')
);

$code = "// <![CDATA[ \n";
$code .= "dpjQuery(document).ready(function() {\n";
$code .= "	dpjQuery('#" . $targetId . "').countdown({until: new Date(" . $targetDate . "), \n";
$code .= "		description: '" . str_replace('\'', '\\\'', $item->title) . "', \n";
$code .= "		labels: ['" . implode("','", $labels) . "'], \n";
$code .= "		labels1: ['" . implode("','", $labels1) . "'], \n";
$code .= "		alwaysExpire: true, expiryText: '".str_replace('\'', '\\\'',$expiryText)."', \n";
$code .= "		layout: '" . str_replace('\'', '\\\'', $layout) . "'\n";
$code .= "	});\n";
$code .= "});\n";
$code .= "// ]]>\n";
$document->addScriptDeclaration($code);
?>
<div class="dpcalendar_counter">
	<div id="<?php echo $targetId;?>" class="countdown">
		<?php echo JText::_("MOD_DPCALENDAR_COUNTER_JSERR");?>
	</div>
</div>

<?php
if ($params->get('show_as_popup', '0') == '1' || $params->get('show_as_popup', '0') == '3')
{
	$calCode = "dpjQuery(document).ready(function() {\n";
	$calCode .= "dpjQuery('body').on('click','.dpc-counter-event-link', function (event) {\n";
	$calCode .= "	event.stopPropagation();\n";

	if ($params->get('show_as_popup', '0') == '1')
	{
		$calCode .= "	var link = dpjQuery(this).attr('href');\n";
		$calCode .= "	dpjQuery('#" . $targetId . "-modal').on('show', function () {\n";
		$calCode .= "		var url = new Url(link);\n";
		$calCode .= "		url.query.tmpl = 'component';\n";
		$calCode .= "		dpjQuery('#" . $targetId . "-modal iframe').attr('src', url.toString());\n";
		$calCode .= "	});\n";
		$calCode .= "	dpjQuery('#" . $targetId . "-modal iframe').removeAttr('src');\n";
		$calCode .= "	dpjQuery('#" . $targetId . "-modal').modal();\n";
	}
	else if ($params->get('show_as_popup', '0') == '3')
	{
		JHtml::_('behavior.modal', '.dpc-counter-event-link-invalid');

		$calCode .= "	var modal = dpjQuery('#" . $targetId . "-modal');\n";
		$calCode .= "	var width = dpjQuery(window).width();\n";
		$calCode .= "	var url = new Url(dpjQuery(this).attr('href'));\n";
		$calCode .= "	url.query.tmpl = 'component';\n";
		$calCode .= "	SqueezeBox.open(url.toString(), {\n";
		$calCode .= "		handler : 'iframe',\n";
		$calCode .= "		size : {\n";
		$calCode .= "			x : (width < 650 ? width - (width * 0.10) : modal.width() < 650 ? 650 : modal.width()),\n";
		$calCode .= "			y : modal.height()\n";
		$calCode .= "		}\n";
		$calCode .= "	});\n";
	}
	$calCode .= "	return false;\n";
	$calCode .= "});\n";
	$calCode .= "});\n";
	$document->addScriptDeclaration($calCode);
	?>
<div id="<?php echo $targetId;?>-modal" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"
	style="height:500px">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  	<iframe style="width:99.6%;height:95%;border:none;"></iframe>
</div>
<?php
}
?>
