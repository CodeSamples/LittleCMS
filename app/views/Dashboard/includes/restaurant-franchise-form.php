<form id="franchiseForm" method="post" class="none">
	<div class="last">
		<h1><?php echo FRANCHISE_COUPON; ?></h1>
	</div>
	<div class="franchiseCouponItem left">
		<div>
			<label><?php echo FRANCHISE_COUPON_NAME; ?></label>
			<br />
			<input type="text" id="name" name="name" value="" />
		</div>
		<div>
			<label><?php echo FRANCHISE_COUPON_CAPTION; ?></label>
			<br />
			<textarea id="caption" name="caption"></textarea>
		</div>
		<div>
			<label><?php echo FRANCHISE_COUPON_IMAGE; ?></label>
			<br />
			<img src="/img/logo-placeholder-120.gif" alt="<?php echo FRANCHISE_COUPON_IMAGE; ?>" width="120" height="120" id="couponImageThumb" />
             <br /><br /><a id="load-coupon-button"><?php echo UPLOAD_IMAGE; ?></a>
			<input type="hidden" id="image" name="image" value="" />
			<br /><br />
		</div>
		<div>
			<label><?php echo FRANCHISE_COUPON_START; ?></label>
			<br />
			<input type="hidden" id="startDb" name="start" value="" />
			<input type="text" id="start" value="" class="date" />
			<span class="dateIcon"></span>
		</div>
		<div>
			<label><?php echo FRANCHISE_COUPON_END; ?></label>
			<br />
			<input type="hidden" id="endDb" name="end" value="" />
			<input type="text" id="end" value="" class="date" />
			<span class="dateIcon"></span>
			<div></div>
		</div>
		<div>
			<label><?php echo FRANCHISE_COUPON_PDF; ?></label>
			<br />
			<input type="text" id="pdf" readonly="readonly" />
			<input type="hidden" id="pdf_hide" name="pdf" value="" />
			<br /><br /><a id="load-coupon-pdf-button"><?php echo UPLOAD_PDF; ?></a>
		</div>
		<br />
		<div class="centerText">
			<input type="hidden" class="restaurantId" name="restaurant_id" value="" />
			<input type="submit" name="coupon" value="<?php echo SAVE; ?>" />
		</div>
	</div>
	<div class="franchiseCouponList right">
		<div id="tableCoupons" class="tableContainer"></div>
	</div>
	<br class="clear" />
</form>
<div id="couponImageUploader" class="none">
	<?php $fileDirname = 'Coupon/add'; include('file-uploader.php'); ?>
</div>
<div id="couponPdfUploader" class="none">
	<iframe id="couponPdfUploaderTarget" name="couponPdfUploaderTarget" class="fileUploaderIframe"></iframe>
	<form enctype="multipart/form-data" method="post" target="couponPdfUploaderTarget" action="/ajax/File/upload" id="couponPdfUploaderForm">
		<input id="filePdfDirname" name="fileDirname" type="hidden" value="Coupon/add" />
	 	<input id="filePdf" type="file" name="file" value="" />
	</form>
</div>
<div id="editCoupondialog" class="none">
	<form id="couponEditForm">
		<div class="franchiseCouponItem">
			<div>
				<label><?php echo FRANCHISE_COUPON_NAME; ?></label>
				<br />
				<input type="text" id="name_edit" name="name_edit" value="" />
			</div>
			<div>
				<label><?php echo FRANCHISE_COUPON_CAPTION; ?></label>
				<br />
				<textarea id="caption_edit" name="caption_edit"></textarea>
			</div>
			<div>
				<label><?php echo FRANCHISE_COUPON_IMAGE; ?></label>
				<br />
				<img id="couponImageThumb_edit" src="" alt="<?php echo FRANCHISE_COUPON_IMAGE; ?>" width="120" height="120" />
				<input type="hidden" id="image_edit" name="image_edit" value="" />
				<br /><br /><a id="load-coupon-button_edit"><?php echo UPLOAD_IMAGE; ?></a> <a id="delete-img-coupon-button_edit"><?php echo DELETE; ?></a>
			</div>
			<div>
				<label><?php echo FRANCHISE_COUPON_START; ?></label>
				<br />
				<input type="hidden" id="startDb_edit" name="start_edit" value="" />
				<input type="text" id="start_edit" value="" class="date" ></span>
				<span class="dateIcon"></span>
			</div>
			<div>
				<label><?php echo FRANCHISE_COUPON_END; ?></label>
				<br />
				<input type="hidden" id="endEdDb_edit" name="end_edit" value="" />
				<input type="text" id="end_edit" value="" class="date" />
				<span class="dateIcon"></span>
			</div>
			<div>
				<label><?php echo FRANCHISE_COUPON_PDF; ?></label>
				<br />
				<input type="text" id="pdf_edit" value="" readonly="readonly" />
				<input type="hidden" id="pdf_edit_hide" name="pdf_edit" value="" />
				<br /><br /><a id="load-coupon-pdf-button_edit"><?php echo UPLOAD_PDF; ?></a>
			</div>
			<br />
			<div class="centerText">
				<input type="hidden" class="restaurantId" name="restaurant_id" value="" />
				<input type="hidden" id="edit_coupon_id" name="edit_coupon_id" value="" />
				<input type="submit" name="coupon_edit" value="<?php echo SAVE; ?>" />
			</div>
		</div>
	</form>
	<div id="couponEditImageUploader" class="none">
		<?php $fileDirname = 'Coupon/edit'; include('file-uploader.php'); ?>
	</div>
</div>
<div id="formFranchiseResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<div id="franchiseFormBlocker" class="pageBlocker"></div>
<div id="franchiseFormPreloader" class="preloader"></div>
<div id="couponPreviewDialog" class="none">
    <h1><?php echo COUPON_PREVIEW_TITLE; ?></h1>
    <div class="couponContainer">
        <div class="image">
            <img src="" alt="">
        </div>
        <div class="txt">
            <h2></h2>
            <p class="couponTitle"></p>
            <p class="couponTxt"></p>
        </div>
    </div>
    <br style="clear:both;" />
    <br style="clear:both;" />
</div>
<script type="text/javascript" src="/js/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="/js/jquery.jtable.min.js"></script>
<script type="text/javascript" src="/js/jquery.jtable.es.js"></script>
<link href="/css/jtable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">

	$(document).ready(function() {
		$('#start, #end, #start_edit, #end_edit').button({
			create:buttonCreateFunction
		});
		$('.dateIcon').button({
			text: false,
			icons: {
				primary: 'ui-icon-calendar'
			}
		});
		$('#start, #end, #start_edit, #end_edit').parent().buttonset();


		$('.dateIcon').on('click', function() {
			$(this).prev().datepicker('show');
		});

		$('#tableCoupons').jtable(jTableParams);
		$('#franchiseForm').submit(franchiseFormSubmit);
		$('#couponEditForm').submit(couponEditFromSubmit);

		$('#couponImageThumb').on(
			'click', 
			{ 
				uploader: "couponImageUploader", 
				input: "image"
			},
			uploadCouponImage);

		$('#couponImageThumb_edit').on(
			'click', 
			{ 
				uploader: "couponEditImageUploader",
				input: "image_edit",
				check_coupon_id: true
			},
			uploadCouponImage);

		$('#filePdf').on('change', checkPdfFileType);
		$('#pdf, #pdf_edit').on('click', selectPdfFile);
                
                $('#load-coupon-button').button({
                                            icons: { 
                                                primary:"ui-icon-image"
                                            }
                                        });
                $('#load-coupon-button').click(function(){
                    $('#couponImageThumb').click();
                });
                $('#load-coupon-button_edit').button({
                                            icons: { 
                                                primary:"ui-icon-image"
                                            }
                                        });
                $('#load-coupon-button_edit').click(function(){
                    $('#couponImageThumb_edit').click();
                });
                
                $('#delete-img-coupon-button_edit').button({
                                            icons: { 
                                                primary:"ui-icon-circle-close"
                                            }
                                        });
                
                $('#delete-img-coupon-button_edit').click(function(){
                    $('#couponImageThumb_edit').attr('src', '/img/logo-placeholder-120.gif');
                    $('#image_edit').val("");
                });

                /* Load pdf button _ */
                $('#load-coupon-pdf-button, #load-coupon-pdf-button_edit').button({
                                            icons: { 
                                                primary:"ui-icon-file"
                                            }
                                        });
                $('#load-coupon-pdf-button').click(function(){
                    $('#pdf').click();
                });
                $('#load-coupon-pdf-button_edit').click(function(){
                    $('#pdf_edit').click();
                });
	});

	
	function buttonCreateFunction(event, ui) {
		var minDateObject = new Date();
		if(minDateObject.getMinutes() > 30) {
			if(minDateObject.getHours() < 23) {
				minDateObject.setHours(minDateObject.getHours() + 1);
			} else {
				minDateObject.setHours(0);
				minDateObject.setDate(minDateObject.getDate() + 1);
			}
			minDateObject.setMinutes(0);
		} else {
			minDateObject.setMinutes(30);
		}
		minDateObject.setSeconds(0);

		$(this).on('keydown', function(){ return false; });
		$(this).datetimepicker({
			minDate: minDateObject,
			altField: "#" + $(this).prev().attr('id'),
			altFormat: 'yy-mm-dd',
			altTimeFormat: 'HH:mm:ss',
			dateFormat: 'dd/mm/yy',
			timeFormat: 'hh:mm tt',
			showSecond: false,
			showMinute: false,
			constrainInput: true,
			altFieldTimeOnly: false,
			closeText: '<?php echo OK; ?>',
			currentText: '<?php echo NOW; ?>',
			onSelect: function(dateText, inst) {
				var selectedDate = $(this).datepicker('getDate');
				var selector = '#';
				var option = "";

				switch($(inst).attr('id')) {
					case 'start':
						option = 'minDate';
						selector += 'end';
					break;
					case 'start_edit':
						option = 'minDate';
						selector += 'end_edit';
					break;
					case 'end':
						option = 'maxDate';
						selector += 'start';
					break;
					case 'end_edit':
						option = 'maxDate';
						selector += 'start_edit';
					break;
				}
				$(selector).datepicker('option', option, selectedDate);
				$(selector).datepicker('option', option + 'Time', selectedDate);
			}
		});
	}

	var jTableParams = {
		title: '<?php echo FRANCHISE_COUPON_LIST; ?>',
		paging: true,
		pageSizeChangeArea: false,
		sorting: false,
		pageSizes: [10],
		defaultSorting: 'id DESC',
		actions: {
			listAction: '/api/Coupon/getCoupons',
			deleteAction: '/api/Coupon/deleteCoupon'
		},
		recordsLoaded: function(event, data) {
			$('.jtable-command-button').parents('td').addClass('jtable-command-column');
			$('#franchiseFormBlocker, #franchiseFormPreloader').hide();
		},
		fields: {
			id: {
				title: '<?php echo ID; ?>',
				key: true,
				list: false
			},
			name: {
				title: '<?php echo FRANCHISE_COUPON_NAME; ?>'
			},
			caption: {
				title: '<?php echo FRANCHISE_COUPON_CAPTION; ?>'
			},
			image: {
				title: '<?php echo FRANCHISE_COUPON_IMAGE; ?>',
				display: function(data) {
					return '<img width="64" height="64" src="' + data.record.image + '" />';
				}
			},
			start: {
				title: '<?php echo FRANCHISE_COUPON_START; ?>'
			},
			end: {
				title: '<?php echo FRANCHISE_COUPON_END; ?>'
			},
			startDb: {
				list: false
			},
			endDb : {
				list: false
			},
			user_count: {
				title: '<?php echo FRANCHISE_COUPON_USER_COUNT; ?>'
			},
			pdf: {
				title: '<?php echo FRANCHISE_COUPON_PDF; ?>',
				display: function(data) {
					var html = '<a href="' + data.record.pdf + '" target="_blank">'
						+ '<button class="jtable-command-button ui-icon ui-icon-contact" type="button">'
						+ '<?php echo SEE; ?>'
						+ '</button>'
						+ '</a>';
					return html;
				}
			},
			edit: {
				title: '<?php echo EDIT; ?>',
				display: function(data) {
					var html = '<button type="button" title="Editar registro" '
						+ 'class="jtable-command-button jtable-edit-command-button" '
						+ 'onclick="showEditCouponForm(' + data.record.id + ');">'
						+ '<span>Editar registro</span>'
						+ '</button>';
					return html;
				}
			},
			preview: {
				title: '<?php echo PREVIEW; ?>',
				display: function(data) {
					var html = '<button type="button" title="Previsualizar registro" '
						+ 'class="jtable-command-button ui-icon ui-icon-copy" '
						+ 'onclick="showPreviewCouponForm(' + data.record.id + ');">'
						+ '<span>Previsualizar registro</span>'
						+ '</button>';
					return html;
				}
			}
		},
		toolbar: {
		    items: [{
		        icon: '/img/icon-xls.gif',
		        text: '<?php echo EXPORT; ?>',
		        click: exportCouponList
		    }]
		}
	};

	

	


	function exportCouponList() {
		window.open('/Coupon/exportXls?restaurant_id=' + $('.restaurantId').val());
	}
	

	function fetchTableCoupon(id) {
		var data = $('#tableCoupons').jtable('getRowByKey', id).children();
		var coupon = {
			id: id,
			name: $(data[0]).html(),
			caption: $(data[1]).html(),
			image: $(data[2]).find('img').attr('src'),
			start: $(data[3]).html(),
			end: $(data[4]).html(),
			pdf: $(data[5]).children().attr('href')
		};

		return coupon;
	}


	function showEditCouponForm(id) {
		var coupon = fetchTableCoupon(id);
		var startDateArr = coupon.start.split(/[- :]/);
		var startDate = new Date(startDateArr[0],
			startDateArr[1]-1, 
			startDateArr[2],
			startDateArr[3],
			startDateArr[4],
			startDateArr[5]);

		var endDateArr = coupon.end.split(/[- :]/);
		var endDate = new Date(endDateArr[0],
			endDateArr[1]-1, 
			endDateArr[2],
			endDateArr[3],
			endDateArr[4],
			endDateArr[5]);

		$('#filePdf').val('');
		$('#edit_coupon_id').val(coupon.id);
		$('#name_edit').val(coupon.name);
		$('#caption_edit').val(coupon.caption);
		$('#couponImageThumb_edit').attr('src', coupon.image);
		$('#image_edit').val(coupon.image);
		$('#start_edit').datepicker('setDate', startDate);
		$('#end_edit').datepicker('setDate', endDate);
		$('#pdf_edit').val(coupon.pdf);
		$('#pdf_edit, #pdf_edit_hide').val(coupon.pdf);

		$('#editCoupondialog').dialog({
			modal: true,
			title: '<?php echo COUPON_EDIT_FORM; ?>',
			width: 360
		});
	}

	function franchiseFormSubmit(event) {
		$('#franchiseFormBlocker, #franchiseFormPreloader').show();
		$('#couponPdfUploaderTarget').off('load');
		$('#couponPdfUploaderTarget').load(function() {
			try {
				var json = $('#couponPdfUploaderTarget').contents()
					.find('body').html();
				var object = $.parseJSON(json);
				if(!object.status) {
					throw object.errors[0];
				} 
				
				$('#pdf_hide').val(object.response.path);
				$.ajax({
					url: '/api/Coupon/addCoupon',
					type: 'post',
					data: $('#franchiseForm').serialize(),
					success: function(response) {
						var result = response;
						if(typeof response != 'object') {
							try {
								result = $.parseJSON($.trim(response));
							} catch(e) {
								console.log(e);	
							}
						}
						if(result.result) {
							$('#tableCoupons').jtable('reload');	
						} else {
							showErrorDialog(result.message);
						}

						$('#name, #caption, #start, #end, #pdf').val('');
						$('#couponImageThumb').attr('src', '/img/logo-placeholder-120.gif');
						$('#franchiseFormBlocker, #franchiseFormPreloader').hide();
					}
				});
			} catch(e) {
				$('#franchiseFormBlocker, #franchiseFormPreloader').hide();
				showErrorDialog(e);
			}
		});
		$('#couponPdfUploaderForm').submit();
		event.preventDefault();
		return false;
	}
	

	function couponEditAjax() {
		$.ajax({
			url: '/api/Coupon/edit',
			type: 'post',
			data: $('#couponEditForm').serialize(),
			success: function(response) {
				try {
					var result = $.parseJSON(response);
					if(result.result) {
						$('#tableCoupons').jtable('reload');
					} else {
						showErrorDialog(result.message);
					}
				} catch(e) {
					console.log(e);
				}
				$('#franchiseFormBlocker, #franchiseFormPreloader').hide();
			}
		});
	}

	function couponEditFromSubmit(event) {
		$('#editCoupondialog').dialog('close');
		$('#franchiseFormBlocker, #franchiseFormPreloader').show();
		$('#couponPdfUploaderTarget').off('load');
		$('#couponPdfUploaderTarget').load(function() {
			try {
				var json = $('#couponPdfUploaderTarget').contents()
					.find('body').html();
				var object = $.parseJSON(json);
				if(!object.status) {
					throw object.errors[0];
				} 
				$('#pdf_edit_hide').val(object.response.path);
				couponEditAjax();
			} catch(e) {
				$('#franchiseFormBlocker, #franchiseFormPreloader').hide();
				showErrorDialog(e);
			}
		});
		if($.trim($('#filePdf').val()) != '') {
			$('#couponPdfUploaderForm').submit();
		} else {
			couponEditAjax();
		}
		event.preventDefault();
		return false;
	}
	


	function showErrorDialog(message) {
		$('#formFranchiseResult').removeClass('ui-state-highlight ui-state-error');
		$('#formFranchiseResult').addClass('ui-state-error');
		$('#formFranchiseResult p b').html(message);
		$('#formFranchiseResult').dialog({
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

	function uploadCouponImage(event) {
		var oldImage = $(this).attr('src');
		var that = $(this);
		var uploaderId = event.data.uploader;
		var inputId = event.data.input;

		//$(this).attr('src', '');
		//$(this).addClass('couponImagePreloader');                
                
		if(typeof event.data.check_coupon_id != 'undefined' 
			&& event.data.check_coupon_id == true) {
			var dirName = 'Coupon/' + $('#edit_coupon_id').val();
			$('#' + uploaderId + ' input[name="fileDirname"]').val(dirName);
		}

		$('#' + uploaderId + ' input[type="file"]').click();
                
		$('#' + uploaderId + ' iframe').load(function(event){
			try {
				var json = $('#' + uploaderId + ' iframe').contents()
					.find('body').html();
				var object = $.parseJSON(json);
				if(!object.status) {
					$(that).attr('src', oldImage);
					throw object.errors[0];
				} else {
					$(that).attr('src', object.response.path);
				}
				$('#' + inputId).val(object.response.path);
			} catch(e) {
				$(that).attr('src', oldImage);
				showErrorDialog(e);
			}
                        $("#load-coupon-button").button({ label: "<?php echo UPLOAD_IMAGE; ?>" });
                        $("#load-coupon-button").button( "option", "disabled", false); 
		});
	}

	

	function checkPdfFileType(event) {
		var selectedVal = $(this).val();
		if(!/\.pdf$/.test(selectedVal.toLowerCase())) {
			$(this).val('');
			$('#pdf').val('');
			showErrorDialog("<?php echo COUPON_PDF_FILE_TYPE_ERROR; ?>");
		} else {
			var showVal = selectedVal.replace(/^.[^\\]*\\.[^\\]*\\/, '');
			var dialogOpen = false;
			try {
				dialogOpen = $('#editCoupondialog').dialog('isOpen');
			} catch(e) {
				console.log(e);
			}
			if(dialogOpen) {
				$('#pdf_edit').val(showVal);
			} else {
				$('#pdf').val(showVal);
			}
			
		}
	}
	

	function selectPdfFile(event) {
		$('#filePdf').click();
	}

	function showPreviewCouponForm(id) {
		var coupon = fetchTableCoupon(id);
		$('#couponPreviewDialog img').attr('src', '');
		$('#couponPreviewDialog .txt > h2').html('');
		$('#couponPreviewDialog .txt > .couponTitle').html('');
		$('#couponPreviewDialog .txt > .couponTxt').html('');

		$('#couponPreviewDialog img').attr('src', coupon.image);
		$('#couponPreviewDialog .txt > h2').html($('#info > b').html());
		$('#couponPreviewDialog .txt > .couponTitle').html(coupon.name);
		$('#couponPreviewDialog .txt > .couponTxt').html(coupon.caption);

		$('#couponPreviewDialog').dialog({
			modal: true,
			draggable: false,
            resizable: false,
            dialogClass: 'ui-dialog-no-title-bar',
            width: 600
		});		
	}
	

</script>