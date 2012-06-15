<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class Examples extends CI_Controller {

	function __construct(){
		parent::__construct();
	
		$this->load->library('fancigrid');
	}
	
	function index()
	{
		$sql["select"]  = "*";
		$sql["from"]    = "fancigrid";
		$sql["where"]   = "1";
				
		$config['base_url'] = base_url("examples/index");
		$config['per_page'] = 15;
		$config["num_links"] = 3;

		$columns = array(
			1 =>	array(
					"data" 	 => "EMPLEADO",
					"field"  => "nombre",
					"sorter" => TRUE,
					"order"	 => "ASC"),
			2 =>	array(
					"data" 	 => "DEPARTAMENTO",
					"field"  => "departamento",
					"sorter" => TRUE),
			3 =>	array(
					"data" 	 => "SUCURSAL",
					"field"  => "sucursal",
					"sorter" => TRUE),
			4 =>	array(
					"data" 	 => "E-MAIL",
					"field"  => "email",
					"sorter" => FALSE),
			5 =>	array(
					"data" 	 => "FECHA INGRESO",
					"field"  => "ingreso",
					"sorter" => TRUE,
					"filter" => FALSE,
					"format" => "date"
					),
			6 =>	array(
					"data" 	 => "EDAD",
					"field"  => "edad",
					"sorter" => TRUE,
					"filter" => FALSE,
					"format" => "center"),
			7 =>	array(
					"data" 	 => "RECORD VENTA",
					"field"  => "record",
					"sorter" => TRUE,
					"filter" => FALSE,
					"format" => "money"),
			8 =>	array(
					"data" 	 => "PORCENTAJE",
					"field"  => "avance",
					"sorter" => TRUE,
					"filter" => FALSE,
					"format" => "percent"),		
		);
		
		$params = array(
		            'url_site' 	=> base_url("examples"),
		            'columns' 	=> $columns,
		            'actions' 	=> array("view","edit","trash"),
		            'sql_query'	=> $sql,
		            'prim_key'	=> 'ID',
		            'my_segment'=> 3,
		            'pagination'=> $config,
		            'extra_params'=> 1,
		            'autosum'	=>	true
		);
		
		$toolbar = array(
					'add' => array(
							'text' => "Nuevo",
							'color'=> "blue",
							'url'  => base_url("proveedores/alta/"),
							'title' => "Agregar nuevo proveedor.",
							'class' => "left"
						),
					'trash'=> array(
							'text' => "Borrar",
							'color'=> "green",
							'url'  => "/borrar/",
							'title' => "Borrar registro(s) seleccionado(s).",
							'class' => "right hide"
						)
					);
		
		//Cargamos la librería ci_datagrid y pasamos los parametros.
	
		$this->fancigrid->initialize( $params );
		
		//$data["toolbar"] = $this->fancigrid->toolbar($toolbar);
		$grid = $this->fancigrid->generate();
		
		$data["grid"] = $grid;
		if(IS_AJAX){
			echo $grid;
		} else {
			$data['title'] = 'FanCI Grid - Table and Tablegrid for CodeIgniter'; // variable $title que se mostrará en la vista.
			$data["caption"] = 'Proveedores';

			$data["scripts"] 	= $this->load->view('scripts', $data, true);
			$data["contenido"] 	= $this->load->view('fancigrid/simple',$data, true); //Cargamos la vista llamada customers.
			$this->load->view('template', $data);
		}
	}
}