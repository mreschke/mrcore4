<?php eval(Page::load_code('part/menu/sites')) ?>

<div class='m_<?php echo $left_right ?>_header'>My Sites</div>
<div class='m_<?php echo $left_right ?>_item'>
    <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com/mrcore/mrticles/Home' ?>' title='Previous version or this website, full of old articles'>mrcore3</a></div>
    <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://photos.mreschke.com' ?>' title='One of my temporary photo galleries'>photos</a></div>
    <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://gallery.mreschke.com' ?>' title='Another one of my temporary photo galleries'>gallery</a></div>
    <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://coppermine.mreschke.com' ?>' title='Yet another one of my temporary photo galleries'>coppermine</a></div>
    <hr />
    <div class='m_<?php echo $left_right ?>_line'><a href='http://subsonic.mreschke.com' target='_blank' title='Streaming music collection on the texas server'>subsonic.mreschke</a></div>
    <div class='m_<?php echo $left_right ?>_line'><a href='http://subsonic.mediaqons.com' target='_blank' title='Streaming music collection on the colorado server'>subsonic.mediaqons</a></div>
    <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mediaqons.com' ?>' title='My colorado servers website (defunct)'>mediaqons</a></div>
    
    <?php if($info->is_authenticated && $info->tbl_user->perm_admin): ?>
        <hr />
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com:8112' ?>'>deluge</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com:49152' ?>'>mediatomb</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mp3act.mreschke.com' ?>'>mp3act</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://jinzora.mreschke.com' ?>'>jinzora</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://kplaylist.mreschke.com' ?>'>kplaylist</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://relay.mreschke.com' ?>'>relay</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com/mrext/3p/mwiki' ?>'>mwiki</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='https://mreschke.com:12200' target='_blank'>webmin</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://server.mreschke.com/mrhistory/mreschke-first' ?>'>mReschke First</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://bugzilla.mreschke.com' ?>'>bugzilla</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://torrentflux.mreschke.com' ?>'>torrentflux</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://blog.mreschke.com/?q=tracker' ?>'>drupal blog</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://dynatron.mreschke.com' ?>'>dynatron blog</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com/mrext/3p/joomla' ?>'>joomla</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://server.mreschke.com/sugarCRM' ?>'>sugar CRM</a></div>
        <div class='m_<?php echo $left_right ?>_line'><a href='<?php echo Page::get_url('net').'/http://mreschke.com/mrext/3p/eyeos' ?>'>eye OS</a></div>
    <?php endif ?>
</div>
