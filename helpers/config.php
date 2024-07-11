<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

class ttConfig {

	private $_params = false;

	public function __construct() {

	}

	public static function loadconfig() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__tt_config');
		$query->where('id = 1');
		$db->setQuery($query);
		$config = $db->loadResult();
		return $config;
	}

	public static function get($config, $default='', $group='', $merge='') {
		$configs = self::loadconfig();
		$registry = new JRegistry();
		$registry->loadString($configs);
		if(!empty($merge) or $merge === '0') {
			return $merge;
		} else if(!empty($group) && isset($registry->get($group, false)->$config)) {
			$return = $registry->get($group, false)->$config;
			if(empty($return) and $return !== '0' and $merge !== '0') $return = $default;
			return $return;
		} else {
			return $registry->get($config, $default);
		}
	}

	public static function all($registry = true) {
		$configs = self::loadconfig();
		if($registry) {
			$registry = new JRegistry();
			$registry->loadString($configs);
			return $registry;
		} else {
			return json_decode($configs);
		}
	}

	public static function merge($params = array(), $group = '') {
		if(!empty($params)) {
			foreach($params as $k => $param) {
				if($param == '') {
					$params->$k = ttConfig::get($k, '', $group);
				} else if(is_object($param)) {
					$params->$k = self::merge($param, $k);
				}
			}
		}
		return $params;
	}

	public static function showDebug(){

		$config = new ttConfig();
		$debug = $config->get('debug','0');

		return $debug;

	}

}
?>