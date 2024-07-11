<?php
/**
 * @package    K&K Video Manager
 * @copyright  Copyright (C) 2013 K&K media production, Künnemann & Kießling GbR. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 * @version    $Id: loadlanguage.php 63 2016-11-13 11:30:38Z Maik KÃ¼nnemann $
 */

defined('_JEXEC') or die;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

if(!class_exists('KKvideoJsApi')) require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kkvideo'.DS.'helpers'.DS.'jsapi.php');

/**
 * Form Field class for the Joomla Platform.
 * Provides spacer markup to be used in form layouts.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldLoadlanguage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Loadlanguage';

	/**
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$language = JFactory::getLanguage();
		JFactory::getLanguage()->load('com_kkvideo', JPATH_ADMINISTRATOR, $language->getTag(), true);
		return KKvideoJsApi::writeJS();
	}

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   11.1
	 */
	protected function getTitle()
	{
		return '';
	}
}
