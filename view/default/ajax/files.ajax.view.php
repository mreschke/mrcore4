<?php if(!$files->embed): ?>
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
    </head>
    <body>
<?php endif ?>




<?php eval(Page::load_part('files')); load_files_view(); ?>





<?php if(!$files->embed): ?>
</body>
</html>
<?php endif ?>