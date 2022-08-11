<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SB_Model extends CI_Model {

	public $tbl; #dbmodel
	public function __construct() {
		parent::__construct();
		#MAPEO DE LAS TABLAS DE LAS DB
		self::load_db_template();
	}

	/**
	 * Cargamos las tablas de la base de datos
	 * @return String Void
	 */
	private function load_db_template() {
		$technojet = get_var('mysql_db1');

		$this->tbl['actividades'] 			= "$technojet.cat_actividades";
		$this->tbl['categorias'] 			= "$technojet.cat_categorias";
		$this->tbl['monedas'] 				= "$technojet.cat_monedas";
		$this->tbl['unidades_medida'] 		= "$technojet.cat_unidades_medida";
		$this->tbl['tipo_consumibles'] 		= "$technojet.cat_tipo_consumibles";
		$this->tbl['tipos_productos'] 		= "$technojet.cat_tipos_productos";
		$this->tbl['usos'] 					= "$technojet.cat_usos";
		$this->tbl['tipos_entrada'] 		= "$technojet.cat_tipos_entrada";
		$this->tbl['sitio'] 				= "$technojet.cat_sitios";

		$this->tbl['menu'] 					= "$technojet.sys_menu";
		$this->tbl['perfiles'] 				= "$technojet.sys_perfiles";
		$this->tbl['usuarios'] 				= "$technojet.sys_usuarios";

		$this->tbl['almacen_requisiciones'] 	= "$technojet.tbl_almacen_requisiciones";
		$this->tbl['bitacora_actividades'] 		= "$technojet.tbl_bitacora_actividades";
		$this->tbl['productos'] 				= "$technojet.tbl_productos";
		$this->tbl['vales_activos'] 			= "$technojet.tbl_vales_activos";
		$this->tbl['vales_activos_productos'] 	= "$technojet.tbl_vales_activos_productos";
		$this->tbl['vales_entrada'] 			= "$technojet.tbl_vales_entrada";
		$this->tbl['vales_entrada_productos'] 	= "$technojet.tbl_vales_entrada_productos";
		$this->tbl['vales_salida'] 				= "$technojet.tbl_vales_salida";
		$this->tbl['vales_salida_productos'] 	= "$technojet.tbl_vales_salida_productos";
		$this->tbl['vales_almacen'] 			= "$technojet.tbl_vales_almacen";
		$this->tbl['vales_estatus'] 			= "$technojet.tbl_vales_estatus";
		$this->tbl['ve_tipos_entrada'] 			= "$technojet.tbl_ve_tipos_entrada";
		$this->tbl['requisiciones'] 			= "$technojet.tbl_requisiciones";
		$this->tbl['requisiciones_productos'] 	= "$technojet.tbl_requisiciones_productos";
		$this->tbl['reportes_sistemas'] 		= "$technojet.tbl_reportes_sistemas";
		$this->tbl['reportes_sistemas_estado'] 	= "$technojet.tbl_reportes_sistemas_estado";
		$this->tbl['reportes_sistemas_productos'] = "$technojet.tbl_reportes_sistemas_productos";
		$this->tbl['ventas_cotizaciones'] = "$technojet.tbl_ventas_cotizaciones";

		$this->tbl['tarea_estatus'] 		= "$technojet.cat_tarea_estatus";
		$this->tbl['tarea_prioridad'] 		= "$technojet.cat_tarea_prioridad";
		$this->tbl['tareas'] 				= "$technojet.tbl_tareas";
		$this->tbl['tareas_participantes'] 	= "$technojet.tbl_tareas_participantes";
		$this->tbl['tareas_comentarios'] 	= "$technojet.tbl_tareas_comentarios";

		#VISTAS
		$this->tbl['vw_almacenes_solicitantes'] 		= "$technojet.vw_almacenes_solicitantes";
		$this->tbl['vw_bitacora_actividades'] 			= "$technojet.vw_bitacora_actividades";
		$this->tbl['vw_departamentos_encargados_surtir']= "$technojet.vw_departamentos_encargados_surtir";
		$this->tbl['vw_departamentos_solicitantes'] 	= "$technojet.vw_departamentos_solicitantes";
		$this->tbl['vw_tipos_requisiciones'] 			= "$technojet.vw_tipos_requisiciones";
	}
}
