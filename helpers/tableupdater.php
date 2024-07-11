<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.helper' );

class ttTableUpdater {

	function execSQLFile($sqlfile) {

		if ( !file_exists($sqlfile) ) {
			JError::raiseWarning( 100, 'No SQL file provided!');
			return false;
		}

		jimport('joomla.installer.helper');
		$db = JFactory::getDBO();
		$queries = $db->splitSql(file_get_contents($sqlfile));

		if (count($queries) == 0) {
			JError::raiseWarning( 100, 'SQL file has no queries!');
			return false;
		}
		$ok = true;

		foreach ($queries as $query) {
			if(empty($query)){
				JError::raiseWarning( 100, 'execSQLFile Query was empty in file '.$sqlfile);
				continue;
			}
			$query = trim($query);
			$queryLines = explode("\n",$query);
			foreach($queryLines as $n=>$line){
				if(empty($line)){
					unset($queryLines[$n]);
				}
			}
			$query = implode("\n",$queryLines);

			if(!empty($query)){
				$db->setQuery($query);
				if (!$db->execute()) {
					JError::raiseWarning(1, 'JInstaller::install: '.$sqlfile.' '.JText::_('COM_TT_SQL_ERROR')." ".$db->stderr(true));
					$ok = false;
				}
			}
		}

		return $ok;
	}


}

