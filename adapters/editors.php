<?php

use Joomla\CMS\Form\Field\EditorField;

defined('_JEXEC') or die;

class vgEditors extends EditorField
{
	public function embedContentToEditor($name, $content)
	{
		$editor = $this->getEditor();
		return $editor->display(
			$name,
			htmlspecialchars($content, ENT_COMPAT, 'UTF-8'),
			'100%', '400', '60', '20', false, null, null, null, ['readonly' => false]
		);
	}
}

?>