<div id='gc'>
    <?=eval(Page::load_part('menu/admin'))?>

    <div id='gb'>

        <div id='subnavc'>
            <span class='subnava'><a href='<?=Page::get_url('admin/groups/new') ?>'>New Group</a></span>
            <span class='subnava'><a href='<?=Page::get_url('admin/groups/detail') ?>'>Full View</a></span>
            <span class='subnava'><a href='<?=Page::get_url('admin/groups') ?>'>Compact View</a></span>
        </div>

        <table id='groups_table' border='0'>
            <tr>
                <th>&nbsp;</th>
                <th>ID</th>
                <th>Group</th>
                <th>Description</th>
            </tr>
            <?php if($event_new): ?>
                <tr>
                    <td>
                        <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/groups') ?>'" value='Cancel' />
                        <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                    </td>
                    <td><?php echo $perm->tbl_perm_group->perm_group_id ?></td>
                    <td><input type='text' class='groups_group_txt' name='txt_group' value='' /></td>
                    <td><input type='text' class='groups_group_description_txt' name='txt_group_description' value='' /></td>
                </tr>
            <?php endif ?>
            <?php $i=0 ?>
            <?php foreach ($perm->tbl_perm_groups as $perm->tbl_perm_group): ?>
                <?php $alt=''; if($i%2) $alt='_alt'; $i++; ?>
                <tr class='groups_row<?php echo $alt ?>'>
                    <?php if($event_edit && $perm->tbl_perm_group->perm_group_id == $group_id): ?>
                        <td>
                            <input type='button' name='btn_delete' onclick='button_click(this)' value='Delete' />
                            <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/groups') ?>'" value='Cancel' />
                            <input type='button' name='btn_save' onclick='button_click(this)' value='Save' />
                        </td>
                        <td><?php echo $perm->tbl_perm_group->perm_group_id ?></td>
                        <td><input type='text' class='groups_group_txt' name='txt_group' value='<?php echo $perm->tbl_perm_group->group ?>' /></td>
                        <td><input type='text' class='groups_group_description_txt' name='txt_group_description' value='<?php echo $perm->tbl_perm_group->group_description ?>' /></td>
                    <?php else: ?>
                        <td><a href='<?php echo Page::get_url('admin/groups').'/edit/'.$perm->tbl_perm_group->perm_group_id ?>'>Edit</a></td>
                        <td><?php echo $perm->tbl_perm_group->perm_group_id ?></td>
                        <td class='groups_name'><?php echo $perm->tbl_perm_group->group ?></td>
                        <td class='groups_description'><?php echo $perm->tbl_perm_group->group_description ?></td>
                    <?php endif ?>
                </tr>
                <?php if($view_detail): ?>
                    <?php $perm->tbl_perm_group_users = Tbl_user::get_users_in_group($perm->tbl_perm_group->perm_group_id) ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>
                            <?php foreach($perm->tbl_perm_group_users as $user_id => $alias): ?>
                                <a href='<?php echo Page::get_url('profile').'/'.$alias ?>'><?php echo $alias ?></a><br />
                            <?php endforeach ?>
                        </td>
                    </tr>
                <?php endif ?>
                
            <?php endforeach ?>        
        </table>
    </div>
</div>