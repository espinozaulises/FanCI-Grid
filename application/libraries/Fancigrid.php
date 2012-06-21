<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Ci_datagrid
 *
 * Author: Ulises Vázquez Espinoza
 *
 * Created:  31.12.2011
 *
 * Description:
 *
 */

class Fancigrid
{

	//Parametros que pueden definirse en el controller.
	public $actions 	= array();	// Botones de acción de la tabla.
	public $columns 	= array(); 	// Arreglo que contiene las columnas y sus opciones
	public $extra_params= 0;		// Valores de las variables extra por default 
	public $grid_name	= 'fanCIgrid';
	public $my_segment	= 3;		// El segmento de la URI a partir del que pasaremos los datos.
	public $pagination 	= array();	// Parametros de la librería pagination. base_url y per_page
	public $prim_key	= "id";		// Nombre del campo de clave primaria por default.
	public $prim_key_hide = true;	// Esconder/mostrar el id en el grid.
	public $segment		= "";
	public $sql_query	= array(); 	// Array que contiene los elementos de la consulta.
	public $sql_string  = ""; 		// Cadena de texto de la consulta sql 
	public $url_site 	= "";
	
	public $like_string	= "";		// Cadena que contiene los datos del filtro/busqueda de texto.
	public $vars_url	= ""; 		// Variables extras pasadas en el url (después de $my_segment)
	public $uri 		= array(); 	// Arreglo que contiene los datos de la url actual.
	public $col_check   = TRUE;
	private $i_col_check;
	public $col_actions = TRUE;
	private $i_col_actions;

	public $autosum = FALSE;		// Mostrar la sumatoria de las columnas con formato money
	// Configuració de las constantes.
	const O_ASC			= "ASC";
	const O_DESC		= "DESC";
	const TABLE_BUTTONS	= 'ACCIONES';
	//Constantes de formato de datos
	const A_CENTER		= 'center';
	const A_RIGHT		= 'right';
	const TYPE_GENERAL 	= 'default';
	const TYPE_MONEY 	= 'money';
	const TYPE_LINK 	= 'link';
	const TYPE_CHECK 	= 'check';
	const TYPE_PERCENT 	= 'percent';
	const TYPE_DATE		= 'date';
	const TYPE_REPLACE	= 'replace';
	const ROW_CHECK 	= "<input class='dg_check_item' type='checkbox' name='dg_item[]' value='";
	const HEAD_CHECK	= "<input type='checkbox' class='dg_check_toggler'>";


	
	// Creando el constructor y pasamos los parametros del controlador
	public function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->library(array('table','pagination'));
		$this->CI->config->load('fancigrid');
		$this->CI->load->helper(array('fanci_helper','security'));
		$this->CI->load->model("fanci_model");

		log_message('debug', "FanCIGrid Class Initialized");		
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Array
	 *
	 * Formatea el array de manera que todo los campos tengan los campos data, sorter, filter y format.
	 *
	 * @access	public
	 * @param	type
	 * @return	type
	 */
	private function _set_headers()
	{
		$i = 0;
		$args = $this->columns;

		if ( $this->col_check )
		{
			$args[$i] = array(
						"data" 	 => self::HEAD_CHECK,
						"sorter" => FALSE,
						"filter" => FALSE,
						"format" => self::A_CENTER);
			$this->i_col_check = $i;
		}

		foreach($args as $key => $val)
		{
			
			if( ! isset($val["sorter"] ) || empty($val["sorter"]) )
			{
				$args[$key]["sorter"] = FALSE;
			}
			if( ! isset($val["filter"] ) || empty($val["filter"]) )
			{
				$args[$key]["filter"] = FALSE;
			}
			if( ! isset($val["format"] ) || empty($val["format"]) )
			{
				$args[$key]["format"] = self::TYPE_GENERAL;
			}
			$i++;
		}
		if( count($this->actions) > 0 )
		{
			foreach ($this->actions as $key) {
				$args[$i] = array(
						"data" 	 => '&nbsp;',
						"sorter" => FALSE,
						"filter" => FALSE,);
				$this->i_col_actions = $i++;

			}
		}
		$this->columns = $args;
	}

	private function _get_headers()
	{
		$cols = "";
		$cont = 0;
		foreach ($this->columns as $key => $val)
		{
			$cols[$key] = $this->_get_sorter($key, $val);
			$cont++;
		}
		$this->cont_fields = $cont;
		return $cols;
	}

	private function _get_sorter($key, $data)
	{
		if( $data["sorter"] ) {
			return '<a class="sorter-grid" href="'.$data["field"].'" title="Click para ordenar ASC/DESC.">'.$data["data"].'</a>';
		}
		else {
			return $data["data"];
		}
	}	

	private function _clear_id(  $str ){
		$str = str_replace(' ', '', $str);
		$str = str_replace('.', '', $str);
		$str = str_replace('/', '', $str);
		return $str;
	}
	/**
	 * Decode
	 **/
	private function decode($string)
	{
		return base64_decode(strtr($string, '%.~', '+/='));
	}

	/**
	 * Encode
	 **/
	private function encode($string)
	{
		return strtr(base64_encode($string), '+/=', '%.~');
	}

	/*
	 * BOTONES DE LAS TABLAS
	* */
	private function table_buttons($uri='', $title='', $ico, $attributes=''){
		if($attributes=='')
			$attributes = array("class" => "ttipL", 'title' => $title); 

		return anchor($this->url_site.$uri , '<span class="sprite '.$ico.'"></span>', $attributes);
	}

	private function _set_actions($id, $btn){

		$botones = "";
		
		if ( is_array($btn) ) {
			$opts = $this->CI->config->item('dg_'.$btn["button"]);
			$opts["id"] 	= 'dg_'.$btn["button"];
			$opts['icon'] 	= ( isset($btn['icon']) ) ? $btn['icon'] : $opts['icon'];
			$opts['title'] 	= ( isset($btn['title']) ) ? $btn['title'] : $opts['title'];
			$opts['url']	= ( isset($btn['url']) ) ? $btn['url'].'/'.$id : $this->url_site."/".$opts['url'].$id;
		}	else 	{
			$opts = $this->CI->config->item('dg_'.$btn);
			$opts["id"] = 'dg_'.$btn;
			$opts['url']	= $this->url_site.'/'.$opts['url'].$id;
		}

		$attr = array(
			"id" 	=> $opts["id"],
			"name" 	=> $opts["id"],
			"title" => $opts["title"],
			"class" => "actions-grid ".$opts["id"]
			);
		$botones .= anchor( $opts["url"], '<span class="fg-icon fg-icon-'. $opts["icon"].'"></span>', $attr);
		return array("data" => $botones, "class" => "fg-options" );
	}

	// --------------------------------------------------------------------

	/**
	 * Genera el data grid
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	private function define_limits(){
		//Definiendo los límites
		$page_segment = $this->CI->uri->segment($this->my_segment);
		if( ! isset($this->pagination["per_page"]) )
			$this->pagination["per_page"] = 10;
		$this->sql_query["limit"] 		= $this->pagination["per_page"];
		$this->sql_query["offset"] 		= (int)$page_segment;
		$this->sql_query["ord_field"]	= NULL;
		$this->sql_query["ord_direction"]= NULL;

		// Comprobamos si además de la paginación existen más variables.
		
		$vars_segment = $this->CI->uri->segment($this->my_segment+1);

		if( $vars_segment ){

		}

		$this->segment = $this->CI->uri->segment($this->my_segment);
		
	}

	// --------------------------------------------------------------------
	/**
	 * Inicializa las variables
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */

	public function initialize( $params = array() ){
		// Set parameters
		foreach ($params as $key => $value)
		{
			$this->$key = $value;
		}

		$this->_set_headers();
		$this->define_limits();
		
		$this->CI->fanci_model->initialize( $this->sql_query );
		$this->sql_string = $this->CI->fanci_model->set_query( $this->sql_query );

				// TEMPLATE POR DEFAULT DE LA TABLA DEL GRID
		$tmpl = array ( 
			'table_open'  => "<table class='fancigrid grid-border' id='". $this->grid_name ."'>" 
		);

		// Cargamos el template por default
		$this->CI->table->set_template($tmpl);

	}

	// --------------------------------------------------------------------

	/**
	 * Genera el data grid
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	public function generate() {
		//AGREGAMOS LOS ENCABEZADOS DE LA TABLA
		$this->CI->table->set_empty("&nbsp;"); 
		$this->CI->table->set_heading( $this->_get_headers() );
		//Realiza la consulta de los datos.
		$rows = $this->CI->fanci_model->select( $this->sql_string );

		// Inicia la magia: Agregando filas a la tabla.
		$totales = array();
		foreach( $rows as $key => $field ) {	
			$i = 0;
			$tmp_row = array();
			$id = $field[$this->prim_key];

			// ¿Ocultar primary key en el grid?
			if( $this->prim_key_hide ){
				unset( $field[$this->prim_key] );
			}

			// Agrego la columna de los checkbox si $col_check = true.
			if( $this->col_check )	{
				$tmp_row[$i] = array("data" => self::ROW_CHECK . $id. "' />", "class" =>"fg-select");
			}
			$i++;
			// Paso el resultado de la consulta a un array temporaL
			foreach ($this->columns as $cols) {
				if ( isset($cols["field"]) )	{ //Si no es el check
					$colField = $cols["field"];
					// PRUEBA
					$dt = $field[$colField];

					$tmp_row[$i] = $this->_parser_format( $cols["format"], $field[$colField] );
					
					if( $cols["format"] === 'money' ){
						if( isset($totales[$i]) )
							$totales[$i] += $field[$colField];
						else
							$totales[$i] = $field[$colField];
					} else {
						$totales[$i] = '&nbsp;';
					}
					$i++;
				}
			}
			// Agrego la columna de acciones si $col_actions = true.
			if( count($this->actions) > 0 )	{
				foreach ($this->actions as $btn) {
					$tmp_row[] = $this->_set_actions($id, $btn);
				}
			}

			$this->CI->table->row_id( $id );
			$this->CI->table->add_row($tmp_row);			
		}
		if( $this->autosum && isset($totales) ) {
			foreach ($totales as $key => $value) {
				if( is_numeric($value) ) {
					$monto = $this->_parser_format( "money", $value );
					$totales[$key] = array("data" => $monto, "class" => "totales" );
				} else {
					$totales[$key] = array("data" => "&nbsp;");
				}
			}
			foreach ($this->actions as $value) {
				$totales[] = array("data" => "&nbsp;"); 
			}
			$this->CI->table->set_footing($totales);
		}

		$this->pagination['per_page'] = $this->sql_query["limit"];
		$this->pagination['total_rows'] = $this->CI->fanci_model->count_rows();
		$this->pagination['uri_segment'] = $this->my_segment;

		$this->CI->pagination->initialize($this->pagination);
		

		/*
		$div_params = '<div id="grid_params" style="display:none">
						<div id = "url_site">'		.$this->url_site.'</div>
						<div id = "dg_url">'		.$this->pagination["base_url"].'</div>
						<div id = "dg_limit">'		.$this->uri["limit"].'</div>
						<div id = "dg_offset">'		.$this->uri["offset"].'</div>
						<div id = "dg_order">'		.$this->uri["field_order"].'</div>
						<div id = "dg_order_type">'	.$this->uri["order"].'</div>
						<div id = "dg_like_str">'	.$this->uri["like_string"].'</div>
						<div id = "dg_vars">'		.$this->uri["vars_url"].'</div>
						<div id = "dg_hash">'		.$this->uri["hash"].'</div>
						<div id = "dg_hash_init">'	.$this->uri["hash_init"].'</div>
					  </div> <!-- Close grid_params -->'."\n";
					  */

		$gridW 	 = '<div class="grid-container">'."\n";
		$gridW 	.= '<div class="the-table">'."\n";

		$fancigrid 	= $this->CI->table->generate();
		$fancigrid .= '</div> <!-- Close the-table-->'."\n";
		$footerW	 = '<div class="the-footer"><div class="numbers">'."\n";
		$footerW	.= $this->CI->pagination->create_links('buttons');
		$footerW 	.= '</div><!-- Close numbers -->'."\n";
		$footerW	.= '<div class="counter">'."\n";
		$footerW	.= $this->sel_page_size();
		$footerW	.= $this->label_counter();
		$footerW 	.= '</div><!-- Close counter -->'."\n";
		$footerW 	.= '</div> <!-- Close the-footer -->'."\n";
		$footerW 	.= '</div> <!-- Close grid-container -->'."\n";
		//$footerW 	.= $div_params;

		return $gridW . $fancigrid . $footerW;
	}

	// Genera el contador de resultados para el footer.
	private function label_counter(){

		$current = $this->sql_query["offset"]+1;
		$from 	 = $this->sql_query["offset"]+$this->pagination["per_page"];
		$total 	 = $this->CI->pagination->total_rows ;
		$texto = sprintf("del %d al %d de %d resultados.",$current, $from, $total);

		return $texto;
	}

	/**
	* Convierte un objeto y todos sus elementos a un arreglo.
	*
	* @param mixed $obj
	* @return array
	*/
	private function Obj2Array($obj) {
		if ( is_object ( $obj ) )
			$obj = get_object_vars ( $obj );
		if ( is_array ( $obj ) ) {
			foreach ( $obj as $key => $value )
				$obj[$key] =  Obj2Array( $obj [$key] );
		}
		return $obj;
	}
	private function sel_page_size() {
		$this->CI->load->helper('form');
		$options = array (
			'10' => "10",
			'20' => "20",
			'50' => "50",
			'100' => "100",
			'250' => "250",
			$this->CI->pagination->total_rows => "Todo",
			);
		$label  = form_label("Mostrar: ","sel_page_size");
		$select = form_dropdown('sel_page_size', $options, "{$this->pagination["per_page"]}",'id="sel_page_size" class="fg-page-size"');
		return $label.$select;
	}


	private function _parser_format( $str_format, $data ){
		$function = $this->_parser_format_function( $str_format, ":{", "}" );
		if( is_array( $function ) ) {
			$format = strtolower( $function["format"] );
			$params = $function["params"];
		} else {
			$format = strtolower( $function );
			$params = "";
		}

		switch ( $format ) {
			case 'center':
				$content = array("data" => $data, "style" => 'text-align:center;');
				break;
			case 'date':
				//Recibe la fecha en formato YYYY-MM-DD
				date_default_timezone_set('America/Chicago'); //Evita errores timezone
				$phpdate = strtotime( $data );
				$params["format"] = ( isset($params["format"]) )? $params["format"]:"d-m-Y";
				// Si se ha definido un setlocale
				if( isset($params["setlocale"]) )
					setlocale(LC_ALL, $params["setlocale"]); 
				$fecha = date($params["format"], $phpdate);
				$content = array("data" => $fecha, "style" => 'text-align:center;');
				break;
			case 'link':
				$texto = ( isset($params["text"]) ) ? $params["text"]:$data;
				$content = '<a href="'.prep_url($data).'">'.$texto.'</a>';
				break;
			case 'money':
				$content = '<span class="fgLeft">$</span><span class="fgRight">'.number_format($data,2,'.',',').'</span>';
				break;
			case 'percent':
				$content = '<div class="percent"><div class="bar" style="width: '.$data.'%">'.$data.'%</div></div>';
				break;
			case 'replace':
				if( isset($params[$data]) )
					$content = $params[$data];
				else
					$content = $data;
				break;
			case 'right':
				$content = array("data" => $data, "style" => 'text-align:right;');
				break;
			case 'FAIL':
				$content = "Fail format (x_x)";
			default:
				$content = $data;
				break;
		}
		return $content;
	}


	private function _parser_format_function($str, $str_start, $str_end){
		if( strstr($str, ':{') ) {
			$params	= strstr($str, $str_start);
		  	
		  	$tmp_format = trim( str_replace($params, '', $str) );
		  	$tmp_params = trim( str_replace(":{", "{", $params) );

		  	if( !json_decode($tmp_params) ) {
		  		$data["format"] = "FAIL";
		  		$data["params"] = "";
		  	}	else 	{
		  		$data["format"] = $tmp_format;
		  		$data["params"] = object2array( json_decode($tmp_params) );
		  	}

	  	}	else 	{
	  		$data = $str;
	  	}
	  	return $data;
	}

}