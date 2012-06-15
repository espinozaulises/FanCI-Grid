<?php
/**
 *
 * @author     Ulises Vázquez Espinoza <espinozaulises@gmail.com>
 * @license    BSD License
 * @version    v1.0 06/05/2012
 */

class Fanci_model extends CI_Model 
{
	public  $consulta;
	private $limit;
	private $offset;
	private $order;
	private $order_type;
	public $qry_un_limit;
	public $count_rows;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function initialize($params){
		$this->limit  = $params["limit"];
		$this->offset = $params["offset"];
		$this->order  = $params["field_order"];
		$this->order_type = $params["order"];
	}
	
	public function select($sql){
		return $this->db
					->query( $sql )
					->result_array();
	}
		
	public function total_rows($qry){
		
		$like = ( isset($qry["like"]) ) ? $qry["like"] : '';
		
		$this->db->select($qry["select"])
				 ->from($qry["from"])
				 ->where($qry["where"]);
		
		if( is_array($like) )
			$this->db->like($like[0],$like[1]);

		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	public function count_rows(){
		$query = $this->db->query($this->qry_un_limit);
		
		return $query->num_rows();
	}
	
	public function generate_query($sql)
	{
		$fields= '';
		$where = '1';
		$limit = '';
		$order = '';
		$like  = '';
				
		//if( is_array($sql) ) {
			
			if( isset($sql["where"]) )
				$where = " WHERE ".$sql["where"];
			//if( isset($sql["limit"]) ){
			//	$tmp = $sql["limit"];
			$limit = " LIMIT ".$this->offset.", ".$this->limit;
			//}
			if( isset($sql["like"]) && !empty($sql["like"]) ){
				$tmp = explode(":",$sql["like"]);
				if( ! empty($tmp[0]) and ! empty($tmp[1]) )
					$like = " AND ".$this->db->escape_str($tmp[0])." LIKE '%".$this->db->escape_str($tmp[1])."%'";
			}
			//if( isset($sql["order_by"]) ){
			//	$tmp = $sql["order_by"];
				$order = " ORDER BY ".$this->order." ".$this->order_type;
			//}
				
			$this->qry_un_limit = "SELECT ".$sql["select"]." FROM ".$sql["from"].$where.$like;
			$this->consulta = "SELECT ".$sql["select"]." FROM ".$sql["from"].$where.$like.$order.$limit;
	}
	
	public function set_query($sql)
	{
		$fields= '';
		$where = '1';
		$limit = '';
		$order = '';
		$like  = '';
	
		if( isset($sql["where"]) ){
			$where = " WHERE ".$sql["where"];
			$where = $this->my_sprintf($where, $sql["vars"]);
		}
			

		$limit = " LIMIT ".$this->offset.", ".$this->limit;
		//}
		if( isset($sql["like"]) && !empty($sql["like"]) ){
			$tmp = $sql["like"];
			if( ! empty($tmp[0]) and ! empty($tmp[1]) )
				$like = " AND ".$this->db->escape_str($tmp[0])." LIKE '%".$this->db->escape_str($tmp[1])."%'";
		}
		$order = " ORDER BY ".$this->order." ".$this->order_type;
	
		$this->qry_un_limit = "SELECT ".$sql["select"]." FROM ".$sql["from"].$where.$like;
		return "SELECT ".$sql["select"]." FROM ".$sql["from"].$where.$like.$order.$limit;
			
	}
	
	/**
	* Ignores php E_WARNING "sprintf(): Too few arguments".
	* If parameters is too fiew, then this function add extra empty parameter and try again.
	* Working in recursion.
	*
	* @param string $template
	* @param mixed $parameters
	* @return string
	*/
	public function my_sprintf($template, $parameters){
		if(!function_exists('handleError')){
			//initialize new error handler function
			function handleError($errno, $errstr, $errfile, $errline, array $errcontext){
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
			set_error_handler('handleError');
		}
	
		if(!is_array($parameters)){
			//manage parameters to allow string
			$parameters = array($parameters);
		}
	
		try{//trying to execute function. if warning is received, then add parameter
			eval('$return = sprintf($template, "'.implode('","', $parameters).'");');
			return $return;
		}catch(ErrorException $e){
			array_push($parameters, null);
			return my_sprintf($template, $parameters);
		}
	}
	
	
}