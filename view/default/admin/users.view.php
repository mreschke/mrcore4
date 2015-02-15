<div id='uc'>

    <?=eval(Page::load_part('menu/admin'))?>

    <div id='ub'>
        
        <div id='subnavc'>
            <span class='subnava'><a href='<?=Page::get_url('profile/new') ?>'>New User</a></span>
            <span class='subnava'><a href='<?=Page::get_url('admin/users') ?>'>Full View</a></span>
            <span class='subnava'><a href='<?=Page::get_url('admin/users/compact') ?>'>Compact View</a></span>
        </div>

    <?php if(!$user->view_compact): ?>
        <?php $i=0 ?>
        <?php foreach($user->tbl_users as $user->tbl_user): ?>
            <?php $alt=''; if($i%2) $alt='_alt'; $i++; ?>
            <div class='users_row<?php echo $alt ?>'>
                <table id='users_table' border='0'>
                <tbody align='left' valign='top'>
                    <tr height='5'>
                        <td rowspan='4' width='150' align='center'>
                            <a href='<?php echo Page::get_url('profile').'/'.$user->tbl_user->alias ?>'>
                                <img src='<?php echo Page::get_url($user->tbl_user->avatar, true) ?>' border='0' alt='avatar' class='avatar_medium' />
                            </a>
                            <br />
                            <a href='<?php echo Page::get_url('profile').'/'.$user->tbl_user->alias ?>'><?php echo $user->tbl_user->alias ?></a>
                        </td>
                        <td width='275'><?php echo $user->tbl_user->first_name.' '.$user->tbl_user->last_name ?></td>
                        <th>Creator:</th>
                        <td width='140'><?php echo Tbl_user::get_user($user->tbl_user->created_by)->last_name ?></td>
                        <th>Topics:</th>
                        <td width='30'><?php echo $user->tbl_user->topic_count ?></td>
                        <th>Admin:</th>
                        <td><?php echo ($user->tbl_user->perm_admin ? 'Yes' : 'No') ?></td>
                        <th>Exec:</th>
                        <td><?php echo ($user->tbl_user->perm_exec ? 'Yes' : 'No') ?></td>
                    </tr><tr height='5'>
                        <td><?php echo $user->tbl_user->title ?></td>
                        <th>Created:</th>
                        <td><?php echo $user->tbl_user->created_on ?></td>
                        <th>Comments:</th>
                        <td><?php echo $user->tbl_user->comment_count ?></td>
                        <th>Create:</th>
                        <td><?php echo ($user->tbl_user->perm_create ? 'Yes' : 'No') ?></td>
                        <th>HTML:</th>
                        <td><?php echo ($user->tbl_user->perm_html ? 'Yes' : 'No') ?></td>
                    </tr><tr height='5'>
                        <td><a href='mailto:<?php echo $user->tbl_user->email ?>'><?php echo $user->tbl_user->email ?></a></td>
                        <th>Updated:</th>
                        <td><?php echo $user->tbl_user->updated_on ?></td>
                        <th>Disabled:</th>
                        <td><?php echo ($user->tbl_user->disabled ? 'Yes' : 'No') ?></td>
                        <th>Groups:</th>
                        <td rowspan='2'>
                            <?php $groups = Tbl_perm::get_perm_groups_array($user->tbl_user->user_id) ?>
                            <?php foreach ($groups as $group_name => $group_id): ?>
                                <?php echo $group_name ?><br />
                            <?php endforeach ?>
                        </td>                    
                    </tr><tr>
                        <td><?php echo $user->tbl_user->description ?></td>
                        <th>Login:</th>
                        <td><?php echo $user->tbl_user->last_login_on ?></td>
                        <th>ID:</th>
                        <td><?php echo $user->tbl_user->user_id ?></td>

                    </tr>
                        
                </tbody>
                </table>
            </div>
        <?php endforeach ?>
    <?php endif ?>
    
    
    <?php if($user->view_compact): ?>
        <table id='users_table_compact'>
            <tbody align='left' valign='top'>
            <tr>
                <th>ID</th>
                <th>Alias</th>
                <th>Avatar</th>
                <th>Email</th>
                <th>Name</th>
                <th>Title</th>
                <th>Description</th>
                <th>Creator</th>
                <th>Created</th>
                <th>Updated</th>
                <th>Login</th>
                <th>Disabled</th>
                <th>Admin</th>
                <th>Create</th>
                <th>Exec</th>
                <th>HTML</th>
            </tr>
            <?php $i=0 ?>
            <?php foreach($user->tbl_users as $user->tbl_user): ?>
                <?php $alt=''; if($i%2) $alt='_alt'; $i++; ?>
                <tr class='users_compact_row<?php echo $alt ?>'>
                    <td><?php echo $user->tbl_user->user_id ?></td>
                    <td><a href='<?php echo Page::get_url('profile').'/'.$user->tbl_user->user_id ?>'><?php echo $user->tbl_user->alias ?></a></td>
                    <td><a href='<?php echo Page::get_url('profile').'/'.$user->tbl_user->user_id ?>'><img src='<?php echo Page::get_url($user->tbl_user->avatar, true) ?>' border='0' alt='avatar' class='avatar_small' /></a></td>
                    <td><?php echo $user->tbl_user->email ?></td>
                    <td align='center'><?php echo ereg_replace(" ", "<br />", $user->tbl_user->first_name.' '.$user->tbl_user->last_name) ?></td>
                    <td><?php echo $user->tbl_user->title ?></td>
                    <td><?php echo $user->tbl_user->description ?></td>
                    <td><?php echo $user->tbl_user->created_by ?></td>
                    <td align='center'><?php echo ereg_replace(" ", "<br />", $user->tbl_user->created_on) ?></td>
                    <td align='center'><?php echo ereg_replace(" ", "<br />", $user->tbl_user->updated_on) ?></td>
                    <td align='center'><?php echo ereg_replace(" ", "<br />", $user->tbl_user->last_login_on) ?></td>
                    <td align='center'><?php echo $user->tbl_user->disabled ?></td>
                    <td align='center'><?php echo $user->tbl_user->perm_admin ?></td>
                    <td align='center'><?php echo $user->tbl_user->perm_create ?></td>
                    <td align='center'><?php echo $user->tbl_user->perm_exec ?></td>
                    <td align='center'><?php echo $user->tbl_user->perm_html ?></td>
                </tr>
            <?php endforeach ?>        
            </tbody>
        </table>
    <?php endif ?>

    </div>
</div>