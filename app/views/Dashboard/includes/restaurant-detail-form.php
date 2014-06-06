<div id="restaurantDetailSelector" class="none">
    <label><?php echo SOCIAL_SELECTOR_LABEL; ?></label>
    <select id="restDetailSelect" class="none"></select>
    <div class="preloader"></div>
</div>
<br /><br />
<form id="detailsForm" method="post" class="none">
	<div class="last">
		<h1><?php echo DETAIL_TITLE; ?></h1>
	</div>
	<div class="clear">
		<label><?php echo DETAIL_NAME_LABEL; ?>: <b id="name"></b></label>
		<br />
		<label><?php echo DETAIL_ADDRESS_LABEL; ?>: <b id="address"></b></label>
		<br />
		<label><?php echo DETAIL_LATITUDE_LABEL; ?>: <b id="lat"></b></label>
		<br />
		<label><?php echo DETAIL_LONGITUDE_LABEL; ?>: <b id="lng"></b></label>
		<br />
		<label><?php echo DETAIL_TOWN_LABEL; ?>: <b id="city"></b></label>
		<br />
		<label><?php echo DETAIL_FOOD_LABEL; ?>: <b id="food"></b></label>
		<br />
		<label><?php echo DETAIL_TAGS_LABEL; ?>: <b id="tags"></b></label>
		<br />
		<label><?php echo DETAIL_KEYWORDS_LABEL; ?>: <b id="keywords"></b></label>
	</div>
	<div class="clear">
		<label><?php echo DETAIL_LOGO_LABEL; ?></label>
		<br /><br />
		<img id="logoThumb" src="/img/logo-placeholder-120.gif" alt="<?php echo DETAIL_LOGO_LABEL; ?>" width="126" height="120" />
		<br /><br />
		<a id="load-logo-button"><?php echo UPLOAD_LOGO; ?></a>
		<input type="hidden" id="logo" name="logo" value="" disabled="disabled" />
		<br /><br />
		<small><?php echo sprintf(DETAIL_LOGO_COPY, LOGO_IMAGE_WIDTH, LOGO_IMAGE_HEIGHT); ?></small>
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
	</div>
	<div class="clear">
		<label><?php echo DETAIL_DESCRIPTION_LABEL; ?></label>
		<br />
		<textarea id="content" name="content" rows="4" disabled="disabled"></textarea>
	</div>
	<div class="clear">
		<label><?php echo DETAIL_EXCERPT_LABEL; ?></label>
		<br />
		<textarea id="excerpt" name="excerpt" rows="2" disabled="disabled"></textarea>
	</div>
	<div class="clear">
		<div class="left">
			<h1><?php echo DETAIL_LOCAL_ELEMENTS; ?></h1>
			<?php foreach ($salLocalElements as $key => $value): ?>
			<input type="checkbox" name="elements[]" id="elements_<?php echo $key; ?>" value="<?php echo $key; ?>" disabled="disabled" /> 			
			<label for="elements_<?php echo $key; ?>"><?php echo $value; ?></label>
			<br />
			<?php endforeach; ?>
		</div>
		<div class="left">
			<h1><?php echo DETAIL_AMBIENCE; ?></h1>
			<?php foreach ($salAmbiences as $key => $value): ?>
			<input type="checkbox" name="ambience[]" id="ambience_<?php echo $key; ?>" value="<?php echo $key; ?>" disabled="disabled" /> 			
			<label for="ambience_<?php echo $key; ?>"><?php echo $value; ?></label>
			<br />
			<?php endforeach; ?>
		</div>
		<div class="left">
			<h1><?php echo DETAIL_PRICE; ?></h1>
			<?php foreach ($salPrices as $key => $value): ?>
			<input type="radio" name="price" id="price_<?php echo $key; ?>" value="<?php echo $key; ?>" disabled="disabled" />
			<label for="price_<?php echo $key; ?>"><?php echo $value; ?></label>
			<br />	
			<?php endforeach; ?>
		</div>
		<br class="clear" />
	</div>
	<div class="clear">
		<label><?php echo DETAIL_TIME; ?></label>
		<input type="text" id="time" name="time" value="" disabled="disabled" />
	</div>
	<div class="clear">
		<label><?php echo DETAIL_PHONE; ?></label>
		<input type="text" id="phone" name="phone" value="" disabled="disabled" />
	</div>
	<div class="clear">
		<label><?php echo DETAIL_MAIL; ?></label>
		<input type="text" id="mail" name="mail" value="" disabled="disabled" />
	</div>
	<div class="clear">
		<h1><?php echo DETAIL_MENU; ?></h1>
		<br />
		<label><?php echo DETAIL_MENU_PDF; ?></label>
		<input type="text" id="pdf_detail" value="" readonly="readonly" disabled="disabled" />
		<input type="hidden" id="menu_pdf" name="menu_pdf" value="" disabled="disabled" />

		<label><?php echo DETAIL_MENU_LOCU; ?></label>
		<input type="text" id="menu_locu" name="menu_locu" value="" disabled="disabled" />
	</div>
	<div class="clear">
		<label><?php echo DETAIL_FACEBOOK; ?></label>
		<input type="text" id="facebook" name="facebook" value="" disabled="disabled" />
	</div>
	<div class="clear">
		<label><?php echo DETAIL_TWITTER; ?></label>
		<input type="text" id="twitter" name="twitter" value="" disabled="disabled" />
	</div>
	<div class="clear last submitContainer">
		<input type="hidden" class="restaurantId" name="restaurant_id" value="" disabled="disabled" />
		<input type="submit" name="detail" value="<?php echo SAVE; ?>" disabled="disabled" />
	</div>
</form>
<div id="detailFormBlocker" class="pageBlocker"></div>
<div id="detailFormPreloader" class="preloader"></div>
<div id="logoUploader" class="none">
	<?php include('file-uploader.php'); ?>
</div>
<div id="pdfUploader" class="none">
	<iframe id="pdfUploaderTarget" name="pdfUploaderTarget" class="fileUploaderIframe"></iframe>
	<form enctype="multipart/form-data" method="post" target="pdfUploaderTarget" action="/ajax/File/upload" id="detailPdfUploaderForm">
		<input id="detailPdfDirname" name="fileDirname" type="hidden" value="Restaurant/menu" />
	 	<input id="detailPdf" type="file" name="file" value="" />
	</form>
</div>
<div id="formDetailResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<script type="text/javascript">

	function restaurantSaveAjax(pdfUploadError) {
		$.ajax({
			url: '/api/Restaurant/save',
			type: 'post',
			data: $('#detailsForm div').children().serialize(),
			success: function(response) {
				try {
					var result = $.parseJSON(response);
					var className = 'ui-state-highlight';
					var msg = result.message;
					if(!result.result) {
						className = 'ui-state-error';
					} else if(typeof pdfUploadError != 'undefined' && pdfUploadError == true) {
						msg = '<?php echo RESTAURANT_UPDATED_PDF_FAILURE; ?>';
						className = 'ui-state-error';
					}

					$('#formDetailResult').removeClass('ui-state-highlight ui-state-error');
					$('#formDetailResult').addClass(className);
					$('#formDetailResult p b').html(msg);
					$('#formDetailResult').dialog({
						modal: true,
						buttons: [
							{
								text: "<?php echo OK; ?>",
								click: function() {
									$(this).dialog('destroy');
									restaurantSelected($('.restaurantId').val());
								}
							}
						],
						dialogClass: "no-close"
					});
				} catch(e) {
					console.log(e);
				}
				$('#detailFormBlocker, #detailFormPreloader').hide();
			}
		});
	}


	function detailFormSubmit(event) {
		if(typeof event.data == 'undefined' 
			|| typeof event.data.franchise == 'undefined'
			|| event.data.franchise != '1' ) {
			if (!checkPhone($("#detailsForm #phone").val())) {
				showErrorDialog("Por favor, verifique el teléfono ingresado.");
				return false;
			}

			if (!checkEmail($("#detailsForm #mail").val())) {
				showErrorDialog("Por favor, verifique el email ingresado.");
				return false;
			}	

			if ($("#detailsForm #facebook").val() != '') {
				if (!checkURL($("#detailsForm #facebook").val())) {
					showErrorDialog("Por favor, verifique la url ingresada en el campo facebook.");
					return false;
				}
			}

			if ($("#detailsForm #twitter").val() != '') {
				if (!checkURL($("#detailsForm #twitter").val())) {
					showErrorDialog("Por favor, verifique la url ingresada en el campo twitter.");
					return false;
				}
			}
		}

		$('#detailFormBlocker, #detailFormPreloader').show();
		$('#pdfUploaderTarget').off('load');


		$('#pdfUploaderTarget').load(function() {
			try {
				var json = $('#pdfUploaderTarget').contents()
					.find('body').html();
				var object = $.parseJSON(json);
				if(!object.status) {
					throw object.errors[0];
				}
				$('#menu_pdf').val(object.response.path);
				restaurantSaveAjax();
			} catch(e) {
				$('#menu_pdf').val('');
				restaurantSaveAjax(true);
			}
		});	

		if($.trim($('#detailPdf').val()) != '') {
			$('#detailPdfUploaderForm').submit();
		} else {
			restaurantSaveAjax();
		}
		event.preventDefault();
		return false;
	}

	function checkPhone(phone) { 
	    var re = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/;
	    return re.test(phone);
	} 

	function checkEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	} 


	function checkURL(url)
	{
	  if (/^(https?:\/\/)?((w{3}\.)?)twitter\.com\/(#!\/)?[a-z0-9_]+$/i.test(url))
	     return 'twitter';    

	 if (/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i.test(url))
	     return 'facebook';

	 return false;
	}


	function checkDetailPdfFileType(event) {
		var selectedVal = $(this).val();
		if(!/\.pdf$/.test(selectedVal.toLowerCase())) {
			$(this).val('');
			$('#pdf_detail').val('');
			$('#formDetailResult').removeClass('ui-state-highlight ui-state-error');
			$('#formDetailResult').addClass('ui-state-error');
			$('#formDetailResult p b').html('<?php echo COUPON_PDF_FILE_TYPE_ERROR; ?>');
			$('#formDetailResult').dialog({
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
		} else {
			var showVal = selectedVal.replace(/^.[^\\]*\\.[^\\]*\\/, '');
			$('#pdf_detail').val(showVal);
		}
	}
	

	function selectDetailPdfFile(event) {
		$('#detailPdf').click();
	}

	var oldLogoGlobalImage;
	
	function uploadLogo(event) {
		var oldImage = $(this).attr('src');
		if($.trim(oldImage) != '') {
			oldLogoGlobalImage = oldImage;
		}
		var uploaderId = event.data.uploader;
		var inputId = event.data.input;
		var dirName = 'Restaurant/logo/' + $('.restaurantId').val();

		$('#' + uploaderId + ' input[name="fileType"]').val('image');
		$('#' + uploaderId + ' input[name="fileSizeW"]').val(<?php echo LOGO_IMAGE_WIDTH; ?>);
		$('#' + uploaderId + ' input[name="fileSizeH"]').val(<?php echo LOGO_IMAGE_HEIGHT; ?>);
		$('#' + uploaderId + ' input[name="fileDirname"]').val(dirName);
		$('#' + uploaderId + ' input[type="file"]').click();
		$('#' + uploaderId + ' iframe').off('load');
		$('#' + uploaderId + ' iframe').load(function(event){
			try {
				var json = $('#' + uploaderId + ' iframe').contents()
					.find('body').html();
				var object = $.parseJSON(json);
				$('#logoThumb').attr('src', '');                           
				$('#logoThumb').addClass('couponImagePreloader');
				if(!object.status) {
					$('#logoThumb').attr('src', oldImage);
					throw object.errors[0];
				} else {
					$('#logoThumb').off('load');
					$('#logoThumb').on('load', function() {
						$('#logoThumb').removeClass('couponImagePreloader');
					});
					$('#logoThumb').attr('src', object.response.path);
				}
				$('#' + inputId).val(object.response.path);
			} catch(e) {
				if(typeof oldLogoGlobalImage != 'undefined') {
					$('#logoThumb').attr('src', oldLogoGlobalImage);	
				} else {
					$('#logoThumb').attr('src', '/img/logo-placeholder-120.gif');
				}
				
				$('#formDetailResult').removeClass('ui-state-highlight ui-state-error');
				$('#formDetailResult').addClass('ui-state-error');
				$('#formDetailResult p b').html(e);
				$('#formDetailResult').dialog({
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
		});
	}

	function showDetailChildSelector(restaurant_id, restaurant_id_sal) {
		$('#restDetailSelect').html('');
        var optionTemplate = '<option value="%d">%s</option>';
        $('#restaurantDetailSelector').show();
        $.ajax({
            url: '/json/Restaurant/getFranchiseChilds/',
            type: 'get',
            data: { 'restaurant_id': restaurant_id },
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                if(response.code == 1) {
                    $('#restDetailSelect').append(
                        optionTemplate
                            .replace('%d', restaurant_id_sal)
                            .replace('%s', '<?php echo FRANCHISE; ?>')
                        );

                    for(var prop in response.response) {
                        $('#restDetailSelect').append(
                            optionTemplate
                                .replace('%d', prop)
                                .replace('%s', response.response[prop])
                            );
                    }

                    $('#restDetailSelect').show();
                }
            },
            complete: function() {
                $('#restaurantDetailSelector .preloader').hide();
            }
        });
    }

    function getSelectedRestDetail(event) {
    	$('.pageBlocker').show().next('.preloader').show();
    	$.ajax({
            url: '/json/Restaurant/childDetail/',
            type: 'get',
            data: {
                'restaurant_id': $(event.currentTarget).val(),
                'parent_id': $($('#restDetailSelect option')[0]).val()
            },
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                restaurantSelected(response.response.id, true);
            }
        });
    }
    $('#restDetailSelect').on('change', getSelectedRestDetail);

	

</script>