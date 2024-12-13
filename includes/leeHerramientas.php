<?php
    $menuHtasHTML = '';
    if ($tipoUser  == 7) {
      $menuHtasHTML = '

      <a href="pcontrol/modulos/usuarios.php" target="_top" style="text-decoration:none; ">
	      <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="iconos-ok/ico_users.png" width="80" height="96" alt="gestion usuarios"/>
      </a>
      
      <a href="pcontrol/modulos/logs.php" target="_top" style="text-decoration:none; ">
	      <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="iconos-ok/ico_logs.png" width="80" height="96" alt="ver logs"/>
      </a>
      <!-- 
      <a href="pcontrol/online.php" target="_top" style="text-decoration:none; ">
	      <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="images/icon-online.png" width="80" height="96" alt="ver logs"/>
      </a>
	  -->
      ';	
    }
    
?>