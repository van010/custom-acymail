<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

class ttJsApi {

	private static $_jsAdd = array();

	public static function addJScript($name, $script = false, $tohead = false){

		self::$_jsAdd[$name]['script'] = trim($script);
		if(!isset(self::$_jsAdd[$name]['written']))self::$_jsAdd[$name]['written'] = false;
		self::$_jsAdd[$name]['tohead'] = $tohead ? true : false;
	}

	public static function getJScripts(){
		return self::$_jsAdd;
	}

	public static function removeJScript($name){
		unset(self::$_jsAdd[$name]);
	}

	public static function writeJS(){

		$html = '';
		foreach(self::$_jsAdd as $name => &$jsToAdd){

			if($jsToAdd['written']) continue;
			if(!$jsToAdd['script'] or strpos($jsToAdd['script'],'/')===0 and strpos($jsToAdd['script'],'//<![CDATA[')!==0){ //strpos($script,'/')===0){

				if(!$jsToAdd['script']){
					$file = $name;
				} else {
					$file = $jsToAdd['script'];
				}

				if(strpos($file,'/')!==0){
					$file = ttJsApi::setPath($file);
				} else if(strpos($file,'//')!==0){
					$file = JURI::root(true).$file;
				}

				if(empty($file)){
					ttdebug('writeJS javascript with empty file',$name,$jsToAdd);
					continue;
				}

				$document = JFactory::getDocument();
				$document->addScript($file,"text/javascript");

			} else {

				$script = trim($jsToAdd['script']);
				if(!empty($script)) {
					$script = trim($script,chr(13));
					$script = trim($script,chr(10));
					if($jsToAdd['tohead']) {
						$document = JFactory::getDocument();
						$document->addScriptDeclaration($script.chr(10));
					} else if(strpos($script,'//<![CDATA[')===false){
						$html .= '<script id="'.$name.'_js" type="text/javascript">//<![CDATA[ '.chr(10).$script.' //]]>'.chr(10).'</script>';
					} else {
						$html .= '<script id="'.$name.'_js" type="text/javascript"> '.$script.' </script>';
					}
				}

			}
			$html .= chr(13);
			$jsToAdd['written'] = true;
		}
		return $html;
	}

	public static function css($namespace, $path = FALSE, $ext = 'css') {

		static $loaded = array();

		// Only load once
		if(!empty($loaded[$namespace])) {
			return;
		}

		if(strpos($namespace,'/')!==0){
			$file = ttJsApi::setPath($namespace, $path, $ext);
		} else if(strpos($namespace,'//')!==0){
			$file = JURI::root(true).$namespace;
		}

		$document = JFactory::getDocument();
		$document->addStyleSheet($file);
		$loaded[$namespace] = TRUE;

	}

	// Set file path(look in template if relative path)
	public static function setPath($namespace ,$path = FALSE ,$ext = 'js', $absolute_path=false) {

		//$file = $namespace.'.'.$ext ;
		$file = $namespace;
		$template = JFactory::getApplication()->getTemplate();

		if($path === FALSE) {
			$uri = JPATH_THEMES .'/'. $template.'/'.$ext ;
			$path = 'templates/'. $template .'/'.$ext ;
		}

		if(strpos($path, 'templates/'. $template ) !== FALSE){
			// Search in template or fallback
			if(!file_exists($uri.'/'. $file)) {
				$assets_path = 'components/com_tradingtechnologies/assets/';
				$path = str_replace('templates/'. $template.'/',$assets_path, $path);
			}
			if ($absolute_path) {
				$path = JPATH_ROOT .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}

		} else if(strpos($path, '//') === FALSE) {
			if ($absolute_path) {
				$path = JPATH_ROOT .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}
		}
		return $path.'/'.$file ;
	}

	// Adds jQuery if needed
	static function jQuery() {

		if(JVERSION<3){
			//Very important convention with other 3rd pary developers, must be kept. DOES NOT WORK IN J3
			if(JFactory::getApplication()->get('jquery')) {
				return FALSE;
			}
		} else {
			JHtml::_('jquery.framework');
		}

		if(!ttConfig::get('load_jquery', true) && JFactory::getApplication()->isSite()) {
			ttdebug('Common jQuery is disabled');
			return FALSE;
		}

		if(JVERSION<3){
			if(ttConfig::get('google_jquery',false)){
				self::addJScript('jquery.min','//ajax.googleapis.com/ajax/libs/jquery/1.11.4/jquery.min.js');
				self::addJScript('jquery-migrate-1.2.1.min.js');
			} else {
				self::addJScript('jquery-1.11.3.min.js');
				self::addJScript('jquery-migrate-1.2.1.min.js');
			}
		}

		self::addJScript('jquery.noconflict.js');
		//Very important convention with other 3rd pary developers, must be kept DOES NOT WORK IN J3
		if(JVERSION<3){
			JFactory::getApplication()->set('jquery',TRUE);
		}

		return TRUE;
	}

	static function uikit() {

		self::jQuery();
		self::addJScript('uikit.min.js','/administrator/components/com_tradingtechnologies/libraries/uikit/js/uikit.min.js');
		self::css('/administrator/components/com_tradingtechnologies/libraries/uikit/css/uikit.min.css',true,'css');

		return TRUE;
	}


}