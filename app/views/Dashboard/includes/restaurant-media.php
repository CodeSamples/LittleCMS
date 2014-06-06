<form id="mediaForm" method="post" class="none">
    <div class="clear">
        <h1><?php echo MEDIA_IMG_LABEL; ?></h1>
        <div id="saveAdvice" class="ui-state-highlight clear none">
		<p>
			<span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>
                        <span id="saveAdviceMessage"><?php echo SAVE_NOTICE; ?></span>
		</p>
	</div>
        <small><?php echo sprintf(DETAIL_LOGO_COPY, MIN_IMAGE_WIDTH, MIN_IMAGE_HEIGHT); ?></small>
        <br />
        <small><?php 
            $showedFileTypes = array();
            foreach (File::$allowedFileTypes as $type) {
                if(preg_match('/image/', $type)) {
                    $showedFileTypes[] = $type;
                }
            }
            echo sprintf(DETAIL_LOGO_COPY_FORMAT, implode(', ', $showedFileTypes)); 
            ?></small>
        <br />
        <small><?php 
            $maxSize = File::MAX_FILE_SIZE / 1024;
        echo sprintf(DETAIL_LOGO_COPY_SIZE, $maxSize . ' Kb'); ?></small>
        <br />
        <div id="addImage" class="addButton"><?php echo DETAIL_NEW_PIC; ?></div>
        <br /><br />
        <label><?php echo DETAIL_ACTUAL_PICS; ?></label>
        <br />
        <div id="imagesContainer"></div>
        <br class="clear" />
    </div>
    <div class="clear">
        <h1><?php echo MEDIA_VIDEOS_LABEL; ?></h1>
        <small><?php 
            $showedFileTypes = array();
            foreach (File::$allowedFileTypes as $type) {
                if(preg_match('/video/', $type)) {
                    $showedFileTypes[] = $type;
                }
            }
            echo sprintf(DETAIL_LOGO_COPY_FORMAT, implode(', ', $showedFileTypes)); 
            ?></small>
        <br />
        <small><?php 
            $maxSize = File::MAX_FILE_SIZE / 1024;
        echo sprintf(DETAIL_LOGO_COPY_SIZE, $maxSize . ' Kb'); ?></small>
        <br />
        <div id="addVideo" class="addButton"><?php echo DETAIL_NEW_VIDEO; ?></div>
        <br /><br />
        <label><?php echo DETAIL_ACTUAL_VIDEOS; ?></label>
        <br />
        <div id="videosContainer"></div>
        <br class="clear" />
    </div>
    <div class="clear last submitContainer">
        <input type="hidden" id="galleryId" name="gallery_id" value="" />
        <input type="hidden" id="videosIds" value="" />
        <input type="hidden" class="restaurantId" name="restaurant_id" value="" />
        <input type="hidden" class="salId" value="" />
        <input type="submit" name="detail" value="<?php echo SAVE; ?>" />
    </div>
</form>
<div id="imageTemplate" class="centerText none">
    <img src="/img/logo-placeholder-120.gif" alt="" width="120" height="120" />
    <input type="hidden" name="media_list[]" value="" />
    <br />
    <a class="deleteButton"><?php echo DELETE; ?></a>
</div>
<div id="galleryImageUploader" class="none">
    <?php include('file-uploader.php'); ?>
</div>
<div id="galleryVideoUploader" class="none">
    <?php include('file-uploader.php'); ?>
</div>
<div id="mediaFormBlocker" class="pageBlocker"></div>
<div id="mediaFormPreloader" class="preloader"></div>
<div id="formMediaResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<div id="videoViewer" class="ui-corner-all none">
    <iframe></iframe>
</div>
<script type="text/javascript">
    var changesUnsaved = false;
    var imageCounter = 0;
    var imageLimit = 0;
    var videoCounter = 0;
    var videoLimit = 0;

    function populateRestaurantMedia(media, maxPics, maxVideos) {
        imageLimit = maxPics; 
        videoLimit = maxVideos;
        var template = $('#imageTemplate');
        $('#imagesContainer').html('');
        for(var i = 0; i < media.media_list.length; i++) {
            var item = $(template).clone();
            if(media.media_list[i].external_id == 0) {
                if(!/<?php echo addcslashes(BUMBIA_FILETYPE, "\/"); ?>/.test(media.media_list[i].type)) {
                    media.media_list[i].external_id = 'I' + imageCounter;
                } else {
                    media.media_list[i].external_id = 'V' + videoCounter;
                }
            }
            $(item).find('input').attr('name', 'media_list[' + media.media_list[i].external_id + ']');
            $(item).find('input').val(media.media_list[i].filename + ',' + media.media_list[i].type);
            $(item).find('.deleteButton').button({
                    icons: {
                        primary: 'ui-icon-circle-plus'
                    }
                }).on('click', deleteMediaImage);
            if(!/<?php echo addcslashes(BUMBIA_FILETYPE, "\/"); ?>/.test(media.media_list[i].type)) {
                $(item).find('img').attr('src', media.media_list[i].filename);
                $(item).attr('id', 'img_' + imageCounter);
                $(item).appendTo('#imagesContainer').addClass('imgItem').removeClass('none');
                imageCounter++;
            } else {    
                if(media.media_list[i].external_id == 0) {
                    $(item).find('img').attr('src', '/img/video-icon.png');
                } else {
                    $(item).find('img').attr('src', media.media_list[i].filename);
                }
                $(item).attr('id', 'vid_' + videoCounter);
                $(item).on('click', previewVideo);
                $(item).appendTo('#videosContainer').addClass('vidItem').removeClass('none');
                videoCounter++;
            }
        }
        $('#mediaFormBlocker, #mediaFormPreloader').hide();
    }

    function deleteMediaImage(event) {
        var parentId = $(event.currentTarget).parent().attr('id');
        if(/img/.test(parentId)) {
            imageCounter--;
        } else {
            videoCounter--;
        }
        $(event.currentTarget).parent().remove();        
        changesUnsaved = true;
    }

    function mediaFormSubmit(event) {
        $('#mediaFormBlocker, #mediaFormPreloader').show();
        $.ajax({
            url: '/json/Gallery/save',
            data: $(this).serialize(),
            type: 'post',
            success: function(response) {
                if(!typeof response == 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                if(response.code != 1) {
                    showMediaErrorDialog(response.response.message);
                } else {
                    showMediaSuccessDialog(response.response.message);
                }
                $('#mediaFormBlocker, #mediaFormPreloader').hide();
                $("#saveAdvice").fadeOut();
                changesUnsaved = false;
            }
        });
        event.preventDefault();
        return false;
    }
    

    function showMediaErrorDialog(message) {
        $('#formMediaResult').removeClass('ui-state-highlight ui-state-error');
        $('#formMediaResult').addClass('ui-state-error');
        $('#formMediaResult p b').html(message);
        $('#formMediaResult').dialog({
            modal: true,
            buttons: [
                {
                    text: "<?php echo OK; ?>",
                    click: function() {
                        $(this).dialog('destroy');
                    }
                }
            ],
            dialogClass: "no-close"
        });
    }

    function showMediaSuccessDialog(message) {
        $('#formMediaResult').removeClass('ui-state-highlight ui-state-error');
        $('#formMediaResult').addClass('ui-state-highlight');
        $('#formMediaResult p b').html(message);
        $('#formMediaResult').dialog({
            modal: true,
            buttons: [
                {
                    text: "<?php echo OK; ?>",
                    click: function() {
                        $(this).dialog('destroy');
                    }
                }
            ],
            dialogClass: "no-close"
        });
    }

    function uploadGalleryImage(event) {
        if(imageCounter < imageLimit) {
            var uploaderId = 'galleryImageUploader';
            var dirName = 'Gallery/' + $('#galleryId').val();
            $('#' + uploaderId + ' input[name="fileType"]').val('image');
            $('#' + uploaderId + ' input[name="fileDirname"]').val(dirName);
            $('#' + uploaderId + ' input[type="file"]').click();
            $('#' + uploaderId + ' iframe').off('load');
            $('#' + uploaderId + ' iframe').load(function(event){
                $('#mediaFormBlocker, #mediaFormPreloader').hide();
                try {
                    var json = $('#' + uploaderId + ' iframe').contents()
                        .find('body').html();
                    var object = $.parseJSON(json);
                    if(!object.status) {
                        throw object.errors[0];
                    } 
                    var template = $('#imageTemplate');
                    var item = $(template).clone();
                    $(item).attr('id', $(item).attr('id') + '_' + imageCounter);
                    $(item).find('img').attr('src', object.response.path);
                    $(item).find('input').attr('name', 'media_list[I' + imageCounter + ']');
                    $(item).find('input').val(object.response.path + ',' + object.response.type);
                    $(item).find('.deleteButton').button({
                            icons: {
                                primary: 'ui-icon-circle-plus'
                            }
                        }).on('click', deleteMediaImage);
                    $(item).appendTo('#imagesContainer').addClass('imgItem').removeClass('none');
                    imageCounter++;                    
                    changesUnsaved = true;
                } catch(e) {
                    showMediaErrorDialog(e);
                }
            });
        } else {
            showMediaErrorDialog('<?php echo MEDIA_IMG_LIMIT; ?>');
        }
    }
    

    function uploadGalleryVideo(event) {
        if(videoCounter < videoLimit) {
            var uploaderId = 'galleryVideoUploader';
            var dirName = 'Gallery/' + $('#galleryId').val();
            $('#' + uploaderId + ' input[name="fileType"]').val('video');
            $('#' + uploaderId + ' input[name="fileDirname"]').val(dirName);
            $('#' + uploaderId + ' input[type="file"]').click();
            $('#' + uploaderId + ' iframe').off('load');
            $('#' + uploaderId + ' iframe').load(function(event){
                $('#mediaFormBlocker, #mediaFormPreloader').hide();
                try {
                    var json = $('#' + uploaderId + ' iframe').contents()
                        .find('body').html();
                    var object = $.parseJSON(json);
                    if(!object.status) {
                        throw object.errors[0];
                    } 
                    var template = $('#imageTemplate');
                    var item = $(template).clone();
                    $(item).attr('id', 'vid_' + videoCounter);
                    $(item).find('img').attr('src', '/img/video-icon.png');
                    $(item).find('input').attr('name', 'media_list[V' + videoCounter + ']');
                    $(item).find('input').val(object.response.path + ',' + object.response.type);
                    $(item).find('.deleteButton').button({
                            icons: {
                                primary: 'ui-icon-circle-plus'
                            }
                        }).on('click', deleteMediaImage);
                    $(item).on('click', previewVideo);
                    $(item).appendTo('#videosContainer').addClass('vidItem').removeClass('none');
                    videoCounter++;                    
                    changesUnsaved = true;
                } catch(e) {
                    $('#' + uploaderId + ' input[type="file"]').val('');
                    showMediaErrorDialog(e);
                }
            });
        } else {
            showMediaErrorDialog('<?php echo MEDIA_VIDEO_LIMIT; ?>');
        }
    }
    

    function previewVideo(event) {
        var videoId = $(event.currentTarget).find('input').attr('name').replace(/\D*/g, '');
        if(videoId == 0) {
            var value = $(event.currentTarget).find('input').val().split(',');
            $('#videoViewer > iframe').attr('src', value[0]);
        } else {
            $('#videoViewer > iframe').attr('src', '/ajax/Video/showVideo?video_id=' + videoId);
        }

        $('#videoViewer').dialog({
            modal: true,
            resizable: false,
            width: 590,
            height: 380,
            close: function(event, ui) {
                $('#videoViewer > iframe').attr('src', '');
            }
        });
    }

    $(document).ready(function(){
        $('#mediaForm').submit(mediaFormSubmit);
        $('#addImage').on('click', uploadGalleryImage);
        $('#addVideo').on('click', uploadGalleryVideo);
        $('#galleryImageUploader input[type="file"], #galleryVideoUploader input[type="file"]')
            .on('change', function() {
                if($.trim($(this).val()) != '') {
                    $('#mediaFormBlocker, #mediaFormPreloader').show();        
                }
            });
        window.onbeforeunload = function (e) {
            var message = '<?php echo SAVE_NOTICE; ?>';
            e = e || window.event;
            
             if(changesUnsaved){
                $("#saveAdvice").show();
                if (e) {
                    e.returnValue = message;
                }
                return message;
            }
        };    
    });

</script>