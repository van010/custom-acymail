<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */
 

defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('JPATH_TT_ADMINISTRATOR') or define('JPATH_TT_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_tradingtechnologies');

class com_tradingtechnologiesInstallerScript {

	function preflight( $type, $parent ) {

		$jversion = new JVersion();

		$this->release = $parent->getManifest()->version;

		$this->currentVersion = $this->getParam('version');

		if ( $type == 'update' ) {

			$this->oldRelease = $this->getParam('version');

			if (version_compare($this->currentVersion, '1.0.0', '<')) {

				//Repair table #__schema which was not used before
				//Just create a dataset with extension id and old version (before update).
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('extension_id'))
					->from('#__extensions')
					->where($db->quoteName('type') . ' = ' . $db->quote('component') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote('com_tradingtechnologies'));
				$db->setQuery($query);
				if ($eid = $db->loadResult()) {
					$query->clear();
					$query->insert($db->quoteName('#__schemas'));
					$query->columns(array($db->quoteName('extension_id'), $db->quoteName('version_id')));
					$query->values($eid . ', ' . $db->quote($this->currentVersion));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}

	}

	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {

		echo '<p>TradingTechnologies for Joomla</p>';

	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {

		echo '<p>Update from ' . $this->currentVersion . ' to ' . $this->release . ' was successful</p>';

		if(!class_exists('ttTableUpdater')) require_once(JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'tableupdater.php');
		$tableupdater = new ttTableUpdater();
		$tableupdater->execSQLFile(JPATH_TT_ADMINISTRATOR.DS.'sql'.DS.'install.sql');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {

	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {

		echo '<div class="header">The Component tradingtechnologies was sucessfully removed</div>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_tradingtechnologies"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
}
