<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title><?=$title;?></title>
    <link href='http://fonts.googleapis.com/css?family=Arimo|Ubuntu|Open+Sans' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/style-doc.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/fanci-grid.css"/>

    <!--[if (gte IE 6)&(lte IE 8)]>
      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
      <script type="text/javascript" src="<?=base_url();?>assets/js/selectivizr.js"></script>
      <noscript><link rel="stylesheet" href="[fallback css]" /></noscript>
    <![endif]-->

    <!-- Scripts -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write("<script src='<?=base_url();?>assets/js/jquery-1.7.2.min.js'><\/script>")</script>

    <script type="text/javascript" src="<?=base_url();?>assets/js/jquery.fanCIgrid.js"></script>
    <!-- <script type="text/javascript" src="<?=base_url();?>assets/js/jquery.history.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/js/jquery.tipsy.js"></script> -->
    <?=$scripts?>

    <style type="text/css">
    	.toolbar-container {
    		  display: table-cell;
          vertical-align: middle;
          position: relative;
    	}
    </style>
  </head>
  <body>
    	<div class="container">
    		<div class="toolbar">
    				<?="<h1>$caption</h1>"; ?>
    				<ul><li><?=isset( $toolbar ) ? $toolbar:''?></li></ul>

    		</div>
    			<?=$contenido; ?>
    	</div>

  </body>

</html>
