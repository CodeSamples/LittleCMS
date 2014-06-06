<?php
$default_tab = 0;
if (isset($_COOKIE["dashboard_settings"])) {
    $settings = json_decode($_COOKIE["dashboard_settings"]);
    $default_tab = isset($settings->default_admin_tab) ? $settings->default_admin_tab : 0;
}

include_once('includes/header-top-bar.php');
?>
<div class="dashboardMainContainer">
    <div id="tabs">
        <ul>
            <li><a href="#fragment-1"><span><?php echo USER_TITLE; ?></span></a></li>
            <li><a href="#fragment-2"><span><?php echo PACKAGE_TITLE; ?></span></a></li>
            <li><a href="#fragment-3"><span><?php echo FEATURE_TITLE; ?></span></a></li>
            <li><a href="#fragment-4"><span><?php echo APPROVAL_TITLE; ?></span></a></li>
            <li><a href="#fragment-5"><span><?php echo RESTAURANT_ASSIGNMENT_TITLE; ?></span></a></li>
        </ul>
        <div id="fragment-1">   
            <div id="users-messages" class="auto-hidden"></div>
            <div id="users"></div>            
        </div>
        <div id="fragment-2">
            <div id="packages-messages" class="auto-hidden"></div>
            <div id="packages">                        
            </div>	
        </div>
        <div id="fragment-3">
            <div id="features-messages" class="auto-hidden"></div>
            <div id="features">                        
            </div>	
        </div>
        <div id="fragment-4">
            <div id="approval-messages" class="auto-hidden"></div>
            <div id="approval">                        
            </div>  
        </div>
        <div id="fragment-5">
            <div id="assignment-messages" class="auto-hidden"></div>
            <div id="assignment">                        
            </div>  
        </div>
    </div>
</div>
<ul id="menu">

</ul>
<script type="text/javascript">

    function approveRestaurant(event) {
        console.log($(this).parent().attr('id').replace(/\D/g, ''));
    }

    $(document).ready(function() {
        $('#tabs').tabs({
            active: <?php echo $default_tab; ?>,
            activate: function(event, ui) {               
                var setting = {
                    setting : 'default_admin_tab',
                    value :  ui.newTab.index()       
                };
                
                $.ajax({
                    data: setting,
                    type: 'POST',
                    url: "/api/Dashboard/userState"
                });
            }
        });

        
        $("#users").load('/ajax/User/getAll');
        $("#packages").load('/ajax/Package/getAll');
        $("#features").load('/ajax/Feature/getAll');
        
        $.ajax({
            url: '/ajax/Restaurant/listPending',
            type: 'get',
            success: function(response) {
                $("#approval").html(response);
                $('#approval a.see').button({
                    icons: {
                        secondary: 'ui-icon-circle-arrow-e'
                    }
                });
                $('#approval a.preview').button({
                    icons: {
                        secondary: 'ui-icon-newwin'
                    }
                });
                $('#approval a.approve').button({
                    icons: {
                        secondary: 'ui-icon-circle-check'
                    }
                });
                $('#approval a.reject').button({
                    icons: {
                        secondary: 'ui-icon-circle-close'
                    }
                });
            }
        });

        $('#assignment').load('/ajax/Restaurant/assignment');

    });
</script>
<script src="/js/jquery.jtable.min.js"></script>
<script src="/js/jquery.jtable.es.js"></script>
<link href="/css/jtable.css" rel="stylesheet" type="text/css" />