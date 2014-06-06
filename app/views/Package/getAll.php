<?php
$packages = $response["packages"];
$features = $response["features"];

$tabs_html = '<ul>';
$features_html = '';
?>
<div id="package-list">    
    <h1><?php echo PACKAGE_SECTION_TITLE; ?>:</h1>    
    <div id="packages-list">
        <?php
        foreach ($packages as $package) {
            $tabs_html .= '<li><a href="#package-' . $package["package"]->getID() . '">' . $package["package"]->getName() . '</a></li>';

            $features_html .='<div id="package-' . $package["package"]->getID() . '">';
            $features_html .= '<div class="package-features"><strong>' . PACKAGE . ': ' . $package["package"]->getName() . '</strong>';
            $features_html .='<form name="package-features-' . $package["package"]->getID() . '" action="" class="form-package-features">';
            $features_html .='<input type="hidden" name="ID" value="' . $package["package"]->getID() . '" /><ul>';
            $features_html .= '<li><label>' . NAME . '</label>';
            $features_html .= '<div class="input"><input type="text" name="name" value="' . $package["package"]->getName() . '"></div></li>';
            $features_html .= '<h3>Features:</h3>';

            foreach ($features as $feature) {
                $type = $feature->getType();
                $value = (isset($package["features"][$feature->getID()])) ? $package["features"][$feature->getID()] : '';
                $features_html .= '<li><label>' . $feature->getName() . '</label><div class="input">';

                switch ($type[0]) {
                    case 'text':
                        $features_html .= '<input type="text" name="feature[' . $package["package"]->getID() . '][' . $feature->getID() . ']" value="' . $value . '">';
                        break;
                    case 'int':
                        $features_html .= '<input class="input-spinner" data-min="' . $type[1]["from"] . '" data-max="' . $type[1]["to"] . '" name="feature[' . $package["package"]->getID() . '][' . $feature->getID() . ']" value="' . $value . '">';
                        break;
                    case 'options':
                        $options = $type[1]["options"];
                        $features_html .= '<select name="feature[' . $package["package"]->getID() . '][' . $feature->getID() . ']">';
                        foreach ($options as $option) {
                            $selected = ($value == $option) ? 'selected="selected"' : '';
                            $features_html .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                        }
                        $features_html .= '</select>';
                        break;
                    case 'boolean':
                        $selected_activo = '';
                        $selected_desativado = '';
                        if ($value == 1) {
                            $selected_activo = 'selected="selected"';
                        } else {
                            $selected_desativado = 'selected="selected"';
                        }
                        $features_html .= '<select name="feature[' . $package["package"]->getID() . '][' . $feature->getID() . ']">';
                        $features_html .= '<option value="1" ' . $selected_activo . '>Activado</option>';
                        $features_html .= '<option value="0" ' . $selected_desativado . '>Desactivado</option>';
                        $features_html .= '</select>';
                        break;
                }

                $features_html .= '</div></li>';
            }
            $features_html .= '<input class="save-button" type="submit" name="submit" value="' . SAVE . '"><a href="#" data-id="' . $package["package"]->getID() . '" class="delete-button">' . DELETE . '</a>';
            $features_html .= '</ul></form></div></div>';
        }
        $tabs_html .= '<li><a href="#package-new">...</a></li></ul>';
        echo $tabs_html . $features_html;
        ?>
        <div id="package-new">
            <strong><?php echo PACKAGE_NEW; ?>:</strong>
            <form id="package-new" name="package-new" action="" class="form-package-features">
                <ul>
                    <li><label>Nombre:</label>
                        <div class="input"><input type="text" name="package-name" value=""></div>
                    </li>     
                    <input class="create-button" type="submit" name="submit" value="<?php echo CREATE; ?>">
                </ul>    
            </form>    
        </div>    
    </div>
    <div id="dialog-confirm" title="<?php echo PACKAGE_DELETE_CONFIRM_TITLE; ?>">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
            <?php echo PACKAGE_DELETE_CONFIRM_TEXT; ?>    
        </p>
    </div>
    <script type="text/javascript">
        var tab_active = 0;

        $(document).ready(function() {

            $("#packages-list").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
            $("#packages-list li").removeClass("ui-corner-top").addClass("ui-corner-left");

            $(".input-spinner").spinner({
                spin: function(event, ui) {

                },
                start: function(event, ui) {
                    $(this).spinner("option", "min", $(this).data("min"));
                    $(this).spinner("option", "max", $(this).data("max"));
                }
            });

<?php
if (isset($_GET["active"])) {
    ?>
                var index = $('#packages-list a[href="#package-<?php echo $_GET["active"]; ?>"]').parent().index();
                $("#packages-list").tabs("option", "active", index);
<?php } ?>

            $(".form-package-features").submit(function() {
                var form = $(this).serialize();
                tab_active = $(this).find('input[name="ID"]').val();

                $.ajax({
                    data: form,
                    type: 'POST',
                    url: "/json/Package/save"
                }).done(function(data) {
                    $("#packages-messages").messageManager(data);
                    if (data.status === 'OK') {
                        $("#packages").load('/ajax/Package/getAll?active=' + tab_active);
                    }

                    $.ajax({
                        type: 'GET',
                        url: "/json/Package/generateJsonPackages"
                    }).done(function(data) {
                        $("#packages-messages").messageManager(data);
                    });
                });

                return false;
            });
            $(".delete-button").click(function() {
                var id_package = $(this).data("id");

                $("#dialog-confirm").dialog({
                    resizable: false,
                    height: 200,
                    position: {my: "left bottom", at: "right top", of: this},
                    modal: true,
                    buttons: {
                        "<?php echo DELETE; ?>": function() {
                            $.ajax({
                                data: 'ID=' + id_package,
                                type: 'POST',
                                url: "/json/Package/delete"
                            }).done(function(data) {
                                $("#packages-messages").messageManager(data);
                                if (data.status === 'OK') {
                                    $("#packages").load('/ajax/Package/getAll');
                                }
                                $.ajax({
                                    type: 'GET',
                                    url: "/json/Package/generateJsonPackages"
                                }).done(function(data) {
                                    $("#packages-messages").messageManager(data);
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

            $('.delete-button').button({
                icons: {
                    primary: "ui-icon-circle-close"
                }
            });

            $('.create-button').button({
                icons: {
                    primary: "ui-icon-circle-plus"
                }
            });

            $('.save-button').button();

        });
    </script>
    <style type="text/css">
        .ui-tabs-vertical { width: 55em; }
        .ui-tabs-vertical .ui-widget-header{
            background: #FF2B2B !important;
        }
        .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 10em; text-align: center; }
        .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
        .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
        .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; margin-bottom: 3px !important; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
        .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
    </style>