<?php

/*
 * General
 */
define('DAHSBOARD', 'Dashboard');
define('SAL_RESERVA', 'Sal! Reserva');
define('SAL_LISTINGS', 'Sal! Listings');
define('ADMIN_AREA', 'Sistema');
define('MANAGER_AREA', 'Restaurantes');
define('STATS_AREA', 'Reportes');
define('DELETE', 'Eliminar');
define('SAVE', 'Guardar');
define('NONE', 'Ninguno');
define('SELECT', 'Seleccionar');
define('CANCEL', 'Cancelar');
define('CREATE', 'Crear');
define('SEE', 'Ver');
define('NAME', 'Nombre');
define('OK', 'Ok');
define('ADD', 'Agregar');
define('EDIT', 'Editar');
define('ID', 'ID');
define('NOW', 'Ahora');
define('ALL', 'Todos');
define('EXPORT', 'Exportar');
define('PREVIEW', 'Previsualizar');
define('APPROVE', 'Aprobar');
define('REJECT', 'Rechazar');
define('ACTIONS', 'Acciones');
define('ENABLED', 'Activado');
define('DISABLED', 'Desactivado');
define('MISSING_FIELDS', 'Falta o está incompleto alguno de los parámetros requeridos, por favor intente nuevamente.');
define('UPLOAD_LOGO', 'Cargar Logo');
define('UPLOAD_IMAGE', 'Cargar Imagen');
define('UPLOADING', 'Cargando...');
define('SAVE_NOTICE','Una vez completado los cambios deberá presionar en el botón Guardar para que los mismos sean efectivos.');
define('FRANCHISE', 'Franquicia');
define('UPLOAD_PDF', 'Cargar PDF');


define('MAIL', 'Email');
define('PASSWORD', 'Password');
define('USERNAME', 'Username');
define('ADMIN', 'Administrador');
define('MANAGER', 'Manager');
define('ROLE', 'Rol');

/*
 * Filters
 */
define('BY_PACKAGE','Por Paquete');
define('BY_FOOD_TYPE','Por Tipo de Comida');

/*
 * Validations
 */
define('REQUIRED','requerido');
define('NAME_REQUIRED','El nombre es requerido.');
define('EMAIL_REQUIRED','El E-Mail es requerido.');
define('EMAIL_INVALID','El E-Mail ingresado no es válido.');
define('USERNAME_NOT_EXIST','No existe un usuario con ese nombre de usuario.');
define('USERNAME_USED','El nombre de usuario ya está en uso, intente con uno diferente por favor.');
define('USER_NOT_EXISTS', 'Usuario inexistente');

define('USERID_REQUIRED','El id de usuario es requerido.');
define('USERNAME_REQUIRED','El nombre de usuario es requerido.');
define('USERNAME_INVALID','El nombre de usuario elegido no es válido.');
define('PASSWD_REQUIRED','El password es requerido y debe contener al menos 8 caracteres.');

/*
 * Login
 */
define('LOGIN_TITLE', 'Sal! Dashboard');
define('LOGIN_USER_LABEL', 'Usuario');
define('LOGIN_PASS_LABEL', 'Clave');
define('LOGIN_SUBMIT_BUTTON', 'Enviar');

/*
 * Dashboard home
 */
define('DASHBOARD_WELCOME', 'Bienvenido %s (%s)');
define('DASHBOARD_LOGOUT', 'Salir');

/*
 * Dashboard manager
 */
define('RESTAURANT_DETAILS', 'Detalle del restaurante');
define('RESTAURANT_FRANCHISE', 'Cupones');
define('RESTAURANT_OFFERS', 'Ofertas');
define('RESTAURANT_COMMENTS', 'Comentarios / Ratings');
define('RESTAURANT_NOTIFICATIONS', 'Notificaciones');
define('RESTAURANT_MEDIA', 'Media');
define('RESTAURANT_SELECT', 'Seleccionar restaurante');
define('RESTAURANT_ADD', 'Agregar restaurante');
define('RESTAURANT_SELECTED', 'Restaurante seleccionado: ');
define('RESTAURANT_UPDATED_SUCCESS', 'Restaurante actualizado correctamente');
define('RESTAURANT_UPDATED_FAILURE', 'Error en la actualización del restaurante. Intente nuevamente por favor.');
define('RESTAURANT_UPDATED_PDF_FAILURE', 'Se han guardado los datos del restaurante mas el pdf del menú no ha subido correctamente, por favor intente nuevamente la carga de este elemento.');
define('RESTAURANT_VALIDATE_ID','Ingrese un valor válido para el ID.');
define('RESTAURANT_DASHBOARD_SELECT','El Restaurant ya tiene Dashboard activo, seleccionandolo...');
define('RESTAURANT_DASHBOARD_ACTIVE','El restaurant ya existe en reserva, se activo para el dashboard.');
define('RESTAURANT_DASHBOARD_ADD','Restaurant %s añadido con éxito.');
define('RESTAURANT_VALIDATE_SAL_ID','No existe un Restaurant en sal Web con ese ID.');
define('RESTAURANT_VALIDATE_DASH_ID','No existe un Restaurant con ese ID.');
define('RESTAURANT_SYNC_ERROR','Error sincronizando con Sal Web o el ID no existe.');
define('RESTAURANT_PACKAGE_ERROR', 'El paquete que posee no le permite realizar esta acción.');
define('RESTAURANT_FRANCHISE_ERROR', 'Esta acción no es válida para una franquicia.');
define('RESTAURANT_FRANCHISE_ONLY', 'Esta acción sólo es válida para las franquicias.');

define('DETAIL_TITLE', 'Detalle del restaurante');
define('DETAIL_LOGO_LABEL', 'Logo');
define('DETAIL_LOGO_COPY', 'La imagen debe tener como tamaño mínimo: <strong>%dx%d</strong>.');
define('DETAIL_LOGO_COPY_FORMAT', 'Los formatos aceptados son: <strong>%s</strong>.');
define('DETAIL_LOGO_COPY_SIZE', 'Tamaño máximo de archivo: <strong>%s</strong>.');
define('DETAIL_FOOD_LABEL', 'Tipo de comida');
define('DETAIL_DESCRIPTION_LABEL', 'Descripción');
define('DETAIL_EXCERPT_LABEL', 'Descripción corta');
define('DETAIL_NEW_PIC', 'Agregar nueva imagen');
define('DETAIL_ACTUAL_PICS', 'Imágenes actuales');
define('DETAIL_NEW_VIDEO', 'Agregar nuevo video');
define('DETAIL_ACTUAL_VIDEOS', 'Videos actuales');
define('DETAIL_NAME_LABEL', 'Nombre del restaurante');
define('DETAIL_ADDRESS_LABEL', 'Dirección física');
define('DETAIL_LATITUDE_LABEL', 'Latitud');
define('DETAIL_LONGITUDE_LABEL', 'Longitud');
define('DETAIL_TOWN_LABEL', 'Pueblo');
define('DETAIL_TAGS_LABEL', 'Etiquetas');
define('DETAIL_KEYWORDS_LABEL', 'Keywords');
define('DETAIL_LOCAL_ELEMENTS', 'Elementos de su local');
define('DETAIL_AMBIENCE', 'Ambiente');
define('DETAIL_PRICE', 'Precio');
define('DETAIL_TIME', 'Horario');
define('DETAIL_PHONE', 'Teléfono');
define('DETAIL_MAIL', 'Correo electrónico');
define('DETAIL_MENU', 'Menu');
define('DETAIL_MENU_PDF', 'pdf');
define('DETAIL_MENU_LOCU', 'Locu ID');
define('DETAIL_FACEBOOK', 'Facebook');
define('DETAIL_TWITTER', 'Twitter');

define('FRANCHISE_COUPON', 'Cupones');
define('FRANCHISE_COUPON_NAME', 'Título del cupón');
define('FRANCHISE_COUPON_CAPTION', 'Descripción');
define('FRANCHISE_COUPON_IMAGE', 'Imagen');
define('FRANCHISE_COUPON_START', 'Inicio');
define('FRANCHISE_COUPON_END', 'Finalización');
define('FRANCHISE_COUPON_PDF', '.pdf del cupón');
define('FRANCHISE_COUPON_USER_COUNT', 'Cantidad de usuarios');
define('FRANCHISE_COUPON_LIST', 'Listado de cupones');
define('FRANCHISE_COUPON_LIST_EXPORT_FILENAME', 'franchise-coupons.xls');
define('FRANCHISE_COUPON_MAIL_SUBJECT', "Sal.pr - Cupon");
define('FRANCHISE_COUPON_MAIL_BODY', "Datos del cupón:\nRestaurante: %s\nDescripción: %s\nVálido desde: %s hasta: %s");

define('COUPON_PREVIEW_TITLE', 'RECIBE TU CUPÓN GRATIS');
define('COUPON_INSERTED_SUCCESS', 'Cupón creado correctamente.');
define('COUPON_INSERTED_FAILURE', 'Error en la creación del cupón.');
define('COUPON_LINK_FAILURE', 'Error en la asociación del cupón, por favor póngase en contacto con Sal!.');
define('COUPON_DELETED_SUCCESS', 'Cupón eliminado correctamente.');
define('COUPON_DELETED_FAILURE', 'Error en el borrado del cupón. Intente nuevamente por favor.');
define('COUPON_EDIT_FORM', 'Editar cupón');
define('COUPON_EDITED_SUCCESS', 'Cupón editado correctamente.');
define('COUPON_EDITED_FAILURE', 'Error en la edición del cupón. Intente nuevamente por favor.');
define('COUPON_EMPTY', 'No se han encontrado cupones para ese restaurante.');
define('COUPON_PDF_FILE_TYPE_ERROR', 'El archivo seleccionado debe ser .pdf');

define('SOCIAL_SELECTOR_LABEL', 'Selector de localidades:');
define('COMMENT_SEE', 'Ver y responder comentario en Sal!');
define('COMMENT_LABEL', 'Comentarios del restaurante');
define('COMMENT_COUNTER', 'Cantidad de comentarios: ');
define('RATING_CATS', 'ambiente,servicio,comida');
define('RATING_GLOBAL', 'total');
define('RATING_VOTES', 'valoraciones');
define('RATING_VOTE', 'valoración');
define('RATING_LABEL', 'Ratings del restaurante');

define('MEDIA_IMG_LABEL', 'Imágenes');
define('MEDIA_VIDEOS_LABEL', 'Videos');
define('MEDIA_IMG_LIMIT', 'Ha llegado al límite de imágenes permitidas para este paquete');
define('MEDIA_VIDEO_LIMIT', 'Ha llegado al límite de videos permitidos para este paquete');
define('GALLERY_UPDATED_SUCCESS', 'Galería actualizada existosamente.');
define('GALLERY_UPDATED_FAILURE', 'Error en la actualización de la galería. Intente nuevamente por favor.');
define('GALLERY_NOT_EXISTS', 'La galería no existe');
define('VIDEO_WEBSERVICE_ERROR', 'Ocurrió un error en la comunicación con la plataforma de videos, intente nuevamente más tarde por favor.');

define('OFFER_TITLE', 'Ofertas');
define('OFFER', 'Oferta');
define('OFFER_PLUS', 'Oferta plus');
define('OFFER_DATED', 'Ofertas temporales');
define('OFFER_DATED_START', 'Desde');
define('OFFER_DATED_END', 'Hasta');
define('OFFER_DATED_TXT', 'Texto de la oferta');
define('OFFER_DATED_DATEPICKER', 'Seleccionar fecha');
define('OFFER_UPDATE_ERROR', 'Ocurrió un error en la actualización de las ofertas. Intente nuevamente por favor');
define('OFFER_UPDATE_SUCCESS', 'Ofertas actualizadas correctamente.');
define('PENDING_MANAGER_MESSAGE', 'Su restaurante tiene cambios que aún no han sido aprobados por Sal!');

/*
 * Dashboard admin
 */
define('USER_TITLE', 'Usuarios');
define('USER_LIST', 'Listado de usuarios');
define('USER_MANAGER', 'Manager de los Restaurants');

define('PACKAGE_SECTION_TITLE', 'Paquetes de Sal Listings');
define('PACKAGE_TITLE', 'Paquetes');
define('PACKAGE', 'Paquete');
define('PACKAGE_NEW', 'Crear nuevo Paquete');
define('PACKAGE_UPDATE', 'Paquete actualizado con éxito.');
define('PACKAGE_UPDATE_FAIL', 'Error guardando el paquete.');
define('PACKAGE_DELETE', 'Paquete eliminado con éxito.');
define('PACKAGE_DELETE_FAIL', 'Error eliminando el paquete.');
define('PACKAGE_DELETE_CONFIRM_TITLE','Eliminar paquete?');
define('PACKAGE_DELETE_CONFIRM_TEXT','Sí elimina el paquete se perderá toda la configuración de los features, está seguro?');
define('PACKAGE_FILE_ERROR','Falló el upload de packages.json a S3.');
define('PACKAGE_SELECT','Seleccione...');

define('FEATURE_SECTION_TITLE', 'Features de paquetes');
define('FEATURE_TITLE', 'Features');
define('FEATURE_UPDATE', 'Features actualizados con éxito.');
define('FEATURE_UPDATE_FAIL', 'Error guardando los features, verifique los valores.');
define('FEATURE_ADD', 'Nuevo Feature');
define('FEATURE_DELETE', 'Feature eliminado con éxito.');
define('FEATURE_DELETE_FAIL', 'Error eliminando el feature.');
define('FEATURE_DELETE_CONFIRM_TITLE','Eliminar feature?');
define('FEATURE_DELETE_CONFIRM_TEXT','Sí elimina este feature se perderán todas la configuraciones del mismo para los diversos paquetes, está seguro?');
define('FRANCHISE_TITLE', 'Franquicias');

define('APPROVAL_TITLE', 'Aprobación de cambios');
define('APPROVAL_EMPTY', 'No hay restaurantes con cambios pendientes de aprobación.');

define('RESTAURANT_ASSIGNMENT_TITLE', 'Asignación de paquetes');
define('RESTAURANT_ASSIGNMENT_FILTER', 'Filtrar listado');

define('NOTIFICATIONS_LABEL', 'Configuración de notificaciónes al restaurante');
define('NOTIFICATIONS_COPY', 'En todos los casos se puede ingresar una lista de emails separados por ",".');
define('NOTIFICATIONS_SHARE', 'Social shares');
define('NOTIFICATIONS_COMMENT', 'Comentarios');
define('NOTIFICATIONS_RATE', 'Rates');
define('NOTIFICATIONS_CONTACT_FORM', 'Formulario de contacto de Sal!');

define('RESTAURANT_PREVIEW_ID_ERROR', 'Id inexistente.');

/*
 * Interacciones Usuario
 */
define('INVALID_USER_PASS','Nombre de usuario y password incorrectos.');
define('REQUIRED_USER_PASS','El nombre de usuario y password son requeridos.');
define('SESSION_CLOSE','Ha cerrado su sesión correctamente.');
define('INVALID_USER_FOR_ACTION','Este usuario no tiene acceso.');
define('NO_ACCESS','Sin Acceso');

/*
 * File handling
 */
define('FILE_ERROR_TYPE','El tipo de archivo no es válido, sólo se permiten: %s');
define('FILE_ERROR_EMPTY','Archivo vacío');
define('FILE_ERROR_SIZE','El tamaño máximo permitido es de: %s');
define('FILE_ERROR_INTERNAL','Ocurrió un error interno, por favor intente nuevamente más tarde.');
define('FILE_ERROR_EXTERNAL','Ocurrió un error externo, por favor intente nuevamente más tarde.');
define('FILE_ERROR_TMP','No se pudo escribir en el directorio temporal /tmp verifique los permisos.');
define('FILE_ERROR_IMAGE_SMALL','El tamaño mínimo de imagen es: %dpx (largo) x %dpx (alto)');

?>