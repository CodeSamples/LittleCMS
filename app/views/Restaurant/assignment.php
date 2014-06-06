<?php

    $dashboardInput = '<input type="radio" name="dashboard_%d" value="1" id="dashboard_%d_1" %s />';
    $dashboardInput .= '<label for="dashboard_%d_1">' . ENABLED . '</label>';
    $dashboardInput .= '<input type="radio" name="dashboard_%d" value="0" id="dashboard_%d_2" %s />';
    $dashboardInput .= '<label for="dashboard_%d_2">' . DISABLED . '</label>';
?>
<div class="assignmentContainer">
    <div class="filter">
        <h2><?php echo RESTAURANT_ASSIGNMENT_FILTER; ?></h2>
        <input id="restaurantAssignmentFilter" type="text" name="restaurantAssignmentFilter" value="" />
        <div id="packages_filter"><label><?php echo BY_PACKAGE; ?>::</label>
             <input data-id="-1" type="checkbox" name="all" checked="checked" /><?php echo ALL; ?>
             <?php
                 foreach ($response['packages'] as $package) {
                            echo ' <input data-id="'.$package->ID.'" type="checkbox" name="" checked="checked" />'.$package->name;
                 }
             ?>               
        </div>
        <div id="food_filter">
            <label><?php echo BY_FOOD_TYPE; ?>:</label>
            <select id="foodFilter" name="foodType">
             <option value="all"><?php echo ALL; ?></option>   
             <?php
                 foreach ($response['foodTypes'] as $foodType) {
                            echo '<option value="'.$foodType.'">'.$foodType.'</option>';
                 }
             ?>     
            </select> 
        </div>
        <div style="clear: both;"></div>
    </div>
    <ul id="restaurantList" class="left">
        <?php foreach ($response['restaurants'] as $single): ?>
        <li id="rest_<?php echo $single->id;?>" class="<?php if($single->dashboard==1){ echo 'package_'.$single->package_id; } ?> <?php echo 'food_'.$single->foodType; ?>">
            <span class="name"><?php echo $single->name; ?></span>
            <span class="dashboardInput none" data-rest="<?php echo $single->id; ?>">
                <?php echo sprintf(
                        $dashboardInput,
                        $single->id,
                        $single->id,
                        ($single->dashboard) ? 'checked="checked"' : '',
                        $single->id,
                        $single->id,
                        $single->id,
                        (!$single->dashboard) ? 'checked="checked"' : '',
                        $single->id
                        ); ?>
            </span>
            <span class="packageSelector none" data-rest="<?php echo $single->id; ?>">
                <select class="packageSelector" name="package_<?php echo $single->id;?>">
                    <option value="0"><?php echo PACKAGE_SELECT; ?></option>
                    <?php

                        foreach ($response['packages'] as $package) {
                            $selected = ($single->package_id == $package->ID) ? 'selected="selected"' : '';
                            echo '<option value="' . $package->ID . '" ';
                            echo $selected;
                            echo '>' . $package->name . '</option>';
                        }

                    ?>
                </select>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
    <div id="restaurantOptions" class="options left none">
        <form id="formAssigment" method="post">
            <h1 class="title"></h1>
            <h2 class="dashTitle"><?php echo DAHSBOARD; ?></h2>
            <div class="dashboardInputContainer"></div>
            <h2 class="packageSelectorTitle"><?php echo PACKAGE_SECTION_TITLE; ?></h2>
            <div class="packageSelectorContainer"></div>
            <input type="submit" value="<?php echo SAVE; ?>" />
        </form>
    </div>
    <br class="clear" />
</div>
<div id="formAssigmentResult" class="ui-corner-all none">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b></b></p>
</div>
<script type="text/javascript">

    function deselectPreviousRestaurant() {
        $('#restaurantList li').removeClass('selected');
        $('#restaurantOptions .title').html('');
        var dashboardInput = $('#restaurantOptions .dashboardInputContainer').children();
        var packageSelector = $('#restaurantOptions .packageSelectorContainer').children();
        var prevRestLi = $('#rest_' + $(dashboardInput).attr('data-rest'));
        $(dashboardInput).addClass('none').appendTo(prevRestLi);
        $(packageSelector).addClass('none').appendTo(prevRestLi);
    }

    function selectRestaurant(event) {
        deselectPreviousRestaurant();
        
        $(this).addClass('selected');
        $('#restaurantOptions .title').html($(this).find('.name').html());
        $(this).find('.dashboardInput')
            .removeClass('none')
            .appendTo('#restaurantOptions .dashboardInputContainer');
        $(this).find('.packageSelector')
            .removeClass('none')
            .appendTo('#restaurantOptions .packageSelectorContainer');
        $('#restaurantOptions').slideDown(400);
    }

    function filterRestaurantList(event) {        
        var list = $('#restaurantList li');
        
        var matchText = $(this).val().toLowerCase();
        for(var i = 0; i < list.length; i++) {
            var itemText = $($(list[i]).find('.name').get(0)).html().toLowerCase();           
            if(itemText.indexOf(matchText) == -1) {
                $(list[i]).hide();
            } else {
                $(list[i]).show();
            }
        }
    }

    function submitAssignmentForm(event) {
        var formData = $(this).serializeArray();
        var ajaxData = {};

        for(var i = 0; i < formData.length; i++) {
            var name = formData[i].name.replace(/_\d*/g, '');
            var id = formData[i].name.replace(/\D/g, '');
            ajaxData[name] = formData[i].value;
            ajaxData.restaurant_id = id;
        } 
        if(ajaxData.dashboard==='1'){
           $('#rest_'+id).removeClass(function (index, css) {
                return (css.match (/\package_\S+/g) || []).join(' ');
           });
        
           $('#rest_'+id).addClass('package_'+ajaxData.package);
        }
        $.ajax({
            url: '/json/Restaurant/saveAssignment',
            method: 'post',
            data: ajaxData,
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                var className = 'ui-state-highlight';
                if(!response.response) {
                    className = 'ui-state-error';
                }

                $('#formAssigmentResult').removeClass('ui-state-highlight ui-state-error');
                $('#formAssigmentResult').addClass(className);
                $('#formAssigmentResult p b').html(response.messages[0]);
                $('#formAssigmentResult').dialog({
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

    $('#packages_filter input').change(function(event){                     
           if (this.checked) {
              if($(this).data("id")===-1){
                  if($('#food_filter select').val()==='all'){
                    $('#restaurantList>li').show();
                  } else {
                    $('#restaurantList>li.food_'+$('#food_filter select').val()).show();
                  }
                  $('#packages_filter>input').prop('checked', true);                    
              } else {
                  if($('#food_filter select').val()==='all'){
                    $('#restaurantList li.package_'+$(this).data("id")).show();
                  } else {
                    $('#restaurantList li.package_'+$(this).data("id")+'.food_'+$('#food_filter select').val()).show();
                  } 
              } 
           } else {
              if($(this).data("id")===-1){
                    $('#restaurantList>li').hide();
                    $('#packages_filter>input').removeAttr('checked');
                    $('#food_filter select').prop('selectedIndex', 0);  
              } else {
                    $('#restaurantList li.package_'+$(this).data("id")).hide();
              }
           }              
    });
    
    $('#food_filter select').change(function(event){                     
          if($(this).val()==='all'){
              $('#restaurantList>li').hide();
              $('#packages_filter input:checked').each(function(){   
                  if($(this).data('id')===-1){
                    $('#restaurantList li').show();  
                  } else {    
                    $('#restaurantList li.package_'+$(this).data('id')).show();
                  }  
              });              
          } else {
              $('#restaurantList>li').hide();
              var foodType = $(this).val();
              $('#packages_filter input:checked').each(function(){    
                  if($(this).data('id')===-1){
                    $('#restaurantList li.food_'+foodType).show();
                  } else {    
                    $('#restaurantList li.package_'+$(this).data('id')+'.food_'+foodType).show();
                  }                  
              });
          }
    });
    
    $(document).ready(function(){       
        $('#restaurantAssignmentFilter').on('keyup', filterRestaurantList);
        $('#restaurantList .dashboardInput').buttonset();
        $('#restaurantOptions input[type="submit"]').button();
        $('#restaurantList li').on('click', selectRestaurant);
        $('#restaurantAssignmentFilter').on('keyup', filterRestaurantList);
        $('#formAssigment').on('submit', submitAssignmentForm);
    });

</script>