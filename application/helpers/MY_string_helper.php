<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	if( ! function_exists("add_ceros"))	{
		function add_ceros($numero,$len) {
			$order_diez = explode(".",$numero);
			$dif_diez = $len - strlen($order_diez[0]);
			for($m = 0; $m < $dif_diez; $m++)
			{
				@$insertar_ceros .= 0;
			}
			return $insertar_ceros .= $numero;
		}
	}
	
	if( ! function_exists("f_miles"))	{
		function f_miles($numero){
			return number_format($numero,2,".",",");
		}
	}
	
	if( ! function_exists("f_moneda"))	{
		function f_moneda($numero, $len=13){
			$numero = f_miles($numero);
			$dif = $len - strlen($numero);
			for($i = 0; $i < $dif; $i++)
			{
				@$espacios .= "&nbsp;";
			}
			return "$".$espacios.$numero;
		}
	}
