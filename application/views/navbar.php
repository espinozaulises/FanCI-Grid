<?php 
	$user = $this->session->userdata('estoy_login');
	$nombre = ucfirst( strtolower($user['nombre']) );?>
<?php if( !isset($menu) || $menu === true ): ?>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
    	<div class="container-fluid">
        	<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
            	<span class="icon-bar"></span>
            	<span class="icon-bar"></span>
            	<span class="icon-bar"></span>
          	</a>
            <div class="btn-group pull-right">
              <a href="#" data-toggle="dropdown" class="btn dropdown-toggle">
                  <li class="icon-user"></li> <?=$nombre?> <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                  <li><a href="#">Mis datos</a></li>
                  <li class="divider"></li>
                  <li><a href="<?=base_url('auth/logout');?>">Cerrar sesi&oacute;n</a></li>
              </ul>
            </div><!--/.nav-collapse -->
           	<div class="nav-collapse">
            	<ul class="nav">
              		<li class="active"><a href="#">Inicio</a></li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Clientes
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu"><li><a href="#">Directorio</a></li></ul>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Proveedores
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="<?=base_url('proveedores/directorio');?>">Directorio</a></li>
                      <li><a href="<?=base_url('proveedores/facturas');?>">Cuentas por pagar</a></li>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Compras/Gastos
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Art&iacute;culos y servicios</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Pedidos</a></li>
                      <li><a href="#">Compras</a></li>
                    </ul>
                  </li>
              		<li><a href="#">Finanzas</a></li>
              		<li><a href="#">Reportes</a></li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Configuraci&oacute;n
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Usuarios y permisos</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Bancos</a></li>
                      <li><a href="#">Cuentas bancarias</a></li>
                      <li><a href="#">Cajas</a></li>
                    </ul>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Herramientas
                      <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Respaldar la BD</a></li>
                    </ul>
                  </li>
            	</ul>
          	</div>
        </div>
    </div>
</div>
<?php endif; ?>