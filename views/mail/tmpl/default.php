<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

$db = JFactory::getDbo();
$db->setQuery("select * from #__acym_mail where type = 'template'");
$templates = $db->loadObjectList();


$db->setQuery("select template_id from #__tt_mail_template");
$active_template = $db->loadResult();

$db->setQuery("select template2_id from #__tt_mail_template");
$active_template2 = $db->loadResult();

?>


<div class="alert alert-primary" role="alert">
    You can creat your own email template from ACY Mailing > templates option
</div>

<?php if (count($templates) === 0) : ?>
    <div class="alert alert-danger" role="alert">
        No templates found
    </div>
<?php else : ?>

    <form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies&task=mailer.save_template'); ?>" method="post">
        <div class="form-group">
            <label for="template">Select email template for opening mail</label>
            <select name="template" class="form-control" id="template">
                <?php
                foreach ($templates as $template) {
                    $selected = ($template->id == $active_template) ? 'selected': '';
                    echo "<option value='$template->id' $selected>$template->name ($template->id)</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="template">Select email template for closing mail</label>
            <select name="template2" class="form-control" id="template">
                <?php
                foreach ($templates as $template) {
                    $selected = ($template->id == $active_template2) ? 'selected': '';
                    echo "<option value='$template->id' $selected>$template->name ($template->id)</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Save</button>
    </form>

    
<?php endif ?>