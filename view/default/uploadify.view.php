<?php eval(Page::load_code('master')) ?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en' dir='ltr'>
<head>
    <title><?php echo $view->title ?></title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta http-equiv='Content-Style-Type' content='text/css' />
    <?php if(isset($view->css)): ?>
        <?php foreach($view->css as $css): ?>
            <link rel='stylesheet' type='text/css' href='<?php echo $css ?>' />
        <?php endforeach ?>
    <?php endif ?>
    <?php if(isset($view->js)): ?>
        <?php foreach($view->js as $js): ?>
            <script language='javascript' src='<?php echo $js ?>' type='text/javascript'></script>
        <?php endforeach ?>
    <?php endif ?>
    

<script type="text/javascript">
    $(document).ready(function() {
      $('#file_upload').uploadify({
        'uploader'  : '<? echo Config::WEB_BASE.'theme/'.Config::THEME ?>/js/uploadify/uploadify.swf',
        'script'    : '<? echo Config::WEB_BASE.'theme/'.Config::THEME ?>/js/uploadify/uploadify.php',
        'cancelImg' : '<? echo Config::WEB_BASE.'theme/'.Config::THEME ?>/js/uploadify/cancel.png',
        'folder'    : '/tmp/<? echo $info->user_id ?>',
        'auto'      : true
      });
    });
</script>

<? #echo Config::FILES_DIR.'/'.$files->path ?>
</head>

<body>

    <h3>Upload to /files/<?php echo $files->path ?></h3>
	    
    <input id="file_upload" name="file_upload" type="file" />

</body>
</html>