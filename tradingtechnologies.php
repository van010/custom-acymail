<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;

if (!class_exists( 'ttHelper' )) require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/ttHelper.php';

$controller = JControllerLegacy::getInstance('tradingtechnologies');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();