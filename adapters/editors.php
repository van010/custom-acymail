<?php

use Joomla\CMS\Form\Field\EditorField;

defined('_JEXEC') or die;

class vgEditors extends EditorField
{

    public $editorId = 'acym_mail_preview_editor';

    /**
     * @param string $name
     * @param string $content
     * @return mixed
     */
	public function embedContentToEditor($name, $content)
	{
		$editor = $this->getEditor();
		return $editor->display(
			$name,
			htmlspecialchars($content, ENT_COMPAT, 'UTF-8'),
			'100%', '400', '60', '20', false, $this->editorId, null, null, ['readonly' => false]
		);
	}
}

?>