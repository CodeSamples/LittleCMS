
<form id="notificationsForm" method="post" class="none">
    <h1><?php echo NOTIFICATIONS_LABEL; ?></h1>
    <small><?php echo NOTIFICATIONS_COPY; ?></small>
    <br /><br />
    <div class="clear">
        <label><?php echo NOTIFICATIONS_SHARE; ?></label>
        <input type="text" id="share" name="share" value="" />
    </div>
    <div class="clear">
        <label><?php echo NOTIFICATIONS_COMMENT; ?></label>
        <input type="text" id="comment" name="comment" value="" />
    </div>
    <div class="clear">
        <label><?php echo NOTIFICATIONS_RATE; ?></label>
        <input type="text" id="rate" name="rate" value="" />
    </div>
    <div class="clear">
        <label><?php echo NOTIFICATIONS_CONTACT_FORM; ?></label>
        <input type="text" id="contact_form" name="contact_form" value="" />
    </div>
    <div class="clear last submitContainer">
        <input type="hidden" class="restaurantId" name="restaurant_id" value="" />
        <input type="submit" name="detail" value="<?php echo SAVE; ?>" />
    </div>
</form>
<div id="notificationsFormBlocker" class="pageBlocker"></div>
<div id="notificationsFormPreloader" class="preloader"></div>
<div id="formNotificationsResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<script type="text/javascript">
    
    function submitNotificationsForm(event) {
        $('#notificationsFormBlocker,  #notificationsFormPreloader').show();
        $.ajax({
            url: '/json/Restaurant/saveNotifications',
            type: 'post',
            data: $('#notificationsForm').serialize(),
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                $('#notificationsFormBlocker,  #notificationsFormPreloader').hide();
                var className = '';
                var msg = '';
                if(response.code != 1) {
                    msg = response.errors[0];
                    className = 'ui-state-error';
                } else {
                    msg = response.messages[0];
                    className = 'ui-state-highlight';
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
                            }
                        }
                    ],
                    dialogClass: "no-close"
                });
            }
        })
        event.preventDefault();
        return false;
    }
    $('#notificationsForm').on('submit', submitNotificationsForm);

    function populateNotificationsForm(data) {
        $('#share').val(data.share);
        $('#comment').val(data.comment);
        $('#rate').val(data.rate);
        $('#contact_form').val(data.contact_form);
        $('#notificationsFormBlocker,  #notificationsFormPreloader').hide();
    }

</script>