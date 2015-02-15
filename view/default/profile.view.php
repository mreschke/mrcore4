<? if ($info->is_authenticated): ?>
<div id='pc' ondblclick="window.location='<?php echo Page::get_url('profile').'/'.$user->user_id.'/edit' ?>';">
<? else: ?>
<div id='pc' >
<? endif ?>
    <?=eval(Page::load_part('menu/admin'))?>
    <div id='pb'>
        <div id='subnavc'>
            <span class='subnava'><a href='<?=Page::get_url('profile/'.$user->user_id.'/edit') ?>'>Edit User</a></span>
        </div>

        <div class='section01'>
            <table class='profile_table' border='0'>
                <tbody valign='middle' align='left'>
                <tr>
                    <td align='center' valign='top' rowspan='7' class='profile_table_avatar_td'>
                        <img src='<?php echo Page::get_url($user->tbl_user->avatar, true) ?>' border='0' class='avatar_full' />
                        <br />
                        <?php if($user->edit): ?>
                            <a href='#' onclick="document.getElementById('profile_upload_avatar_div').style.display='block';">Change</a>
                            <div id='profile_upload_avatar_div'>
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                <input name="uploadedfile" type="file" id='profile_upload' /><br />
                                <input type="button" value="Cancel" onclick="document.getElementById('profile_upload').value='';document.getElementById('profile_upload_avatar_div').style.display='none';" />
                            </div>
        
                        <?php endif ?>
                    </td>
                    <th class='thl'>Name:</th>
                    <td class='td'>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_name_txt' name='txt_name' maxlength='101' value='<?php echo trim($user->tbl_user->first_name.' '.$user->tbl_user->last_name) ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->first_name.' '.$user->tbl_user->last_name ?>
                        <?php endif ?>
                    </td>
                    <th class='thr'>Created By:</th>
                    <td>
                        <?php if($user->edit_admin): ?>
                            <select name='lst_created_by' id='profile_created_by_select'>
                                <option selected='selected' value='-1'></option>
                                <?php foreach ($user->users as $alias => $user_id): ?>
                                    <?php if($user->tbl_user->created_by==$user_id): ?>
                                        <option selected='selected' value='<?php echo $user_id ?>'><?php echo $alias ?></option>
                                    <?php else: ?>
                                        <option value='<?php echo $user_id ?>'><?php echo $alias ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>                    
                        <?php else: ?>
                            <?php echo Tbl_user::get_user($user->tbl_user->created_by)->alias ?>
                        <?php endif ?>
                    </td>
                </tr><tr>
                    <th class='thl'>Title:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_title_txt' name='txt_title' maxlength='50' value='<?php echo $user->tbl_user->title ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->title ?>
                        <?php endif ?>
                    </td>
                    <th class='thr'>Created On:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='text' id='profile_created_on_txt' name='txt_created_on' maxlength='19' value='<?php echo $user->tbl_user->created_on ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->created_on ?>
                        <?php endif ?>                    
                    </td>
                </tr><tr>
                    <th class='thl'>Email:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_email_txt' name='txt_email' maxlength='50' value='<?php echo $user->tbl_user->email ?>' />
                        <?php else: ?>
                            <? if ($info->is_authenticated): ?>
                                <a href='mailto:<?php echo $user->tbl_user->email ?>'><?php echo $user->tbl_user->email ?></a>
                            <? endif ?>
                        <?php endif ?>
                    </td>
                    <th class='thr'>Updated On:</th>
                    <td>
                        <?php echo $user->tbl_user->updated_on ?>
                    </td>                        
                </tr><tr>
                    <th class='thl'>Alias:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_alias_txt' name='txt_alias' maxlength='50' value='<?php echo $user->tbl_user->alias ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->alias ?>
                        <?php endif ?>
                    </td>
                    <th class='thr'>Last Login:</th>
                    <td>
                        <?php echo $user->tbl_user->last_login_on ?>
                    </td>            
                </tr><tr>
                    <th class='thl'>Disabled:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='checkbox' id='profile_disabled_chk' name='chk_disabled' <?php if($user->tbl_user->disabled): ?> checked='checked' <?php endif ?> />
                        <?php else: ?>
                            <input type='checkbox' name='chk_disabled_readonly' disabled='disabled' <?php if($user->tbl_user->disabled): ?> checked='checked' <?php endif ?> />
                        <?php endif ?>                    
                    </td>
                    <th class='thr'><?php if($info->admin): ?>Description:<?php endif ?></th>
                    <td>
                        <?php if($user->edit_admin): ?>
                            <input type='text' id='profile_description_txt' name='txt_description' value='<?php echo $user->tbl_user->description ?>' />
                        <?php elseif ($info->admin): ?>
                            <?php echo $user->tbl_user->description ?>
                        <?php endif ?>
                    </td>
                </tr><tr>
                    <th class='thl'>GlobalID:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_global_topic_id_txt' name='txt_global_topic_id' maxlength='8' value='<?php echo $user->tbl_user->global_topic_id ?>' />
                        <?php else: ?>
                            <? if ($user->tbl_user->global_topic_id): ?>
                                <a href='<?=Page::get_url('topic/'.$user->tbl_user->global_topic_id)?>'><?=$user->tbl_user->global_topic_id?></a>
                            <? endif ?>
                        <?php endif ?>
                    </td>
                    <th class='thl'>HomeID:</th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='text' id='profile_user_topic_id_txt' name='txt_user_topic_id' maxlength='8' value='<?php echo $user->tbl_user->user_topic_id ?>' />
                        <?php else: ?>
                            <? if ($user->tbl_user->user_topic_id): ?>
                                <a href='<?=Page::get_url('topic/'.$user->tbl_user->user_topic_id)?>'><?=$user->tbl_user->user_topic_id?></a>
                            <? endif ?>
                        <?php endif ?>
                    </td>
                </tr><tr>
                    <th class='thl'><?php if ($user->edit): ?>Password:<?php endif ?></th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='password' id='profile_password_txt' name='txt_password' autocomplete="off" />
                        <?php endif ?>
                    </td>
                    <th class='thr'><?php if ($user->edit): ?>Confirm:<?php endif ?></th>
                    <td>
                        <?php if($user->edit): ?>
                            <input type='password' id='profile_password_confirm_txt' name='txt_password_confirm' autocomplete="off" />
                        <?php endif ?>                    
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        

        
        <div class='section01'>
            <table class='profile_table' border='0'>
                <tbody valign='middle' align='left'>
                <tr>
                    <td rowspan='2' class='spacer_td'>&nbsp;</td>
                </tr><tr>
                    <th class='thl'>Topics:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='text' id='profile_topic_count_txt' name='txt_topic_count' maxlength='6' value='<?php echo $user->tbl_user->topic_count ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->topic_count ?>
                        <?php endif ?>                    
                    </td>
                    <th class='thr'>Comments:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='text' id='profile_comment_count_txt' name='txt_comment_count' maxlength='6' value='<?php echo $user->tbl_user->comment_count ?>' />
                        <?php else: ?>
                            <?php echo $user->tbl_user->comment_count ?>
                        <?php endif ?>                    
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        
        <? if ($info->is_authenticated): ?>
        <div class='section01'>
            <table class='profile_table' border='0'>
                <tbody valign='middle' align='left'>
                <tr>
                    <td rowspan='3' class='spacer_td'>&nbsp;</td>
                </tr><tr>                
                    <th class='thl'>Admin:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='checkbox' id='profile_admin_chk' name='chk_admin' <?php if($user->tbl_user->perm_admin): ?> checked='checked' <?php endif ?> />
                        <?php else: ?>
                            <input type='checkbox' name='chk_admin_readonly' disabled='disabled' <?php if($user->tbl_user->perm_admin): ?> checked='checked' <?php endif ?> />
                        <?php endif ?>
                        <span class='help'>User is super admin</span>
                    </td>
                    <th class='thr'>Create:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='checkbox' id='profile_create_chk' name='chk_create' <?php if($user->tbl_user->perm_create): ?> checked='checked' <?php endif ?> />
                        <?php else: ?>
                            <input type='checkbox' name='chk_create_readonly' disabled='disabled' <?php if($user->tbl_user->perm_create): ?> checked='checked' <?php endif ?> />
                        <?php endif ?>
                        <span class='help'>User can create new topics</span>
                    </td>
                </tr><tr>                
                    <th class='thl'>Exec:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='checkbox' id='profile_exec_chk' name='chk_exec' <?php if($user->tbl_user->perm_exec): ?> checked='checked' <?php endif ?> />
                        <?php else: ?>
                            <input type='checkbox' name='chk_exec_readonly' disabled='disabled' <?php if($user->tbl_user->perm_exec): ?> checked='checked' <?php endif ?> />
                        <?php endif ?>
                        <span class='help'>User can write execute code (php/cmd...)</span>
                    </td>
                    <th class='thr'>HTML:</th>
                    <td class='td'>
                        <?php if($user->edit_admin): ?>
                            <input type='checkbox' id='profile_html_chk' name='chk_html' <?php if($user->tbl_user->perm_html): ?> checked='checked' <?php endif ?> />
                        <?php else: ?>
                            <input type='checkbox' name='chk_html_readonly' disabled='disabled' <?php if($user->tbl_user->perm_html): ?> checked='checked' <?php endif ?> />
                        <?php endif ?>
                        <span class='help'>User can write HTML code</span>
                    </td>
                </tr><tr>
                        <th class='thl'>Groups:</th>
                        <td class='td' colspan='3'>
                            <div class='help'>NOTE: All users except anonymous should be in groups 'Public' and 'Users'.</div>
                            <table id='profile_perm_group_table'>
                            <?php foreach($user->perm_groups as $user->perm_group): ?>
                                <tr>
                                <td><input type='checkbox' <?php if(!$user->edit_admin): ?> disabled='disabled' <?php endif ?> <?php if($user->perm_group->selected): ?>checked='checked'<?php endif ?> name='chk_group_<?php echo $user->perm_group->perm_group_id ?>' value='<?php echo $user->perm_group->perm_group_id ?>' /></td>
                                <td class='profile_perm_group'><?php echo $user->perm_group->group ?></td>
                                <td class='profile_perm_group_description'><?php echo $user->perm_group->group_description ?></td>
                                </tr>
                            <?php endforeach ?>
                            </table>
                        </td>

                </tr><tr>
                    <td colspan='3' align='right'>
                        <?php if ($user->edit): ?>
                            <input type='button' name='btn_save' onclick='if (validate(<?php if($user->edit_admin) {echo 1;}else{echo 0;} ?>,<?php if($user->new_user) {echo 1;}else{echo 0;} ?>)) button_click(this);' value='Save Profile' />
                        <?php endif ?>
                    </td>
                    <td colspan='2'>
                        <?php if ($user->edit): ?>
                            <?php if($user->new_user): ?>
                                <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('admin/users') ?>'" value='Cancel' />
                            <?php else: ?>
                                <input type='button' name='btn_cancel' onclick="window.location='<?php echo Page::get_url('profile').'/'.$user->user_id ?>'" value='Cancel' />
                            <?php endif ?>
                            
                        <?php endif ?>
                    </td>
                </tr>            
                </tbody>
            </table>
        </div>
        <? endif ?>

    </div>
</div>