<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:  Ci DataGrid Config
*
* Author: Ulises Vázquez Espinoza
* 		  espinoza.ulises@gmail.com
*
* Added Awesomeness: Phil Sturgeon
*
* Location: 
*
* Created:  
*
* Description:  
*
*/

	$config["text-commands"] = "OPCIONES";

/*
 * Buttons Toolbar
 */

	/*
	 * Buttons table
	 */
	/* Edit */
	$config["dg_edit"]["id"] 	= "dg_editar";
	$config["dg_edit"]["title"] = "Actualizar datos.";
	$config["dg_edit"]["url"] 	= "editar/";
	$config["dg_edit"]["icon"] 	= "edit";
	
	/* Delete */
	$config["dg_trash"]["id"] 		= "dg_borrar";
	$config["dg_trash"]["title"] 	= "Borrar registro.";
	$config["dg_trash"]["url"] 	= "borrar/";
	$config["dg_trash"]["icon"] 	= "trash1";
	
	/* Preview */
	$config["dg_view"]["id"] 	= "dg_ver";
	$config["dg_view"]["title"] = "Ver datos y/o historial.";
	$config["dg_view"]["url"] 	= "vista/";
	$config["dg_view"]["icon"] 	= "preview";

	
 
