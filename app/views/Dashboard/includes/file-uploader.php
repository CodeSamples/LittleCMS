<?php 

	//global $fileDirname;
	$fileDirname = (!isset($fileDirname)) ? '' : $fileDirname;
	$suffix = substr(md5(strtotime("now") . $fileDirname . rand()), -5);

?>
<iframe id="fileUploaderTarget<?php echo $suffix; ?>" name="fileUploaderTarget<?php echo $suffix; ?>" class="fileUploaderIframe"></iframe>
<form enctype="multipart/form-data" method="post" target="fileUploaderTarget<?php echo $suffix; ?>" action="/ajax/File/upload" id="fileUploadForm<?php echo $suffix; ?>">
	<input id="fileDirname<?php echo $suffix; ?>" name="fileDirname" type="hidden" value="<?php echo $fileDirname; ?>" />
	<input id="fileType<?php echo $suffix; ?>" type="hidden" value="" name="fileType" />
	<input id="fileSizeW<?php echo $suffix; ?>" type="hidden" value="" name="fileSizeW" />
	<input id="fileSizeH<?php echo $suffix; ?>" type="hidden" value="" name="fileSizeH" />
 	<input id="file<?php echo $suffix; ?>" type="file" name="file" value="" />
 	<input type="hidden" name="MAX_FILE_SIZE" value="512000" />
</form>
<script type="text/javascript">
	function uploadFile<?php echo $suffix; ?>(event) {
		if($.trim($(this).val()) != '') {
			$('#fileUploadForm<?php echo $suffix; ?>').submit();
		}
	}
	$('#file<?php echo $suffix; ?>').on('change', uploadFile<?php echo $suffix; ?>);
</script>