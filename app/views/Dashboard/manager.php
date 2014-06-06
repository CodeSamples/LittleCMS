<?php

	include_once('includes/header-top-bar.php');	

	$salLocalElements = unserialize(SAL_LOCAL_ELEMENTS);
	$salAmbiences = unserialize(SAL_AMBIENCES);
	$salPrices = unserialize(SAL_PRICES);    
    $user = $_SESSION["userObject"]; 
    $user_role = $user->getDashboardRole();

?>
<div class="dashboardMainContainer">
	<div class="right restaurantSelectorInfo">
		<span id="info"><?php echo RESTAURANT_SELECTED; ?>
			<b><?php echo NONE; ?></b>
		</span>
		&nbsp;&nbsp;
		<span>
			<a id="restaurantSelectorButton" href="#">
				<?php echo RESTAURANT_SELECT; ?>
			</a>
		</span>
                <?php if($user_role==ROLE_ADMIN){ ?>    
                <span>
                        <a id="restaurantCreateButton" href="#">
				<?php echo RESTAURANT_ADD; ?>
			</a>
                </span>  
                <?php } ?> 	
	</div>
	<div class="ui-state-highlight clear left none" id="pendingAdvice">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                        <span id="pendingAdviceMessage"></span>
		</p>
	</div>
	<div id="tabs" class="clear">
		<ul>
			<li><a href="#fragment-1"><span><?php echo RESTAURANT_DETAILS; ?></span></a></li>
			<li><a href="#fragment-2"><span><?php echo RESTAURANT_FRANCHISE; ?></span></a></li>
			<li><a href="#fragment-3"><span><?php echo RESTAURANT_OFFERS; ?></span></a></li>
			<li><a href="#fragment-4"><span><?php echo RESTAURANT_MEDIA; ?></span></a></li>
			<li><a href="#fragment-5"><span><?php echo RESTAURANT_COMMENTS; ?></span></a></li>
			<?php if($user_role == ROLE_ADMIN): ?>
			<li><a href="#fragment-6"><span><?php echo RESTAURANT_NOTIFICATIONS; ?></span></a></li>
			<?php endif; ?>
		</ul>
		<div id="fragment-1">
			<?php include_once('includes/restaurant-detail-form.php'); ?>
		</div>
		<div id="fragment-2">
			<?php include_once('includes/restaurant-franchise-form.php'); ?>
		</div>
		<div id="fragment-3">
			<?php include_once('includes/restaurant-offers-form.php'); ?>
	  	</div>
	  	<div id="fragment-4">
			<?php include_once('includes/restaurant-media.php'); ?>
	  	</div>
	  	<div id="fragment-5">
			<?php include_once('includes/restaurant-comments.php'); ?>
	  	</div>
	  	<?php if($user_role == ROLE_ADMIN): ?>
	  	<div id="fragment-6">
			<?php include_once('includes/restaurant-notifications.php'); ?>
	  	</div>
	  	<?php endif; ?>
	</div>
</div>
<div id="restaurantSelector" class="none">
	<div class="preloader"></div>
        <div class="assignmentContainer">
        <div class="filter none" style="margin: 0.5em 1em 0.5em 0.2em;">
            <h2><?php echo RESTAURANT_ASSIGNMENT_FILTER; ?></h2>
            <input id="restaurantFilter" type="text" />
        </div>  
        </div>
	<ol id="selectable"></ol>          
</div>
<?php if($user_role == ROLE_ADMIN): ?>
<div id="restaurantAdd" class="none">
	<div class="preloader none"></div>
    <div id="resultAdd" class="none"></div>
    <div id="restaurantID">
        <label>ID del Restaurant en Sal! Web:</label>
        <input type="text" name="id_restaurant" id="salweb_id" value="" class="smalltext" />
    </div>
</div>
<?php endif; ?>
<script type="text/javascript">

	function openRestaurantSelectorDialog(event) {
		$.ajax({
			url:'/api/Restaurant/listAll',
			type:'get',
			success: function(data) {
				try {
					var restaurantList = $.parseJSON(data);
					if(restaurantList.length == 1) {
						restaurantSelected(restaurantList[0].id);
						$('#restaurantSelectorButton').button('disable');
					} else {
						$('#restaurantSelector').dialog({
							dialogClass: "no-close",
							title: "<?php echo RESTAURANT_SELECT; ?>",
							modal: true,
							maxHeight: 480,
							buttons: [
								{
									text: "<?php echo CANCEL; ?>",
									click: function() {
										$(this).dialog('close');
									}
								}
							],
							create: function(event, ui) {
								var listHtml = '';
								for(var i = 0; i < restaurantList.length; i++) {
									listHtml += '<li class="ui-widget-content" data-id="'
										+ restaurantList[i].id + '">'
										+ '<a>' + restaurantList[i].name + '</a>'
										+ '</li>';
								}
								$('#selectable').html(listHtml);
								$('#selectable').selectable();
								$('#restaurantSelector .preloader').hide();
								$('#restaurantSelector .filter').fadeIn();
								$('#selectable').slideDown(400);	
							}
						});
					}
				} catch (e) {
					console.log(e);
				}
			}
		});

		
	}
   	
   	<?php if($user_role == ROLE_ADMIN): ?>
    function openRestaurantAddDialog(event) {
		$('#restaurantAdd').dialog({
			dialogClass: "no-close",
			title: "<?php echo RESTAURANT_ADD; ?>",
			modal: true,
			maxHeight: 480,
			buttons: [
				{
					text: "<?php echo CANCEL; ?>",
					click: function() {
						$(this).dialog('close');
					}
				},
				{
					id: "button-add-restaurant",
					text: "<?php echo SELECT; ?>",
					click: function() {
						$("#button-add-restaurant").attr("disabled", true);
	                    $("#restaurantID").hide();
	                    $("#restaurantAdd .preloader").fadeIn();
	                    $.ajax({
	                        url:'/json/Restaurant/importSalWeb?sal_id='+$("#salweb_id").val(),
	                        type:'get',
	                        success: function(data) {
	                            $("#restaurantAdd .preloader").fadeOut();
	                            if(data.response){
	                                $("#resultAdd").messageManager(data);
	                                setTimeout(function(){
	                                    restaurantSelected(data.response.id);
	                                    $('#restaurantAdd').dialog('close');
	                                },3000);
	                            } else {
	                                $("#resultAdd").messageManager(data);   
	                                $("#button-add-restaurant").attr("disabled", false);
	                                $("#restaurantID").fadeIn();                                                            
	                            }
	                            $("#resultAdd").fadeIn();
	                        }
	                    });
					}
				}
			],
	        open: function( event, ui ) {
	            $("#salweb_id").val('');
	            $("#restaurantID").show();
	            $("#resultAdd").hide();
	            $("#restaurantAdd .preloader").hide();
	            $("#button-add-restaurant").attr("disabled", false);
	        },
			create: function(event, ui) {
				$("#salweb_id").val('');
			}
		});
	}
    <?php endif; ?>  

    function filterRestaurantList(event) {
        var list = $('#selectable li');
        var matchText = $(this).val().toLowerCase();
        for(var i = 0; i < list.length; i++) {
            var itemText = $(list[i]).find('a').get(0);
            itemText=$(itemText).html().toLowerCase();
            if(itemText.indexOf(matchText) == -1) {
              $(list[i]).hide();
            } else {
              $(list[i]).show();
            }
        }
    }

    function getRestaurantComments(id) {
    	$.ajax({
			url: '/json/Comment/getComments',
			data: {
				restaurant_id: id
			},
			type: 'get',
			success: function(response) {
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						console.log(e);
					}	
				}
				populateRestaurantComments(response.response);
			}
		});
		$.ajax({
			url: '/json/Comment/getRatings',
			data: {
				restaurant_id: id
			},
			type: 'get',
			success: function(response) {
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						console.log(e);
					}	
				}
				populateRestaurantRatings(response.response);
			}
		});
    }

    function getRestaurantGallery(gallery_id, maxPics, maxVideos) {
    	$.ajax({
			url: '/json/Gallery/getGallery',
			data: {
				gallery_id: gallery_id
			},
			type: 'get',
			success: function(response) {
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						console.log(e);
					}	
				}
				if(response.response != false) {
					populateRestaurantMedia(response.response, maxPics, maxVideos);
				} else {                                       
                                        imageLimit = maxPics;                                     
                                        videoLimit = maxVideos; 
                                      
					$('#mediaFormBlocker, #mediaFormPreloader').hide();
				}
			}
		});
    }

    function getRestaurantDetail(id, features, isChild) {
    	$.ajax({
			url:'/api/Restaurant/detail',
			type:'get',
			data: {
				'restaurant_id':id
			},
			success: function(response) {
				try {
					var restaurant = response;
					$('#name').html(restaurant.name);
					$('#address').html(restaurant.address);
					$('#lat').html(restaurant.lat);
					$('#lng').html(restaurant.lng);
					$('#city').html(restaurant.city);
					$('#food').html(restaurant.food);
					$('#tags').html(restaurant.tags);
					$('#keywords').html(restaurant.keywords);
                                        $('#load-logo-button').button({
                                            icons: { 
                                                primary:"ui-icon-image"
                                            }
                                        });
					if($.trim(restaurant.logo) != '') {
						$('#logoThumb').attr('src', restaurant.logo);
						$('#logo').val(restaurant.logo);
					} else {
						$('#logoThumb').attr('src', '/img/logo-placeholder-120.gif');
						$('#logo').val('');
					}
					$('#content').val(restaurant.content);
					$('#excerpt').val(restaurant.excerpt);
					for(var i = 0; i < restaurant.elements.length; i++) {
						$('#elements_' + restaurant.elements[i]).attr('checked', 'checked');
					}
					for(var i = 0; i < restaurant.ambience.length; i++) {
						$('#ambience_' + restaurant.ambience[i]).attr('checked', 'checked');
					}
					if(typeof restaurant.price != 'number') {
						restaurant.price = restaurant.price.length;
					}
					$('#price_' + restaurant.price).attr('checked', 'checked');
					$('#time').val(restaurant.time);
					$('#phone').val(restaurant.phone);
					$('#mail').val(restaurant.mail);
					$('#menu_locu').val(restaurant.menu_locu);
					$('#menu_pdf, #pdf_detail').val(restaurant.menu_pdf);
					$('#facebook').val(restaurant.facebook);
					$('#twitter').val(restaurant.twitter);
					$('#galleryId').val(restaurant.gallery_id);
					$('#videosIds').val(restaurant.videos);
					$('#info > b').html(restaurant.name);
					$('.restaurantId').val(restaurant.id);
					$('.salId').val(restaurant.id_sal);

					$('form.none').show();
					$('#detailFormBlocker,  #detailFormPreloader').hide();


					if(restaurant.pending == "1") {
						$('#pendingAdviceMessage').html('<?php echo PENDING_MANAGER_MESSAGE; ?>');
						$('#pendingAdvice').show();
					} else {
                                                $('#pendingAdviceMessage').html('');
                                                $('#pendingAdvice').hide();
                                        }

					$('#imagesContainer, #videosContainer').html('');
					$('#commentsContainer > .comment').remove();
					$('#commentsContainer .preloader').show();
					$('#ratingsContainer .star').remove();
					$('#ratingsContainer .txtInfo').html('');
					$('#ratingsContainer .preloaderSmall').show();

					$("#tabs").tabs("enable");
					$('#tabs ul li a').parent().show();

					$('#detailsForm input, #detailsForm textarea').attr('disabled', 'disabled');
					$('#detailsForm').off('submit');
					$('#detailsForm input[type="submit"]').button("option", "disabled", true);
					$('#detailPdf').off('change');
					$('#pdf_detail, #logoThumb, #load-logo-button').off('click');

					
					var maxPics = 0;
					var maxVideos = 0;
					if(typeof features.pics != 'undefined') {
						maxPics = parseInt(features.pics);
					}					
					if(typeof features.videos != 'undefined') {
						maxVideos = parseInt(features.videos);
					}					
					getRestaurantGallery(restaurant.gallery_id, maxPics, maxVideos);

					if(typeof features.content_alter != 'undefined' && features.content_alter == '1') {
						$('#detailsForm input, #detailsForm textarea').removeAttr('disabled');
						$('#detailsForm').on(
							'submit',
							{ "franchise": restaurant.franchise },
							detailFormSubmit);
						$('#detailsForm input[type="submit"]').button("option", "disabled", false);
						$('#detailPdf').on('change', checkDetailPdfFileType);
						$('#pdf_detail').on('click', selectDetailPdfFile);
						if(typeof features.logo != 'undefined' && features.logo == '1') {
							$('#logoThumb').on(
								'click', 
								{ 
									uploader: "logoUploader", 
									input: "logo"
								},
								uploadLogo);
                            $('#load-logo-button').on(
								'click', 
								{ 
									uploader: "logoUploader", 
									input: "logo"
								},
								uploadLogo);
						} else {
                             $('#load-logo-button').hide(); 
                        }
					} else {
						$("#tabs").tabs("disable", 3);
						$('#tabs a[href="#fragment-4"]').parent().hide();
					}



					if(typeof features.social_alerts != 'undefined' && features.social_alerts == '1') {
						getRestaurantComments(id);	
						if(restaurant.franchise == '1') {
							showSocialChildSelector(restaurant.id, restaurant.id_sal);
						}
					} else {
						$("#tabs").tabs("disable", 4);
						$('#tabs a[href="#fragment-5"]').parent().hide();

						$("#tabs").tabs("disable", 5);
						$('#tabs a[href="#fragment-6"]').parent().hide();
					}

					var franchiseHideSelector = '#address,#lat,#lng,#city,#mail,#facebook,#twitter';

					if(restaurant.franchise == '0' && !isChild) {
						$('#restaurantDetailSelector').hide();
						$('#restDetailSelect').html('');
						$('#restaurantDetailSelector .preloader').show();
						$(franchiseHideSelector).parent().show().prev('br').show();
						$('input[name="elements[]"]').parent().parent().show();
						if(typeof features.reserve != 'undefined' && features.reserve == true) {
							getRestaurantOffers(id);
						} else {
							$("#tabs").tabs("disable", 2);
							$('#tabs a[href="#fragment-3"]').parent().hide();	
						}
						$("#tabs").tabs("disable", 1);
						$('#tabs a[href="#fragment-2"]').parent().hide();
					} else {
						if(!isChild) {
							showDetailChildSelector(restaurant.id, restaurant.id_sal);
						}
						$(franchiseHideSelector).parent().hide().prev('br').hide();
						$('input[name="elements[]"]').parent().parent().hide();
						$("#tabs").tabs("disable", 2);
						$('#tabs a[href="#fragment-3"]').parent().hide();

						if(typeof features.coupon != 'undefined' 
							&& features.coupon == '1'
							&& !isChild) {
							$('#tableCoupons').jtable('load', {
								restaurant_id: restaurant.id
							});
						} else {
							$("#tabs").tabs("disable", 1);
							$('#tabs a[href="#fragment-2"]').parent().hide();
						}
					}

					<?php if($user_role == ROLE_ADMIN): ?>
					getRestaurantNotifications(id);
					<?php endif; ?>

					
				} catch(e) {
					console.log(e);
				}
			}
		});
    }

    <?php if($user_role == ROLE_ADMIN): ?>
	function getRestaurantNotifications(id) {
		$.ajax({
			url: '/json/Restaurant/getNotifications',
			type: 'get',
			data: { 'restaurant_id': id },
			success: function(response) {
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						console.log(e);
					}
				}
				populateNotificationsForm(response.response);
			}
		})
	}
	<?php endif; ?>

    function getRestaurantOffers(id) {
    	$('#offer').val('');
		$('#offer_plus').jqteVal('');
		$('#datedOfferContainer').html('');
		$.ajax({
			url: '/json/Offer/getRestaurantOffer',
			type: 'get',
			data: {
				'restaurant_id':id
			},
			success: function(response) {
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						console.log(e);
					}
				}
				populateOfferForm(response.response);
			}
		});
    }

	function restaurantSelected(id, isChild) {
		$('.pageBlocker').show().next('.preloader').show();
		$.ajax({
			url: '/json/Restaurant/getRestaurantFeatures',
			data: { 'restaurant_id': id },
			success: function(response) {
				$('.ui-tabs-nav li a')[0].click();
				if(typeof response != 'object') {
					try {
						response = $.parseJSON($.trim(response));
					} catch(e) {
						
					}
				}
				var features = response.response;
				if(typeof features.reserve != 'undefined' && features.reserve == true) {
					$('#reserveLink').attr('href', '<?php echo SAL_RESERVA_URL; ?>')
						.removeClass('disabled');
				} else {
					$('#reserveLink').attr('href', '#').addClass('disabled');
				}
				getRestaurantDetail(id, features, isChild);
			}
		});


	}
        
        function selectRestList(event){           
            $('#restaurantSelector').dialog('close');
            var selectedId = $('#selectable .ui-selected').attr('data-id');
            restaurantSelected(selectedId);
        }

	$(document).ready(function() {
		$('#tabs').tabs();
		$('#restaurantSelectorButton').button({
			icons: { 
				primary:"ui-icon-newwin"
			}
		});
		$('#restaurantSelectorButton').on('click', openRestaurantSelectorDialog);
	        $('#restaurantCreateButton').button({
			icons: { 
				primary:"ui-icon-plus"
			}
		});
	    <?php if($user_role == ROLE_ADMIN): ?>
		$('#restaurantCreateButton').on('click', openRestaurantAddDialog);
		<?php endif; ?>
		$('form .deleteButton').button({
			icons: { 
				primary:"ui-icon-circle-close"
			}
		});
		$('form .addButton').button({
			icons: { 
				primary:"ui-icon-circle-plus"
			}
		});
		$('form input[type="submit"]').button();

		<?php if(isset($response->restaurant_id)): ?>
		restaurantSelected(<?php echo $response->restaurant_id; ?>);
		<?php else: ?>
		openRestaurantSelectorDialog();
		<?php endif; ?>

		$('#restaurantFilter').on('keyup', filterRestaurantList);
                
                $("#selectable").on( "selectableselected", function( event, ui ) {                                          
                    selectRestList();
                }); 
	});

</script>