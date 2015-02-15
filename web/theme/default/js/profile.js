

function validate(admin, new_user) {
    name=document.getElementById('profile_name_txt');
    created_by=document.getElementById('profile_created_by_select');
    if (!created_by) created_by = 0;
    //title=document.getElementById('profile_title_txt');
    created_on=document.getElementById('profile_created_on_txt');
    if (!created_on) created_on = 0;
    email=document.getElementById('profile_email_txt');
    //updated_on=document.getElementById('profile_updated_on_txt');
    alias=document.getElementById('profile_alias_txt');
    //last_login=document.getElementById('profile_last_login_txt');
    //disabled=document.getElementById('profile_disabled_chk');
    //description=document.getElementById('profile_description_txt');
    password=document.getElementById('profile_password_txt');
    password_confirm=document.getElementById('profile_password_confirm_txt');
    topic_count=document.getElementById('profile_topic_count_txt');
    if (!topic_count) topic_count = 0;
    comment_count=document.getElementById('profile_comment_count_txt');
    if (!comment_count) comment_count = 0;
    //admin=document.getElementById('profile_admin_chk');
    //create=document.getElementById('profile_create_chk');
    //signature

    validated = false;
    
    if (trim(name.value) == '') {
        alert('Please enter a name');
        name.focus();
    } else if (trim(email.value) == '') {
        alert('Please enter an email address');
        email.focus();
    } else if (trim(alias.value) == '') {
        alert('Please enter a user alias');
        alias.focus();
    } else if (new_user && trim(password.value) == '') {
        alert('Please enter a password');
        password.focus();
    } else if ((trim(password.value) != '' || trim(password_confirm.value) != '') && trim(password.value) != trim(password_confirm.value)) {
        alert('Passwords do not match');
        password.focus();
    } else if ((trim(password.value) == trim(password_confirm.value)) && trim(password.value).length > 0 && trim(password.value).length < 4) {
        alert('Password must be at least 4 characters');                    
        password.focus();
    } else {
        validated = true;
    }
    
    if (validated && admin) {
        validated = false;
        if (created_by.value <= 0) {
            alert('Please select creator of this user (Created By)');
            created_by.focus();
        } else if (created_on.value == '') {
            alert('Please enter a creation date (Created On)');
            created_on.focus();        
        //} else if (trim(topic_count.value) == '' || isNaN(topic_count.value)) {
            //alert('Please enter a topic count >= 0');
            //topic_count.focus();
        //} else if (comment_count.value == '' || isNaN(comment_count.value)) {
            //alert('Please enter a comment count >= 0');
            //comment_count.focus();
        } else {
            validated = true;
        }
    }
    return validated;
}