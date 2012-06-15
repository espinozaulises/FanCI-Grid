<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *	Botones CSS3 con íconos
 * */
if ( ! function_exists('action_button_icon')) {
	function action_button_icon($label = '', $icon='', $class='', $type='', $extra = '', $lbl_color='')
	{
		if( empty($label) AND !empty($icon) ){
			$content = "";
			$span_class   = "icon ".$icon;
			
		} elseif ( !empty($label) AND empty($icon) ){
			$content = $label;
			$span_class   = "label";
			
		} elseif ( !empty($label) AND !empty($icon) ){
			$content = "</span><span class='label $lbl_color'>".$label;
			$span_class   = "icon ".$icon;
			
		}
		$extra .= ( empty($type) ) ? '' : ' type="'.$type.'"';
		$extra .= ( empty($class) ) ? '' : ' class="'.$class.'"';
		
		return '<button '.$extra.'><span class="'.$span_class.'">'.$content.'</span></button>';
	}
}

if ( ! function_exists('a_button_icon')) {
	function a_button_icon($url='', $label = '', $icon='', $class='', $extra = '', $lbl_color='')
	{
		if( empty($label) AND !empty($icon) ){
			$content = "";
			$span_class   = "icon ".$icon;
				
		} elseif ( !empty($label) AND empty($icon) ){
			$content = $label;
			$span_class   = "label";
				
		} elseif ( !empty($label) AND !empty($icon) ){
			$content = "</span><span class='label $lbl_color'>".$label;
			$span_class   = "icon ".$icon;
				
		}
		$extra .= ( empty($class) ) ? '' : ' class="button '.$class.'"';

		return '<a href="'.$url.'" '.$extra.'><span class="'.$span_class.'">'.$content.'</span></a>';
	}
}

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

if( ! function_exists('format_money') ) {
	function format_money($number) {
	    $number = sprintf('%.2f', $number);
	    while (true) {
	        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
	        if ($replaced != $number) {
	            $number = $replaced;
	        } else {
	            break;
	        }
	    }
	    return '<span style="float:left">$</span><span style="float:right">'.$number.'</span>';
	}
}

if( ! function_exists('object2array') ) {
	function object2array($valor){//valor
		if( !( is_array($valor) || is_object($valor) ) ){ //si no es un objeto ni un array
			$dato = $valor; //lo deja
		} else { //si es un objeto
			foreach($valor as $key => $valor2){ //lo conteo
				$dato[$key] = object2array($valor2); //
			}
		}
		return $dato;
	}
}

if( ! function_exists('fanci_toolbar') ) {
	function fanci_toolbar(){
		//foreach($buttons as $key => $val){
			//$this->load->helper("form");
			$input  = array(
					  'name'        => 'str_search',
					  'id'          => 'str_search',
					  'class'       => 'text search-query',
					  'style'		=> 'float:left; margin-right:5px;'
					);
			
			//$filtro .= "<span>Filtros:&nbsp;</span>";
		foreach($this->columns as $key => $val){
			if( $key <> $this->i_col_check AND $key <> $this->i_col_actions )	
				$select[$key] = $val["data"];
		}
			$filtro  = form_open($this->url_site."/".$this->uri["hash"],"id='form_filter'") . form_input($input)."&nbsp;";
			$filtro .= form_dropdown("dg_filter_by", $select ,'','id="dg_filter_by" class="style-select ttipDw" title="Buscar en el campo..."');
			$filtro .= action_button_icon("", "icon198",'ttipDw left','submit', "id='btool-search' title='Buscar'");
			$filtro .= action_button_icon("", "icon83",'ttipDw right','','id="btool-clear" title="Limpiar la busqueda"');

		foreach( $buttons as $key => $val)
		{
			$filtro .= a_button_icon($val["url"],$val["text"], 'icon'.$key, 'ttipDw '.$val["class"],'id="btool-'.$key.'" title="'.$val["title"].'"',$val["color"]);
			
		}	
			$filtro .= form_close();
			
			return $filtro;
		//}
	}
}