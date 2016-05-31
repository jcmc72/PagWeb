<?php
/**
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
// get helper
require_once (dirname(__FILE__).DS.'helper.php');

//require_once(JPATH_SITE.DS.'components'.DS.'com_simplecalendar'.DS.'helpers'.DS.'route.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_simplecalendar'. DS . 'helpers' . DS . 'output.class.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_simplecalendar'. DS . 'helpers' . DS . 'route.php');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list = &modSimpleCalendarHelper::getList($params);

require(JModuleHelper::getLayoutPath('mod_simplecalendar'));