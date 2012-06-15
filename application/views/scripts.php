<!-- Cargamos los archivos javascript recibidos en el array $js -->
<?php if(isset($js)): ?>
	<?php foreach ($js as $script):	?>
	<script type="text/javascript" src="<?=base_url() . 'assets/scripts/' . $script?>.js"></script>
	<?php endforeach; ?>
<?php endif; ?>
<!-- Cargamos los archivos css recibidos en el array $css -->
<?php if(isset($css)): ?>
	<?php foreach ($css as $hoja): 	?>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/estilos/<?=$hoja?>.css"/>
	<?php endforeach; ?>
<?php endif; ?>
<!-- Si se ha enviado código javascript, lo insertamos en la página -->
<?php	echo ( isset($javascript) ) ? $javascript:''; ?>

