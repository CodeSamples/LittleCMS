<?php
   $user = $_SESSION["userObject"]; 
   $user_role = $user->getDashboardRole();       
?>
<div class="dashboardHeaderTop">
    <div id="header_menu">
        <span class="logo">
		<a href="/Dashboard/home">
	        	<img src="/img/logo-sal-small.png" width="54" height="43" alt="Sal!" />                        
	        </a>	
                <div><?php echo DAHSBOARD; ?></div>
        </span>	
        <ul>
            <li><a href="/Dashboard/manager"><?php echo MANAGER_AREA; ?></a></li>
            <li><a href="#" class="disabled"><?php echo STATS_AREA; ?></a></li>
          <?php if($user_role==ROLE_ADMIN): ?>    
            <li><a href="/Dashboard/admin"><?php echo ADMIN_AREA; ?></a></li>
          <?php endif; ?>
          <?php if($user_role==ROLE_ADMIN): ?> 
            <li><a id="reserveLink" href="<?php echo SAL_RESERVA_URL; ?>"><?php echo SAL_RESERVA; ?></a></li>
          <?php else: ?>
            <li><a id="reserveLink" href="#" class="disabled"><?php echo SAL_RESERVA; ?></a></li>
          <?php endif; ?>

            <li class="image"><a href="/User/logout" class="image"><img src="http://<?php echo BASE_URL; ?>/img/icon_logout.png" alt="<?php echo DASHBOARD_LOGOUT; ?>" title="<?php echo DASHBOARD_LOGOUT; ?>" /></a></li>
        </ul>
    </div>   
</div>
<div style="clear: both;"></div>
<!-- <div id="welcome"><?php echo sprintf(DASHBOARD_WELCOME, $response->userRealName, $response->userName); ?></div> -->