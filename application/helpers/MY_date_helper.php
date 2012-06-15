<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('date_to_mysql') ) {
	function date_to_mysql($fecha)
	{
	    if( !empty($fecha) )	
	    {
	    	list($dia,$mes,$anio)=explode("/",$fecha);
	    	$fecha = "$anio-$mes-$dia";
		}	else 	{
			$fecha = "0000-00-00";
		}
		return $fecha;
	}
}

if( ! function_exists('date_mysql_to') ) {
	function date_mysql_to($fecha)
	{
	    if( !empty($fecha) )	
	    {
	    	list($anio,$mes,$dia)=explode("-",$fecha);
	    	$fecha = "$dia/$mes/$anio";
		}	else 	{
			$fecha = "Error: Fecha no v&aacute;lida.";
		}
		return $fecha;
	}
}