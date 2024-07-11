<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Banners\Administrator\View\Banner\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('form.validate');


$doc = JFactory::getDocument();
ttJsApi::jQuery();
$js = '
	jQuery(document).ready(function() {

		jQuery("[data-uk-switcher]").on("show.uk.switcher", function(event, area){
			var navId = jQuery(this).attr("id");
			Cookie.write(navId, jQuery("#"+navId+" .uk-active").index());
		});

	});
	';
$doc->addScriptDeclaration($js);

$active_tab = isset($_COOKIE["tt-config-nav"]) ? $_COOKIE["tt-config-nav"] : 0;
?>
<form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies');?>" id="adminForm" method="post" name="adminForm" class="form-validate uk-form uk-form-horizontal">
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php endif; ?>
		<div class="adminform">
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>    
		<div id="config-document">
			<?php 
			echo $this->loadTemplate('site');
			?>
		</div>
		<div class="clr"></div>
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	</div>
	<?php endif; ?>
</form>
<?php
echo ttJsApi::writeJS();
?>