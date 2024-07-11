<?php
/**
 * @package    K&K Video Manager
 * @copyright  Copyright (C) 2013 K&K media production, Künnemann & Kießling GbR. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 * @version    $Id: categoryfilter.php 63 2016-11-13 11:30:38Z Maik KÃ¼nnemann $
 */

defined('_JEXEC') or die;

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldCategoryFilter extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'CategoryFilter';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();
		$published = $this->element['published'] ? $this->element['published'] : array(0,1);

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level, a.published');
		$query->from('#__kkvideo_categories AS a');
		$query->join('LEFT', $db->quoteName('#__kkvideo_categories').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Filter root
		$query->where('a.level > 0');

		// Filter on the published state
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			JArrayHelper::toInteger($published);
			$query->where('a.published IN (' . implode(',', $published) . ')');
		}

		$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.published');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			if ($options[$i]->published == 1)
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level - 1). $options[$i]->text ;
			}
			else
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level - 1). '[' .$options[$i]->text . ']';
			}
		}

		return $options;
	}
}
