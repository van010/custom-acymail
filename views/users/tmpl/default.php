<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;
?>



<table class="adminlist uk-table" id="inventorylist">
    <thead>
        <tr>
            <th width="10%"> Users </th>
            <th width="10%"> Send mail </th>
        </tr>
    </thead>

    <form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies&task=users.update_users_mail_list'); ?>" method="post">
        <tbody>
            <?php

            $db = JFactory::getDbo();
            $db->setQuery("select * from #__users LEFT JOIN #__tt_mail_list on #__tt_mail_list.user_id = #__users.id");
            //$db->setQuery("select * from #__users join #__tt_subscribers using(id)");
            $users = $db->loadObjectList();

            foreach ($users as $user) :
                $user_id = $user->id;
                $user_name = $user->name;
                $send_mail = ($user->send_mail == 1) ? 'checked' : '';
                $send_mail_value = ($user->send_mail == 1) ? 1 : 0;
            ?>
                <tr>
                    <td><?php echo ucfirst($user_name) ?></td>
                    <td>
                        <input type="checkbox" <?php echo $send_mail ?> onchange="updateHiddenInput(this)">
                        <input type="hidden" name="<?php echo $user->id ?>" value="<?php echo $send_mail_value ?>">
                    </td>
                    <td>
                        <!--<button type="submit"
                                data-action="send-main"
                                data-user-id="<?php /*echo $user_id*/?>"
                                data-user-sendmail="<?php /*echo $user->send_mail*/?>"
                                role="button" >Send mail
                        </button>-->
                        <!--<input type="submit"
                               name="<?php /*echo $user->id */?>"
                               value="send-mail-hihi123-<?php /*echo $user_id*/?>">-->

                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</table>

<script>
    function updateHiddenInput(checkbox) {

        if (checkbox.checked) {
            checkbox.nextElementSibling.value = '1';
        } else {
            checkbox.nextElementSibling.value = '0';
        }


    }
</script>