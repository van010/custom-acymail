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
            <th width="10%"> Trading Accounts </th>
            <th width="10%"> Send mail </th>
        </tr>
    </thead>

    <form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies&task=traders.update_accounts_mail_list'); ?>" method="post">
        <tbody>
            <?php

            $db = JFactory::getDbo();
            $db->setQuery("select * from #__tt_accounts order by name");
            $accounts = $db->loadObjectList();


            foreach ($accounts as $account) :
                $account_id = $account->id;
                $account_name = $account->name;
                $send_mail = ($account->send_mail == 1) ? 'checked' : '';
                $send_mail_value = ($account->send_mail == 1) ? 1 : 0;
            ?>
                <tr>
                    <td><?php echo ucfirst($account_name) . ' - ' . $account_id ?></td>
                    <td>
                        <input type="checkbox" <?php echo $send_mail ?> onchange="updateHiddenInput(this)">
                        <input type="hidden" name="<?php echo $account->id ?>" value="<?php echo $send_mail_value ?>">
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