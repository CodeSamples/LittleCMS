<?php
    
    $errors = $obj_response->getErrors();
    $messages = $obj_response->getMessages();

?>
<div class="loginHeader">
    <span>
        <img src="/img/logo-sal.png" width="108" height="85" alt="Sal!" />
    </span>
    <span><?php echo DAHSBOARD; ?></span>
</div>
<div id="loginMessages">
    <?php foreach ($errors as $value): ?>
    <div class="ui-state-error ui-corner-all">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <?php echo $value; ?></p>
    </div>
    <?php endforeach; ?>
    <?php foreach ($messages as $value): ?>
    <div class="ui-state-highlight ui-corner-all">
        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
        <?php echo $value; ?></p>
    </div>
    <?php endforeach; ?>
</div>
<div class="loginFormContainer">
    <h1><?php echo LOGIN_TITLE; ?></h1>
    <br/><br/>
    <p>Estamos encantados de verte volver! Por favor identifícate para continuar.</p>
    <form name="login" action="/User/login" method="post">
        <label><?php echo LOGIN_USER_LABEL; ?></label>
        <input type="text" name="username" value="" />
        <br/><br/>
        <label><?php echo LOGIN_PASS_LABEL; ?></label>
        <input type="password" name="password" value="" />
        <br/><br/>
        <div class="centerText">
            <input id="loginFormSubmit" type="submit" value="<?php echo LOGIN_SUBMIT_BUTTON; ?>" />
        </div>
    </form>
        <p class="loginBottomText">
            <small style="vertical-align:middle;">Problemas con tu cuenta? Escríbenos a <a href="mailto:servicio@sal.pr">servicio@sal.pr</a></small>
        </p>
</div>
<script type="text/javascript">
    $('#loginFormSubmit').button();
</script>