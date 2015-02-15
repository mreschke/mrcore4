<div id='login_outer_div'>
    <div id='login_div'>
        <center>
        <table class='table_pad'>
            <tr>
                <td align='right' title='Email or alias'>Username:</td>
                <td><input type="text" name="txtUsername" id='login_username_text' autofocus onkeyup="keyup_actions(this, event, 'login_submit');" title='Email or alias' /></td>
            </tr><tr>
                <td align='right'>Password:</td>
                <td><input type="password" name="txtPassword" id='login_password_text' onkeydown="keyup_actions(this, event, 'login_submit');" /></td>
            </tr><tr>
                <td>&nbsp;</td>
                <td align='right'><input type='button' onclick='button_click(this)' name='btn_login' value='Login' id='login_submit' /></td>
            </tr>
        </table>
        
        <?php if(@$view->message): ?>
            <div class="message_div">
                <div class='message'>
                    <?php echo $view->message ?>
                </div>
            </div>
        <?php endif ?>
        
        <input type='hidden' name='txt_referrer' value='<? echo $referrer ?>' />
        </center>
    </div>
</div>
