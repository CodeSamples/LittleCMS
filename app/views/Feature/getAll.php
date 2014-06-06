<?php

function feature_selected_type($type, $actual) {
    $selected = '';
    if (isset($type[0])) {
        if ($type[0] === $actual) {
            $selected = 'selected="selected"';
        }
    }

    return $selected;
}

$features = $response;
?>
<div id="feaures-list">    
    <h1><?php echo FEATURE_SECTION_TITLE; ?>:</h1>    
    <div id="features-list">
        <form id="featuresForm" name="features">
            <input class="save-button" type="submit" name="submit" value="<?php echo SAVE; ?>">
            <a class="add-button" href="#" id="add-feature-button" ><?php echo FEATURE_ADD; ?></a>
            <a class="cancel-button" href="#" id="add-feature-button" ><?php echo CANCEL; ?></a>
            <ul id="features-list-ul">    
                <?php
                $last_id = 1;
                foreach ($features as $feature) {
                    echo '<li class="feature"><a href="#" data-id="' . $feature->getID() . '" class="feature-delete-button">' . DELETE . '</a>';
                    echo '<ul><li><label>' . NAME . ':</label><input type="text" class="mediumtext" name="feature[' . $feature->getID() . '][name]" value="' . $feature->getName() . '" maxlength="255" /></li>';
                    echo '<li><label>Tipo:</label>';

                    $type = $feature->getType();
                    if (!isset($type[0])) {
                        $type[0] = 'int';
                    }
                    ?>
                    <select id="feature-type-<?php echo $feature->getID(); ?>" name="feature[<?php echo $feature->getID(); ?>][type]" onchange="featureValues(<?php echo $feature->getID(); ?>)">
                        <option value="int" <?php echo feature_selected_type($type, 'int') ?>>Valor Entero</option>
                        <option value="options" <?php echo feature_selected_type($type, 'options') ?>>Opciones</option>
                        <option value="boolean" <?php echo feature_selected_type($type, 'boolean') ?>>Booleano (Sí / No)</option>
                        <option value="text" <?php echo feature_selected_type($type, 'text') ?>>Texto</option>
                    </select>
                    </li>
                    <li>
                        <div id="feature-values-<?php echo $feature->getID(); ?>">
                            <div id="feature-values-int-<?php echo $feature->getID(); ?>" style="display: <?php echo ($type[0] === 'int') ? 'block' : 'none' ?>;">
                                Desde: <input class="smalltext" type="text" name="feature[<?php echo $feature->getID(); ?>][from]" value="<?php echo isset($type[1]["from"]) ? $type[1]["from"] : 0; ?>" />
                                Hasta: <input class="smalltext" type="text" name="feature[<?php echo $feature->getID(); ?>][to]" value="<?php echo isset($type[1]["to"]) ? $type[1]["to"] : 100; ?>" />
                            </div>
                            <div id="feature-values-options-<?php echo $feature->getID(); ?>" style="display: <?php echo ($type[0] === 'options') ? 'block' : 'none' ?>;">
                                Opciones: <input type="text" name="feature[<?php echo $feature->getID(); ?>][options]" value="<?php echo isset($type[1]["options"]) ? implode(',', $type[1]["options"]) : 'option1,option2'; ?>" /> 
                                (Ingrese las opciones separadas por comas)
                            </div>
                            <div id="feature-values-boolean-<?php echo $feature->getID(); ?>" style="display: <?php echo ($type[0] === 'boolean') ? 'block' : 'none' ?>;">
                                (Es una opción true/false, es decir si el paquete dispone del feature o no)
                            </div>
                            <div id="feature-values-text-<?php echo $feature->getID(); ?>" style="display: <?php echo ($type[0] === 'text') ? 'block' : 'none' ?>;">
                                (Cualquier valor de texto es posible para el valor del feature del paquete)
                            </div>
                        </div>
                    </li>
                </ul>
                </li>
                <?php
                $last_id = $feature->getID();
            }
            ?>
            </ul>
        </form>
    </div>
    <div style="clear: both;"></div>
</div>                      
<div id="dialog-confirm-feature" title="<?php echo FEATURE_DELETE_CONFIRM_TITLE; ?>" style="display:none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
        <?php echo FEATURE_DELETE_CONFIRM_TEXT; ?>    
    </p>
</div>      
<script type="text/javascript">
var last_id_feature = <?php echo $last_id + 1; ?>;

function featureValues(id) {
    var type = $("#feature-type-" + id).val();

    $("#feature-values-" + id).children().hide();
    $("#feature-values-" + type + "-" + id).fadeIn('fast');
}

$(document).ready(function() {
    $('.save-button').button({
        icons: {
            primary: "ui-icon-disk"
        }
    });

    $('.cancel-button').button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });

    $('.add-button').button({
        icons: {
            primary: "ui-icon-circle-plus"
        }
    });

    $('.feature-delete-button').button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });

    $(".cancel-button").click(function() {
        $("#features").load('/ajax/Feature/getAll');
    });

    $("#featuresForm").submit(function() {
        var form = $(this).serialize();

        $.ajax({
            data: form,
            type: 'POST',
            url: "/json/Feature/save"
        }).done(function(data) {
            $("#features-messages").messageManager(data);
            if (data.status === 'OK') {
                $("#features").load('/ajax/Feature/getAll');
                $("#packages").load('/ajax/Package/getAll');
            }
            $.ajax({
                type: 'GET',
                url: "/json/Package/generateJsonPackages"
            }).done(function(data) {
                $("#features-messages").messageManager(data);
            });
        });

        return false;
    });
    $("#add-feature-button").click(function() {
        var html = '<li class="feature new-feature"><ul><li><label><?php echo NAME; ?>:</label>';
        html += '<input type="text" name="feature[' + last_id_feature + '][name]" value="" maxlength="255" /></li>';
        html += '<li><label>Tipo:</label>';
        html += '<select id="feature-type-' + last_id_feature + '" name="feature[' + last_id_feature + '][type]" onchange="featureValues(' + last_id_feature + ')">';
        html += '       <option value="int">Valor Entero</option>';
        html += '       <option value="options">Opciones</option>';
        html += '       <option value="boolean">Booleano (Sí / No)</option>';
        html += '       <option value="text">Texto</option>';
        html += '</select></li><li>';
        html += '   <div id="feature-values-' + last_id_feature + '">';
        html += '       <div id="feature-values-int-' + last_id_feature + '" style="display: block">';
        html += '         Desde: <input class="smalltext" type="text" name="feature[' + last_id_feature + '][from]" value="0" />';
        html += '         Hasta: <input class="smalltext" type="text" name="feature[' + last_id_feature + '][to]" value="100" />';
        html += '       </div>';
        html += '       <div id="feature-values-options-' + last_id_feature + '" style="display: none">';
        html += '         Opciones: <input type="text" name="feature[' + last_id_feature + '][options]" value="option1,option2" /> ';
        html += '         (Ingrese las opciones separadas por comas)';
        html += '       </div>';
        html += '       <div id="feature-values-boolean-' + last_id_feature + '" style="display: none">';
        html += '         (Es una opción true/false, es decir si el paquete dispone del feature o no)';
        html += '       </div>';
        html += '       <div id="feature-values-text-' + last_id_feature + '" style="display:none;">';
        html += '         (Cualquier valor de texto es posible para el valor del feature del paquete)';
        html += '       </div>';
        html += '   </div></li></ul></li>';
        last_id_feature++;
        $("#features-list-ul").append(html);
        $(".new-feature").focus();
        $(".new-feature").css("border", "2px solid #FF0000");
    });

    $(".feature-delete-button").click(function() {
        var id_feature = $(this).data("id");

        $("#dialog-confirm-feature").dialog({
            resizable: false,
            height: 250,
            position: {my: "rigth bottom", at: "left top", of: this},
            modal: true,
            buttons: {
                "<?php echo DELETE; ?>": function() {
                    $.ajax({
                        data: 'ID=' + id_feature,
                        type: 'POST',
                        url: "/json/Feature/delete"
                    }).done(function(data) {
                        $("#features-messages").messageManager(data);
                        if (data.status === 'OK') {
                            $("#features").load('/ajax/Feature/getAll');
                            $("#packages").load('/ajax/Package/getAll');
                        }
                        $.ajax({
                            type: 'GET',
                            url: "/json/Package/generateJsonPackages"
                        }).done(function(data) {
                            $("#features-messages").messageManager(data);
                        });
                    });
                    $(this).dialog("close");
                },
                Cancel: function() {
                    $(this).dialog("close");
                }
            }
        });
    });
});

</script>