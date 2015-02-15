<?
#http://petewarden.typepad.com/searchbrowser/2008/03/how-to-use-imap.html

$username = 'mreschke19';
$password = 'afqrsdwe!$23';

$mail_headers = gmail_summary_page($username, $password);

function gmail_summary_page($user, $password)
{
    $imapaddress = "{imap.gmail.com:993/imap/ssl}";
    $imapmainbox = "INBOX";
    $maxmessagecount = 10;

    $mail_headers = display_mail_summary($imapaddress, $imapmainbox, $user, $password, $maxmessagecount);
    return $mail_headers;
}

function display_mail_summary($imapaddress, $imapmainbox, $imapuser, $imappassword, $maxmessagecount)
{
    $mail_headers = array(); //the return array of object header

    $imapaddressandbox = $imapaddress . $imapmainbox;

    $connection = imap_open ($imapaddressandbox, $imapuser, $imappassword)
        or die("Can't connect to '" . $imapaddress .
        "' as user '" . $imapuser .
        "' with password '" . $imappassword .
        "': " . imap_last_error());

    #$count = imap_num_msg($connection);
    #for($msgno = 1; $msgno <= $count; $msgno++) {
    #  $headers = imap_headerinfo($connection, $msgno);
    #  if($headers->Unseen == 'U') {
    #    echo $headers->subject."<br />";
    #  }
    #}

    $result = imap_search($connection, "UNSEEN");
    for ($i=0; $i <= count($result); $i++) {
        $headers = imap_headerinfo($connection, $result[$i]);
        #$imap->subject = $headers->subject;
        $mail_headers[] = $headers;
        #echo $headers->subject."<br />";
    }
    
#var_dump($result);


#    echo "<u><h1>Gmail information for " . $imapuser ."</h1></u>";

#    echo "<h2>Mailboxes</h2>\n";
#    $folders = imap_listmailbox($connection, $imapaddress, "*")
#        or die("Can't list mailboxes: " . imap_last_error());

#    foreach ($folders as $val)
#        echo $val . "<br />\n";

#    echo "<h2>Inbox headers</h2>\n";
#    $headers = imap_headers($connection)
#        or die("can't get headers: " . imap_last_error());

#    $totalmessagecount = sizeof($headers);

#    echo $totalmessagecount . " messages<br/><br/>";

#    if ($totalmessagecount<$maxmessagecount)
#        $displaycount = $totalmessagecount;
#    else
#        $displaycount = $maxmessagecount;

#    for ($count=1; $count<=$displaycount; $count+=1)
#    {
#        $headerinfo = imap_headerinfo($connection, $count)
#            or die("Couldn't get header for message " . $count . " : " . imap_last_error());
#        $from = $headerinfo->fromaddress;
#        $subject = $headerinfo->subject;
#        $date = $headerinfo->date;
#        echo "<em><u>".$from."</em></u>: ".$subject." - <i>".$date."</i><br />\n";
#    }

#    echo "<h2>Message bodies</h2>\n";

#    for ($count=1; $count<=$displaycount; $count+=1)
#    {
#        $body = imap_body($connection, $count)
#            or die("Can't fetch body for message " . $count . " : " . imap_last_error());
#        echo "<pre>". htmlspecialchars($body) . "</pre><hr/>";
#    }

    imap_close($connection);
    return $mail_headers;
}



