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
    $(function(){
	$('#swfupload-control').swfupload({
	    upload_url: '<?php echo Config::WEB_BASE.'upload-file.php?path='.$files->path ?>',
	    file_post_name: 'uploadfile',
	    file_size_limit : "1048576", //In KB
	    //file_types : "*.jpg;*.png;*.gif",
	    file_types : "*.*",
	    //file_types_description : "Image files",
	    file_types_description : "All Files",
	    file_upload_limit : 100,
	    flash_url : '<?php echo Config::WEB_BASE.'theme/'.Config::THEME ?>/js/uploader/swfupload.swf',
	    button_placeholder : $('#button')[0],
	    button_image_url : '<?php echo Config::WEB_BASE.'theme/'.Config::THEME ?>/images/wdp_buttons_upload_114x29.png',
	    button_width : 114,
	    button_height : 29,
	    //button_image_url : '/theme/devel/images/XPButtonUploadText_61x22.png',
	    //button_image_url : '/theme/devel/images/upload.png',
	    //button_width : 32,
	    //button_height : 32,
	    debug: false
	})
	    .bind('fileQueued', function(event, file){
		var listitem='<li id="'+file.id+'" >'+
		    'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
		    '<div class="progressbar" ><div class="progress" ></div></div>'+
		    '<p class="status" >Pending</p>'+
		    '<span class="cancel" >&nbsp;</span>'+
		    '</li>';
		$('#log').append(listitem);
		$('li#'+file.id+' .cancel').bind('click', function(){
		    var swfu = $.swfupload.getInstance('#swfupload-control');
		    swfu.cancelUpload(file.id);
		    $('li#'+file.id).slideUp('fast');
		});
		// start the upload since it's queued
		$(this).swfupload('startUpload');
	    })
	    .bind('fileQueueError', function(event, file, errorCode, message){
		alert('Size of the file '+file.name+' is greater than limit');
	    })
	    .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
		$('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
	    })
	    .bind('uploadStart', function(event, file){
		$('#log li#'+file.id).find('p.status').text('Uploading...');
		$('#log li#'+file.id).find('span.progressvalue').text('0%');
		$('#log li#'+file.id).find('span.cancel').hide();
	    })
	    .bind('uploadProgress', function(event, file, bytesLoaded){
		//Show Progress
		var percentage=Math.round((bytesLoaded/file.size)*100);
		$('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
		$('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
	    })
	    .bind('uploadSuccess', function(event, file, serverData){
		var item=$('#log li#'+file.id);
		item.find('div.progress').css('width', '100%');
		item.find('span.progressvalue').text('100%');
		var pathtofile='<a href="<?php echo Page::get_url('files').'/'.$files->path.'/' ?>'+file.name+'" target="_blank">view &raquo;</a>';
		item.addClass('success').find('p.status').html('Done!!! | '+pathtofile);
	    })
	    .bind('uploadComplete', function(event, file){
		// upload has completed, try the next one in the queue
		$(this).swfupload('startUpload');
	    })
    });	

</script>

</head>

<body>

    <h3>Upload to /files/<?php echo $files->path ?></h3>
	    
    <div id="swfupload-control">
	<p>Upload max: 55555TB!!!</p>
	<input type="button" id="button" />
	<p id="queuestatus" ></p>
	<ol id="log"></ol>
    </div>



</body>
</html>