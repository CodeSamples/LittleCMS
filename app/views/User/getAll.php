<?php



?>
<div id="tableUsers" class="tableContainer"></div>
<div id="edit_manager_dialog" class="none" title="">
    
    <div id="container_manager_list">
    <form id="rest_manager">
    <ul id="rest_manager_list">
        
    </ul>
    <input id="user_id_manager" type="hidden" name="user_id" value="" />
    </form>
    <div style="clear: both;"></div>
    </div>   
    <a id="save-button-manager">Guardar Cambios</a>   
    <div id="container_manager_add">
    <h4>Agregar Nuevo:</h4>
    <div id="restaurantSelector">
    <div class="preloader"></div>
        <div class="assignmentContainer">
        <div class="filter none" style="margin: 0.5em 1em 0.5em 0.2em;">
            <h2><?php echo RESTAURANT_ASSIGNMENT_FILTER; ?></h2>
            <input id="restaurantFilter" type="text" />
        </div>  
        </div>
	<ol id="selectable"></ol>      
    </div>
    </div>     
</div>
<script type="text/javascript">
var edited_username;
var table_users = $('#tableUsers').jtable({
            title: '<?php echo USER_LIST; ?>',
            paging: true,
            pageSize: 15,
            sorting: true,
            defaultSorting: 'username DESC',
            actions: {
                listAction: '/api/User/getAll',
                createAction: '/api/User/create',
                updateAction: '/api/User/update',
                deleteAction: '/api/User/delete'
            },
            fields: {
                userID: {
                    key: true,
                    list: false
                },
                username: {
                    title: '<?php echo USERNAME; ?>',
                    edit: true,
                    inputClass: 'doubleCheck',
                    create: true
                },
                userpassword: {
                    title: '<?php echo PASSWORD; ?>',
                    type: 'password',
                    list: false
                },
                realname: {
                    title: '<?php echo NAME; ?>',
                    width: '20%'
                },
                email: {
                    title: '<?php echo MAIL; ?>',
                    width: '20%'
                },
                rest_manager: {
                    title: '<?php echo USER_MANAGER; ?>',
                    width: '20%',
                    sorting: false,
                    edit: false,
                    create: false
                },
                dashboard_role: {
                    title: '<?php echo ROLE; ?>',
                    options: {
                        'null': '<?php echo NO_ACCESS; ?>', 
                        '<?php echo ROLE_ADMIN; ?>': '<?php echo ADMIN; ?>', 
                        '<?php echo ROLE_MANAGER; ?>': '<?php echo MANAGER; ?>'
                    },
                    width: '10%'
                }
            },
            recordsLoaded: function(event, data){
                 $(".edit_manager").click(function(){
                      $("#rest_manager_list").html("");
                      var list = $(this).data("ids")+'';
                      var names = $(this).data("names")+'';
                      var userid = $(this).data("userid");
                      var username = $(this).data("username");
                      list = list.split(",");
                      names = names.split(",");
                      $("#user_id_manager").val(userid);
                      if(list.length>0){
                        $(list).each(function(pos, value){
                            if(value!==''){                        
                                $("#rest_manager_list").append('<li id="rest-'+userid+'-'+value+'"><input type="checkbox" name="manager['+userid+']['+value+']" checked="checked" />'+names[pos]+'</li>');
                            }   
                        });
                      }
                      
                      $.ajax({
					url:'/api/Restaurant/listAll',
					type:'get',
					success: function(data) {
						try {
							var resturantList = $.parseJSON(data);
							var listHtml = '';
							for(var i = 0; i < resturantList.length; i++) {
								listHtml += '<li class="ui-widget-content add-rest-li" data-id="'
									+ resturantList[i].id + '" data-name="'
									+ resturantList[i].name + '" data-userid="'
									+ userid + '" data-username="'
									+ username + '">'
									+ '<a>' + resturantList[i].name + '</a>'
									+ '</li>';
							}
							$('#selectable').html(listHtml);
							$('#selectable').selectable();
							$('#restaurantSelector .preloader').hide();
                                                        $('#restaurantSelector .filter').fadeIn();
							$('#selectable').slideDown(400);
                            $('.add-rest-li').click(function(){      
                               if(!$("#rest-"+$(this).data("userid")+'-'+$(this).data("id")).length){                                                          
                                    $("#rest_manager_list").append('<li id="rest-'+$(this).data("userid")+'-'+$(this).data("id")+'"><input type="checkbox" name="manager['+$(this).data("userid")+']['+$(this).data("id")+']" checked="checked" />'+$(this).data("name")+'</li>');
                               } 
                            });
						} catch (e) {
							console.log(e);
						}
					}
				});
                      
                      edited_username = username;
                      
                      $( "#edit_manager_dialog" ).dialog({
                        height: "auto",
                        width: 600,
                        position: ['top', 'center'],
                        modal: true,
                        title: "Restaurantes Asociados al Manager: "+username
                      });
                 });
            }
        });

        $('#tableUsers').jtable('load');
        
        $('#save-button-manager').button();
        $('#restaurantFilter').on('keyup', filterRestaurantList);
        $('#save-button-manager').click(function(){
            $('#edit_manager_dialog').dialog('close');
            $.ajax({
		url:'/json/User/saveManager',
		type:'post',
                data: $("#rest_manager").serialize(),
                success: function(data) {
                    $("#users-messages").messageManager(data);
                    table_users.jtable('reload',function(){
                          $('#tableUsers .jtable tr').each(function(){
                              if($(this).data("record-key")===edited_username){
                                  $(this).effect("highlight", {}, 3000);
                              }
                          });
                    });
                }
            });
        });
        
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

       
</script>
<style type="text/css">
    .edit_manager{
        color: #FF0000;
        cursor: pointer;
    }
    #container_manager_list, #container_manager_add{
        margin: 5px;
        padding: 5px;
        border: 1px solid #ccc;
    }
    #restaurantSelector #selectable{
        height: 250px;
        overflow-y: scroll;
    }
    #rest_manager_list li{
        margin-left: 20px;
        font-size: 12px;
        font-weight: normal;
        list-style-type: none;
        width: 230px;
        float: left;
    }
    #edit_manager_dialog h4{
        font-size: 14px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }
    #save-button-manager{
        margin: 10px 10px 10px 10px;
    }
</style>