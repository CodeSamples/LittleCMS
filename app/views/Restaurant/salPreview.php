<?php    
    
    if(!$response) {
        $errors = $obj_response->getErrors();
        print_r($errors[0]);
        die();
    }

    ob_end_clean();

    $baseDir = realpath(dirname(__FILE__) . '/salPreviewAssets');

    $headerImg = base64_encode(file_get_contents($baseDir.'/sal-header.jpg'));
    $footerImg = base64_encode(file_get_contents($baseDir.'/sal-footer.jpg'));
    $iconOffer = base64_encode(file_get_contents($baseDir.'/icon-offers.png'));
    $iconDetails = base64_encode(file_get_contents($baseDir.'/icon-details.png'));
    $iconComment = base64_encode(file_get_contents($baseDir.'/icon-comments.png'));
    $gigyaTopBar = base64_encode(file_get_contents($baseDir.'/gigya-top-bar.jpg'));
    $ratingStars = base64_encode(file_get_contents($baseDir.'/rating-stars.png'));
    $rateButton = base64_encode(file_get_contents($baseDir.'/button-rate.png'));
    $shadow = base64_encode(file_get_contents($baseDir.'/shadow.png'));
    $gigyaBottomBar = base64_encode(file_get_contents($baseDir.'/gigya-bottom-bar.jpg'));
    $sidebar = base64_encode(file_get_contents($baseDir.'/sidebar.jpg'));
    $iconMapTab = base64_encode(file_get_contents($baseDir.'/icon-tab-map.png'));
    $iconMenuTab = base64_encode(file_get_contents($baseDir.'/icon-tab-menu.png'));
    $iconElements = base64_encode(file_get_contents($baseDir.'/icon-elements.png'));
    $galleryJs = file_get_contents($baseDir.'/galleria-1.2.7.js');
    $classicLoader = base64_encode(file_get_contents($baseDir.'/classic-loader.gif'));
    $carouselArrL = base64_encode(file_get_contents($baseDir.'/carousel-arrows-l.png'));
    $carouselArrR = base64_encode(file_get_contents($baseDir.'/carousel-arrows-r.png'));
    $sliderArrL = base64_encode(file_get_contents($baseDir.'/slider-arrows-l.png'));
    $sliderArrR = base64_encode(file_get_contents($baseDir.'/slider-arrows-r.png'));
    $magnifyingGlass = base64_encode(file_get_contents($baseDir.'/magnifying-glass.png'));
    $menuCloser = base64_encode(file_get_contents($baseDir.'/menu-closer.png'));
    $buttonReserve = base64_encode(file_get_contents($baseDir.'/button-reserve.png'));
    $iconTag = base64_encode(file_get_contents($baseDir.'/tag.png'));
    $reserveHeader = base64_encode(file_get_contents($baseDir.'/reserva-header-bg.png'));
    $buttonFranchiseCoupon = base64_encode(file_get_contents($baseDir.'/button-franchise-coupon.png'));

    $stylesheet = file_get_contents($baseDir.'/stylesheet.css');
    $styleReplaceSearch = array(
        'ICON_ELEMENTS',
        'CLASSIC_LOADER',
        'CAROUSEL_ARR_L',
        'CAROUSEL_ARR_R',
        'SLIDER_ARR_L',
        'SLIDER_ARR_R',
        'MAGNIFYING_GLASS',
        'MENU_CLOSER',
        'RESERVE_HEADER',
        'FRANCHISE_COUPON'
        );
    $styleReplaceTarget = array(
        $iconElements,
        $classicLoader,
        $carouselArrL,
        $carouselArrR,
        $sliderArrL,
        $sliderArrR,
        $magnifyingGlass,
        $menuCloser,
        $reserveHeader,
        $buttonFranchiseCoupon
        );
    $stylesheet = str_replace($styleReplaceSearch, $styleReplaceTarget, $stylesheet);
    
    $salAmbiences = unserialize(SAL_AMBIENCES);
    $ambience = array();
    $ambience_changed = false;
    foreach ($response['detail']->ambience as $key) {
        if(isset($salAmbiences[$key])) {
            $ambience[] = $salAmbiences[$key];
            if(!in_array($key, $response['detail_sal']->ambience)){
                $ambience_changed = true;
            } 
        }
    }
    if(!$ambience_changed){
        foreach ($response['detail_sal']->ambience as $key) {
        if(isset($salAmbiences[$key])) {
            $ambience[] = $salAmbiences[$key];
            if(!in_array($key, $response['detail']->ambience)){
                $ambience_changed = true;
            } 
        }
        }
    }    
    
    $salElements = unserialize(SAL_LOCAL_ELEMENTS);
    $elements = array();
    $elements_changed = false;
    foreach ($response['detail']->elements as $key) {
        if(isset($salElements[$key])) {
            $elements[$key] = $salElements[$key];
            if(!in_array($key, $response['detail_sal']->elements)){
                $elements_changed = true;
            } 
        }
    }
    if(!$elements_changed){
        foreach ($response['detail_sal']->elements as $key) {
        if(isset($salElements[$key])) {
            $elements[$key] = $salElements[$key];
            if(!in_array($key, $response['detail']->elements)){
                $elements_changed = true;
            } 
        }
        }
    }

    $salPrices = unserialize(SAL_PRICES);
    $price = (isset($salPrices[$response['detail']->price])) ? 
        $salPrices[$response['detail']->price] : null;
    
    $price_sal = (isset($salPrices[$response['detail_sal']->price])) ? 
        $salPrices[$response['detail_sal']->price] : null;

    $price_changed = ($price != $price_sal);
    
    $priceLabels = array(
        'Menos de $30',
        'De $30 a $50',
        'De $50 a $80',
        'Mayor de $80'
        );

    $hasMenu = false;
    if(isset($response['detail']->menu_pdf) && trim($response['detail']->menu_pdf) != '') {
        $hasMenu = true;
    }
    if(isset($response['detail']->menu_locu) && trim($response['detail']->menu_locu) != '') {
        $hasMenu = true;
    }

    $images = array();
    $videos = array();
    $media_changed = false;
    if(isset($response['gallery']->media_list) && is_array($response['gallery']->media_list)) {        
        foreach ($response['gallery']->media_list as $media) {            
            $arr = 'images';
            if(preg_match('/video/', $media->type)) {
                $arr = 'videos';
            } 
            ${$arr}[$media->getExternal_id()] = $media->getFilename();
            $media_exists = false;
            if(!$media_changed && is_array($response['gallery_sal']->media_list)){
                foreach ($response['gallery_sal']->media_list as $media_sal) {
                    if($media->getFilename()==$media_sal->getFilename()){
                        $media_exists = true;
                        break;
                    }
                }
            }
            $media_changed = $media_changed || !$media_exists;
        }
    }
    if(!$media_changed && is_array($response['gallery_sal']->media_list)){
        foreach ($response['gallery_sal']->media_list as $media_sal) {            
            $media_exists = false;
            if(!$media_changed){
                foreach ($response['gallery']->media_list as $media) {
                    if($media->getFilename()==$media_sal->getFilename()){
                        $media_exists = true;
                        break;
                    }
                }
            }
            $media_changed = $media_changed || !$media_exists;
        }
    }
    
    $offer_changed = false;
    if($response['detail']->franchise == '0') {
        $offer = $response['offer']->getOffer();
        $offer_changed = ($offer !== $response['offer_sal']->getOffer());
        if(null !== $response['offer']->getOffer_plus() 
            && trim($response['offer']->getOffer_plus()) != '') {
            $offer = $response['offer']->getOffer_plus();
            $offer_changed = ($offer !== $response['offer_sal']->getOffer_plus());
        }
        if(sizeof($response['offer']->getOffer_dated()) > 0) {
            $tz_object = new DateTimeZone('America/Puerto_Rico');
            $datetime = new DateTime();
            $datetime->setTimezone($tz_object);

            foreach ($response['offer']->getOffer_dated() as $key => $value) {
                if( strtotime($value[0]) <= intval($datetime->format('U')) && strtotime($value[1]) >= intval($datetime->format('U')) ) {
                    $offer = $value[2];
                    break;
                }
            }
            
            foreach ($response['offer_sal']->getOffer_dated() as $key => $value) {
                if( strtotime($value[0]) <= intval($datetime->format('U')) && strtotime($value[1]) >= intval($datetime->format('U')) ) {
                    $offer_sal = $value[2];
                    break;
                }
            }
            
            $offer_changed = ($offer !== $offer_sal);
        }    
    }
    
    $description_changed = (trim($response['detail']->content)!=trim($response['detail_sal']->content));
    $time_changed = (trim($response['detail']->time)!=trim($response['detail_sal']->time));
    $email_changed = (trim($response['detail']->mail)!=trim($response['detail_sal']->mail));
    $phone_changed = (trim($response['detail']->phone)!=trim($response['detail_sal']->phone));    

    if($response['detail']->franchise == '0') {
        $salphone_changed = (trim($response['offer']->getSal_phone())!=trim($response['offer_sal']->getSal_phone()));    
    } else {
        $salphone_changed = false;
    }
    
    $pdf_changed = (trim($response['detail']->menu_pdf)!=trim($response['detail_sal']->menu_pdf));   
    $locu_changed = (trim($response['detail']->menu_locu)!=trim($response['detail_sal']->menu_locu));   

    header('Content-Type: text/html; charset=utf-8');    

?>
<!doctype html>
<html>
    <head>
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
        <style type="text/css"><?php echo $stylesheet; ?></style>
        <link rel="stylesheet" href="http://d37uu5vx6wkhnq.cloudfront.net/wp-content/themes/GeoPlaces/js/galleria.classic.css" id="galleria-theme">
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript"><?php echo $galleryJs; ?></script>
        <script type="text/javascript" src="http://d37uu5vx6wkhnq.cloudfront.net/wp-content/themes/GeoPlaces/js/galleria.classic.js"></script>
        <script type="text/javascript">

            var map, mapOptions, mapMarker;
            var GalleriaObject;

            function switchTab(event) {
                var tabindex = $(this).attr('tabindex');
                $('.tabs > div').removeClass('active');
                $(this).addClass('active');
                if($(this).attr('id') != 'menuTab' || $('#menuLocu').length == 1) {
                    $('.tabsTargets > div').hide();
                    $($('.tabsTargets > div')[tabindex]).show();
                }
                if(typeof map != 'undefined') {
                    google.maps.event.trigger(map,"resize");
                    map.setCenter(mapOptions.center);
                }
            }

            function initializeMap() {
                mapOptions = {
                    center: new google.maps.LatLng(
                        <?php echo $response['detail']->lat; ?>,
                        <?php echo $response['detail']->lng; ?>
                    ),
                    zoom: 15,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                };
                map = new google.maps.Map(
                    document.getElementById("mapCanvas"),
                    mapOptions
                    );
                mapMarker = new google.maps.Marker({
                    position: mapOptions.center, 
                    map: map,
                    draggable: false
                });
            }
            google.maps.event.addDomListener(window, 'load', initializeMap);

            function addMagnifyingGlass() {
                var glassCode = '<div id="magnifyingGlass"></div>';
                $(glassCode).appendTo('.galleria-stage');
                $('.galleria-stage').bind('mouseenter', showMagnifyingGlass);
                $('.galleria-stage').bind('mouseleave', hideMagnifyingGlass);
                $('#magnifyingGlass').bind('click', openGalleriaImage);
            }

            function openGalleriaImage(event) {
                $(GalleriaObject.getActiveImage()).click();
            }

            function showMagnifyingGlass(event) {
                var childItem;
                childItem = $('.galleria-stage .galleria-image').has('iframe');
                if(childItem.length < 1) {
                    $('#magnifyingGlass').fadeIn(250);
                } 
            }

            function hideMagnifyingGlass(event) {
                $('#magnifyingGlass').fadeOut(250);
            }

            function startGalleria() {
                $('#galleria').galleria({
                    image_crop: true, // crop all images to fit
                    thumb_crop: true, // crop all thumbnails to fit
                    transition: 'fade', // crossfade photos
                    transition_speed: 700, // slow down the crossfade
                    autoplay: false, 
                    data_config: function(img) {
                        return {
                            title: $(img).next('strong').html(),
                            description: $(img).next('strong').html()
                        };
                    },
                    extend: function() {
                        this.bind(Galleria.IMAGE, function(e) {
                            $(e.imageTarget).css('cursor','pointer')
                                .click(this.proxy(function() {
                                    this.openLightbox();
                                }));   
                        });
                    }
                });

                Galleria.ready(function(){
                    GalleriaObject = this;
                    $('#galleria img').show();
                    var theDiv = '<div class="galleriaHeader">Fotos y V&iacute;deos</div>';
                    $(theDiv).insertBefore('.galleria-thumbnails-container');
                    addMagnifyingGlass();
                });   
            }

            <?php if($hasMenu): ?>
            function showPlaceMenu(event) {
                $('.tabs').removeClass('active');
                $(event.currentTarget).addClass('active');

                if($('#menuLocu').length) {
                  $('.nota-elements-containers').hide();
                  $('#menuLocu').show();
                } else {
                  window.scrollTo(0, 0);
                  $('body').addClass('noscroll');
                  $('#menuBackground').show(0, function() {
                      $('#singlePlaceMenu').fadeIn(350);
                  });
                }
            }

            function closePlaceMenu(event) {
                $('#singlePlaceMenu, #menuBackground').hide();
                $('body').removeClass('noscroll');
            }
            <?php endif; ?>
            
            $(document).ready(function() {
                $('.tabs > div').on('click', switchTab);
                startGalleria();
                <?php if($hasMenu): ?>
                $('#menuTab').on('click', showPlaceMenu);
                $('#singlePlaceMenu #close').on('click', closePlaceMenu);
                <?php endif; ?>
            });
            
        </script>
        <style type="text/css">
            .changed{
                border: 1px dotted #FF0000;                
                background-color: #FFF0EF;
            }
            .pricechanged{
                clear: both;
            }
            #changed_note{
                padding: 10px;
                margin: 10px auto 10px auto;
                width: 600px;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <img src="data:image/jpg;base64,<?php echo $headerImg; ?>" alt="" />
        </div>
        <div id="content">
            <div class="breadcrumbs">
                <ul>
                    <li>sal.pr&nbsp;&raquo;&nbsp;</li>
                    <li><?php echo $response['detail']->food; ?>&nbsp;&raquo;&nbsp;</li>
                    <li><?php echo $response['detail']->name; ?></li>
                    <li class="last">
                        Recibe ofertas y promociones
                        <img src="data:image/png;base64,<?php echo $iconOffer; ?>" alt="" />
                    </li>
                </ul>
                <br />
            </div>
            <?php 
               if($elements_changed || $ambience_changed || $price_changed || $time_changed 
                  || $phone_changed || $email_changed || $media_changed || $offer_changed
                  || $salphone_changed || $pdf_changed || $locu_changed){
                   ?>
            <div id="changed_note" class="changed">
                <p>Hay diferencias con respecto a la información publicada en Sal! Web, los cambios están marcados con línea punteada roja como este mensaje.</p>                
            </div>
                   <?php
               }
            ?>
            <h1 class="title"><?php echo $response['detail']->name; ?></h1>
            <div class="socialTop">
                <img class="icon" src="data:image/png;base64,<?php echo $iconComment; ?>" alt="" />
                <b>Comentarios</b>
                <br />
                <img class="bar" src="data:image/jpg;base64,<?php echo $gigyaTopBar; ?>" alt="" />
            </div>
            <div class="leftContent">
                <div class="tabs">
                    <div tabindex="0" class="active">Informaci&oacute;n</div>
                    <div tabindex="1">
                        <img src="data:image/png;base64,<?php echo $iconMapTab; ?>" alt="" />
                        Ver Mapa
                    </div>
                    <?php if($hasMenu): ?>
                    <div id="menuTab" tabindex="2" class="<?php echo ($pdf_changed || $locu_changed) ? 'changed' : ''; ?>">
                        <img src="data:image/png;base64,<?php echo $iconMenuTab; ?>" alt="" />
                        Ver Men&uacute;
                    </div>
                    <?php endif; ?>
                </div>
                <div class="tabsTargets">
                    <div>
                        <div class="sideLeft">
                            <ul class="details">
                                <li class="label">PUNTUACI&Oacute;N TOTAL</li>
                                <li><img src="data:image/png;base64,<?php echo $ratingStars; ?>" alt="" /></li>
                                <li class="small">0 / 5 - 0 RESE&Ntilde;AS</li>
                                <li>Ambiente:</li>
                                <li><img src="data:image/png;base64,<?php echo $ratingStars; ?>" alt="" /></li>
                                <li>Servicio</li>
                                <li><img src="data:image/png;base64,<?php echo $ratingStars; ?>" alt="" /></li>
                                <li>Comida</li>
                                <li><img src="data:image/png;base64,<?php echo $ratingStars; ?>" alt="" /></li>
                                <li class="button"><img src="data:image/png;base64,<?php echo $rateButton; ?>" alt="" /></li>
                            </ul>
                            <ul class="details">
                                <li class="label">Comida:</li>
                                <li><?php echo $response['detail']->food; ?></li>
                                <?php if(sizeof($ambience) > 0): ?>
                                <li class="label">Ambiente:</li>
                                <li class="<?php echo $ambience_changed ? 'changed' : ''; ?>"><?php echo implode(', ', $ambience); ?></li>
                                <?php endif; ?>
                                <?php if(trim($response['detail']->tags) != ''): ?>
                                <li class="label">Etiquetas:</li>
                                <li><?php echo $response['detail']->tags; ?></li>
                                <?php endif; ?>
                            </ul>
                            <?php if($response['detail']->franchise == '0'): ?>
                                <?php if(trim($response['detail']->time) != ''): ?>
                                <ul class="details">
                                    <li class="icon time" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Horario:</li>
                                    <li class="sub <?php echo $time_changed ? 'changed' : ''; ?>"><?php echo $response['detail']->time; ?></li>
                                </ul>
                                <?php endif; ?>
                                <?php if(trim($response['detail']->mail) != ''): ?>
                                <ul class="details">
                                    <li class="icon contact" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Email:</li>
                                    <a href="mailto:<?php echo $response['detail']->mail; ?>" target="_blank">
                                        <li class="sub <?php echo $email_changed ? 'changed' : ''; ?>"><?php echo $response['detail']->mail; ?></li>
                                    </a>
                                </ul>
                                <?php endif; ?>
                                <?php if(trim($response['detail']->phone) != ''): ?>
                                <ul class="details">
                                    <li class="icon phone" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Tel&eacute;fono:</li>
                                    <li class="sub <?php echo $phone_changed ? 'changed' : ''; ?>"><?php echo $response['detail']->phone; ?></li>
                                </ul>
                                <?php endif; ?>
                                <ul class="details">
                                    <li class="icon address" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Direcci&oacute;n f&iacute;sica:</li>
                                    <li class="sub"><?php echo $response['detail']->address; ?></li>
                                    <li class="icon town" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Pueblo:</li>
                                    <li class="sub"><?php echo $response['detail']->city; ?></li>
                                </ul>
                            <?php else: ?>
                                <ul class="details">
                                    <select class="franchiseChildSelector">
                                        <option>Escoge localidad</option>
                                    </select>
                                    <h3 class="placeHolder">
                                        Escoga localidad (restaurante) para ver los detalles
                                    </h3>
                                </ul>
                            <?php endif; ?>
                            <ul class="details">
                                <li class="icon favourite" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                <li class="label">Agregar a favoritos</li>
                            </ul>
                            <ul class="details">
                                <a href="#" onclick="javascript:window.print();">
                                <li class="icon print" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                <li class="label">Imprimir</li>
                                </a>
                            </ul>
                            <ul class="details last">
                                <?php if($response['detail']->franchise == '0'): ?>
                                    <?php if(trim($response['detail']->twitter) != ''): ?>
                                    <a href="http://<?php echo $response['detail']->twitter; ?>" target="_blank">
                                    <li class="icon tw" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Twitter</li>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(trim($response['detail']->facebook) != ''): ?>
                                    <br />
                                    <a href="http://<?php echo $response['detail']->facebook; ?>" target="_blank">
                                        <li class="icon fb" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                        <li class="label">Facebook</li>
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(trim($response['detail']->mail) != ''): ?>
                                <br />
                                    <li class="icon contact" style="background-image: url(data:image/png;base64,<?php echo $iconDetails; ?>)"></li>
                                    <li class="label">Contactar</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="sideRight">
                            <div class="galleryContainer <?php echo $media_changed ? 'changed' : ''; ?>">
                                <div id="galleria">
                                    <?php

                                        if(sizeof($videos) > 0) {
                                            foreach ($videos as $key => $value) {
                                                echo '<a href="/ajax/Video/showVideo/?video_id=';
                                                echo $key . '">';
                                                echo '<img class="iframe" data-big="';
                                                echo '/Video/showVideo/?video_id=' . $key;
                                                echo '" src="' . $value . '" />';
                                                echo '</a>';
                                            }
                                        }

                                        if(sizeof($images) > 0) {
                                            foreach ($images as $value) {
                                                echo '<a href="' . $value . '">';
                                                echo '<img data-big="' . $value . '" ';
                                                echo ' src="' . $value . '" alt="" />';
                                                echo '</a>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>                            
                            <p class="<?php echo $description_changed ? 'changed' : ''; ?>"><?php echo $response['detail']->content; ?></p>
                            <?php if($response['detail']->franchise == '0'): ?>
                                <?php if(sizeof($elements) > 0 || isset($price)): ?>
                                <div class="elementsContainer">
                                    <h1>Elementos de su local</h1>
                                    <div class="<?php echo $elements_changed ? 'changed' : ''; ?>">
                                    <ul>
                                        <?php if(isset($price)): ?>
                                        <li class="price_<?php echo strlen($price); ?> <?php echo $price_changed ? 'changed pricechanged' : ''; ?>">                                           
                                            <span class="icon"></span>
                                            <?php echo $priceLabels[strlen($price)]; ?>                                           
                                        </li>
                                        <?php endif; ?>
                                        <?php foreach ($elements as $key => $value): ?>
                                        <li class="elements_<?php echo $key; ?>">
                                            <span class="icon"></span>
                                            <?php echo $value; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                        <div style="clear: both;"></div>
                                    </div>
                                    <?php if(isset($price)): ?>
                                    <br style="clear:both;" />
                                    <br style="clear:both;" />
                                    <p class="priceNote"><b>*</b> El promedio por costo es la suma de 1 aperitivo, 1 plato principal y 1 postre. Sin incluir bebidas.</p>
                                    <?php endif; ?>
                                    <p class="blue">&iquest;Es dueño de este restaurante&#63;</p>
                                    <p class="blue">&iquest;Cerró el restaurante&#63; &iexcl;Repórtalo&#33;</p>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="elementsContainer">
                                    <h1>Elementos de su local</h1>
                                    <div class="placeHolder">
                                    - -   Escoga localidad primero   - -
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="none">
                        <div id="mapContainer">
                            <div id="mapCanvas"></div>
                            <p><?php echo $response['detail']->address; ?></p>
                        </div>
                    </div>
                    <?php if($hasMenu): ?>
                    <div class="none">
                        <?php if(isset($response['detail']->menu_locu) && trim($response['detail']->menu_locu) != ''): ?>
                        <div id="menuLocu" class="nota-elements-containers">
                          <div class="menuContainer">
                            <div class="menuItself">
                              <div class="menuContent">
                                <br />
                                  <noscript>Debe tener activo javascript para poder visualizar el men&uacute; del restaurante</noscript>
                                  <script type="text/javascript" id="-locu-widget" src="https://widget.locu.com/menuwidget/locu.widget.developer.v2.0.js?venue-id=<?php echo $response['detail']->menu_locu; ?>&widget-key=9488952fec01bb86463baaa5370f2b33db77835b"></script>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="shadow">
                    <img src="data:image/jpg;base64,<?php echo $shadow; ?>">
                </div>
                <div class="gigyaBottomBar">
                    <img src="data:image/jpg;base64,<?php echo $gigyaBottomBar; ?>">
                </div>
            </div>
            <div class="rightContent">
                <?php if($response['detail']->franchise == '0' && $response['reserve'] === true): ?>
                <div id="reserva-online-wrap">
                    <h1 class="section-title">
                        <span></span>
                    </h1>
                    <div id="reserva-online-content">
                        <div class="porcentaje">
                            <p>
                                <img src="data:image/png;base64,<?php echo $iconTag; ?>" width="30" height="26">
                            </p>
                        </div>       
                        <span class="reserva-text <?php echo $offer_changed ? 'changed' : ''; ?>">
                            <span>
                                <?php echo html_entity_decode($offer, ENT_COMPAT | 'ENT_HTML401', 'UTF-8'); ?>
                            </span>
                        </span>        
                        <div class="under-reserva">
                            <p>RESERVA ONLINE</p>
                            <img src="data:image/png;base64,<?php echo $buttonReserve; ?>" alt="" />
                            <?php if(trim($response['offer']->getSal_phone()) != ''): ?>
                            <div class="separation">
                                <span class="left"></span>
                                <span class="center">&Oacute;</span>
                                <span class="right"></span>
                            </div>
                            <p class="reserva-tel <?php echo $salphone_changed ? 'changed' : ''; ?>">
                                Llama a <?php echo $response['offer']->getSal_phone(); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php elseif($response['detail']->franchise == '1' && null !== $response['offer']->getId()): ?>
                    <div class="franchiseCouponContainer">
                        <h1>Cupón exclusivo</h1>
                        <div class="couponItem">
                            <h2><?php echo $response['offer']->getName(); ?></h2> 
                            <p class="couponTxt"><?php echo $response['offer']->getCaption(); ?></p>
                            <div class="couponButton">
                                <p class="loginTxt">regístrate o haz logín para recibir tu cupón gratis:</p>
                                <div class="button" id="franchiseCouponButton">
                                    <a>CUPÓN GRATIS</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="franchiseCouponModal">
                        <h1>RECIBE TU CUP&Oacute;N GRATIS</h1>
                        <div class="couponContainer">
                            <div class="image">
                                <img src="<?php echo $response['offer']->getImage(); ?>" alt="" />
                            </div>
                            <div class="txt">
                                <h2><?php echo $response['detail']->name; ?></h2>
                                <p><?php echo $response['offer']->getName(); ?></p>
                                <p class="couponTxt"><?php echo $response['offer']->getCaption(); ?></p>
                            </div>
                            <div class="txtConditions">
                                <p>Las ofertas y los descuentos son ofrecidos por los restaurantes y solo éstos son responsables de honrar sus ofertas o descuentos.<br />Ofertas y descuentos no incluyen impuestos. GFR Media, LLC, no es reponsable por las ofertas y descuentos ofrecidos y anunciados por cada restaurante. Los restaurantes identificados en éste anuncio no están relacionados, afiliados, endosados, patrocinados o auspiciados por GFR Media, LLC.</p>
                            </div>
                        </div>
                        <br style="clear:both;" />
                        <div class="loginContainer">
                            <div class="button" id="franchiseCouponSubmit">
                                <a>RECIBIR CUP&Oacute;N</a>
                            </div>
                        </div>
                        <div class="blocker"></div>
                        <div class="preloader"></div>
                        <div id="result" class="response"></div>
                    </div>
                    <script type="text/javascript">
                        function showFranchiseCoupon(event) {
                            jQuery('#franchiseCouponModal').dialog({
                                modal: true,
                                draggable: false,
                                resizable: false,
                                dialogClass: 'ui-dialog-no-title-bar',
                                width: 600,
                                height: 'auto'
                            });
                        }
                        jQuery('#franchiseCouponButton').on('click', showFranchiseCoupon);
                    </script>
                <?php endif; ?>
                <div class="sidebar">
                    <img src="data:image/jpg;base64,<?php echo $sidebar; ?>">
                </div>
            </div>
        </div>
        <div id="footer">
            <img src="data:image/jpg;base64,<?php echo $footerImg; ?>" alt="" />
        </div>
        <?php if(isset($response['detail']->menu_pdf) && trim($response['detail']->menu_pdf) != ''): ?>
        <div id="menuBackground" class="pageBlocker"></div>
        <div id="singlePlaceMenu">
            <div class="menuContainer">
                <div class="menuItself">
                    <div class="menuHeader">
                        <div class="topFloater"></div>
                        <div class="menuTitle">Men&uacute;: <?php echo $response['detail']->name; ?></div>
                        <div id="close" class="menuCloser"></div>
                    </div>
                    <div class="menuContent">
                        <iframe id="menuPdfIframe" src="<?php echo $response['detail']->menu_pdf; ?>"></iframe>
                        <p>
                            <a href="<?php echo $response['detail']->menu_pdf; ?>" target="_blank">Descargar men&uacute;</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </body>
</html>
<?php exit(); ?>