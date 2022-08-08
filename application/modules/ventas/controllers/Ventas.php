<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends SB_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('technojet/Almacenes_vales_model', 'db_av');
		$this->load->model('technojet/Ventas_productos_model', 'db_vp');
		$this->load->model('technojet/Productos_model', 'db_productos');
		$this->load->model('ventas/Vales_productos_model', 'db_vales_pro');
		$this->load->model('technojet/Almacen_requisiciones_model', 'db_ar');
	}

	public function cotizaciones() {
		$dataView['tpl-tbl-almacen']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-almacenes');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-detallado');

		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'cotizaciones', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    	$includes['modulo']['js'][] = ['name'=>'reporte-mensual', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    	$includes['modulo']['js'][] = ['name'=>'reporte-detallado', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/cotizaciones/cotizaciones_view',$dataView, $includes);
	}

	public function get_modal_add_cotizacion(){
		//cargar contenido de vistas / CATALOGOS

		//cargar JS's / INTERACCIÓN

		//Carga de vista principal
		$sqlWhere['tipo'] = 'ENTRADA';
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada();
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		#OBTENEMOS LAS REQUISICIONES EXISTENTES
		$requisiciones = $this->db_ar->get_requisiciones_select2();
		$dataView['requisiciones'] = $requisiciones;

		#OBTENEMOS EL CONSECUTIVO DEL VALE DE ENTRADA
		$folio = $this->db_vales_pro->get_ultimo_vale_entrada();
		$dataView = array_merge($dataView, $folio);
		// $dataView['vales-entrada'] = $this->db_vp->get_vales_entrada([], TRUE);
		
		#OBTENEMOS EL CONSECUTIVO DEL VALE DE REQUISICION DE MATERIAL
		$folio = $this->db_ar->get_ultimo_requisicion();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-entrada', $dataView, FALSE);
		//modal-add-producto-entrada
	}
	
	public function mostrador() {
		$dataView['tpl-tbl-mostrador']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-mostrador');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-detallado');

		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'mostrador', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/pedidos-internos/mostrador/mostrador_view', $dataView, $includes);
	}

	public function get_modal_add_mostrador(){
		$this->parser_view('ventas/pedidos-internos/mostrador/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}
	public function get_modal_add_mostrador_product(){
		$this->parser_view('ventas/pedidos-internos/mostrador/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}


	public function factura() {
		$dataView['tpl-tbl-factura']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-factura');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-detallado');

		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'factura', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/pedidos-internos/factura/factura_view', $dataView, $includes);
	}

	public function get_modal_add_factura(){
		$this->parser_view('ventas/pedidos-internos/factura/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}
	public function get_modal_add_factura_product(){
		$this->parser_view('ventas/pedidos-internos/factura/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	

	// public function index() {
	// 	$dataView['usos'] = $this->db_catalogos->get_usos();
	// 	$dataView['tpl-tbl-reporte-mensual'] 		= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-mensual');
	// 	$dataView['tpl-tbl-reporte-detallado'] 		= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-detallado');
	// 	$dataView['tpl-tbl-almacen']				= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-almacenes');
	// 	$dataView['tpl-tbl-almacen']				= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-almacenes');

	// 	// $dataView['tpl-tbl-factura']				= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-factura');
	// 	// $dataView['tpl-tbl-mostrador']				= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-mostrador');


	// 	$includes 	= get_includes_vendor(['moment', 'dataTables', 'DTRowGroup', 'jQValidate']);
	// 	$pathJS 	= get_var('path_js');
    //     $includes['modulo']['js'][] = ['name'=>'cotizaciones', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    //     $includes['modulo']['js'][] = ['name'=>'reporte-mensual', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    //     $includes['modulo']['js'][] = ['name'=>'reporte-detallado', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

    //     // $includes['modulo']['js'][] = ['name'=>'factura', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    //     // $includes['modulo']['js'][] = ['name'=>'mostrador', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

	// 	$this->load_view('ventas/cotizaciones/almacen_view', $dataView, $includes);
	// 	// $this->load_view('ventas/pedidos-internos/factura/mostrador_view', $dataView, $includes);
	// 	// $this->load_view('ventas/pedidos-internos/mostrador/mostrador_view', $dataView, $includes);
	// }

	public function get_productos_almacenes() {
		if ($_POST['id_categoria'] || ($_POST['id_uso']==5)) {
			$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);
			if ($sqlWhere['id_uso']!=5) {
				$sqlWhere['id_vale_estatus_entrada'] = 1;
				$sqlWhere['id_vale_estatus_salida'] = 3;
				$response = $this->db_productos->get_productos_info($sqlWhere);
			
			#PRODUCTOS DE ALMACENES ACTIVOS
			} else {
				$response = $this->db_productos->get_productos_activos_info($sqlWhere);
			}

		} else $response = [];

		echo json_encode($response);
	}

	public function get_productos_vales_entrada() {
		$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);

		if ($sqlWhere['id_uso'] || $sqlWhere['id_categoria']) {
			$sqlWhere['tipo'] = 'ENTRADA';
			$productos = ($sqlWhere['id_uso']==5)
				? $this->db_vp->get_productos_vales_activos($sqlWhere)
				: $this->db_vp->get_productos_vales_entrada($sqlWhere);
			
		} else $productos = [];

		echo json_encode($productos);
	}

	public function get_modal_add_vale_entrada() {
		$sqlWhere['tipo'] = 'ENTRADA';
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada();
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		#OBTENEMOS LAS REQUISICIONES EXISTENTES
		$requisiciones = $this->db_ar->get_requisiciones_select2();
		$dataView['requisiciones'] = $requisiciones;

		#OBTENEMOS EL CONSECUTIVO DEL VALE DE ENTRADA
		$folio = $this->db_vales_pro->get_ultimo_vale_entrada();
		$dataView = array_merge($dataView, $folio);

		($this->input->post('id_uso')==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-entrada-activos', $dataView, true)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-entrada', $dataView, true);
	}

	public function get_modal_vale_salida() {
		$sqlWhere['tipo'] = 'SALIDA';
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada();
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		#OBTENEMOS EL CONSECUTIVO DEL VALE DE SALIDA
		$folio = $this->db_vales_pro->get_ultimo_vale_salida();
		$dataView = array_merge($dataView, $folio);

		($this->input->post('id_uso')==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-salida-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-salida', $dataView, FALSE);
	}

	public function get_productos_vales_salida() {
		$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);

		if ($sqlWhere['id_uso'] || $sqlWhere['id_categoria']) {
			$sqlWhere['tipo'] = 'SALIDA';
			$productos = ($sqlWhere['id_uso']==5)
				? $this->db_vp->get_productos_vales_activos($sqlWhere)
				: $this->db_vp->get_productos_vales_salida($sqlWhere);

		} else $productos = [];

		echo json_encode($productos);
	}

	public function get_modal_add_producto_entrada() {
		$dataView['tipos-productos'] = $this->db_catalogos->get_tipos_productos_min();

		if($this->input->post('id_uso')==5) {
			$unidades_medida = $this->db_catalogos->get_unidades_medida_min();
			$dataView['unidades-medida'] = $unidades_medida;
			$dataView['monedas'] 		= $this->db_catalogos->get_monedas_min();

			$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-entrada-activos', $dataView, FALSE);

		} else $this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-entrada', $dataView, FALSE);
	}

	public function get_modal_add_producto_salida() {
		$dataView['tipos-productos'] = $this->db_catalogos->get_tipos_productos_min();

		if($this->input->post('id_uso')==5) {
			$unidades_medida = $this->db_catalogos->get_unidades_medida_min();
			$dataView['unidades-medida'] = $unidades_medida;
			$dataView['monedas'] 		= $this->db_catalogos->get_monedas_min();

			$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-salida-activos', $dataView, FALSE);

		} else $this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-salida', $dataView, FALSE);
	}

	public function process_save_productos_entrada() {
		$response = ($this->input->post('id_uso') == 5)
			? self::save_productos_activos('ENTRADA')
			: self::save_productos_entrada();

		echo json_encode($response);
	}

	public function save_productos_entrada() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_categoria'
				,'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'id_requisicion'
				,'concepto_entrada'
				,'vale_salida_correspondiente'
				,'id_ve_tipo_entrada'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$insert = $this->db_vales_pro->insert_vales_entrada($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_entrada']= $insert;
			$dataView['tipo_entrada']= $this->input->post('tipo_entrada');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_entrada' 			=> $insert
					,'id_producto' 				=> $producto['id_producto']
					,'cantidad' 				=> $producto['cantidad']
					,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
					,'referencia_entrada' 		=> $producto['referencia_entrada']
					,'id_usuario_insert' 		=> $this->session->userdata('id_usuario')
					,'timestamp_insert' 		=> timestamp()
				];
				$sqlDataBatch[] = $sqlDataPro;

				#DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insert = $this->db_vales_pro->insert_vales_entrada_productos($sqlDataBatch);
				$insert OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= "ha creado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría:".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function save_productos_activos($tipo) {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA|SALIDA
			$sqlData = $this->input->post([
				 'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'concepto'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['tipo'] = strtoupper($tipo);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['autorizo'] = strtoupper($this->input->post('autorizo'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);

			switch ($sqlData['tipo']) {
				case 'ENTRADA': 
					$sqlData['id_requisicion'] = $this->input->post('id_requisicion');
					$sqlData['id_ve_tipo_entrada'] = $this->input->post('id_ve_tipo_entrada');
				break;
				case 'SALIDA': $sqlData['pedido_interno'] = $this->input->post('pedido_interno'); break;
			}

			$insert = $this->db_vales_pro->insert_vales_activos($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_activo']= $insert;
			$dataView['tipo_entrada']= $this->input->post('tipo_entrada');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_activo' 	=> $insert
					,'id_tipo_producto' => $producto['id_tipo_producto']
					,'id_unidad_medida' => $producto['id_unidad_medida']
					,'no_parte' 		=> $producto['no_parte']
					,'descripcion' 		=> $producto['descripcion']
					,'cantidad' 		=> $producto['cantidad']
					,'no_serie' 		=> $producto['no_serie']
					,'estado_producto' 	=> $producto['estado_producto']
					,'id_moneda' 		=> $producto['id_moneda']
					,'costo' 			=> $producto['costo']
				];
				$sqlDataBatch[] = $sqlData;

				#DATA PARA EL PDF
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$sqlData['tipo_producto'] = $producto['tipo_producto'];
				$sqlData['moneda'] 			= $producto['moneda'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_activos_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$tipo = strtolower($tipo);
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
				,'content_file' => $this->parser_view("ventas/cotizaciones/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= ($tipo=='entrada')
				? "ha creado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
				: "ha creado un vale de salida almacén/almacenes con uso: $_POST[uso]";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_activos', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_save_productos_salida() {
		$response = ($this->input->post('id_uso')==5)
			? self::save_productos_activos('SALIDA')
			: self::save_productos_salida();

		echo json_encode($response);
	}

	public function save_productos_salida() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_categoria'
				,'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'pedido_interno'
				,'concepto_salida'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$insert = $this->db_vales_pro->insert_vales_salida($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_salida']= $insert;
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_salida' 	=> $insert
					,'id_producto' 		=> $producto['id_producto']
					,'cantidad' 		=> $producto['cantidad']
					,'referencia_salida'=> $producto['referencia_salida']
					,'id_usuario_insert'=> $this->session->userdata('id_usuario')
					,'timestamp_insert' => timestamp()
				];
				$sqlDataBatch[] = $sqlData;

				#DATA PARA EL PDF
				$sqlData['no_parte'] 	= $producto['no_parte'];
				$sqlData['descripcion'] = $producto['descripcion'];
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_salida_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= "ha creado un vale de salida almacén/almacenes con uso: $_POST[uso] y categoría:".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_remove_vale_entrada() {
		try {
			$this->db->trans_begin();

			$id_uso = $this->input->post('id_uso');
			if ($id_uso!=5) {
				$sqlWhere 	= $this->input->post(['id_vale_entrada']);
				#ELIMIANCIÓN DEL VALE DE ENTRADA
				$update = $this->db_vales_pro->update_vales_entrada(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA
				$update = $this->db_vales_pro->update_vales_entrada_productos(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				$actividad 		= "ha eliminado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría: ".$_POST['categoria'];
				$data_change 	= ['delete'=>['oldData'=>$_POST]];
				registro_bitacora_actividades($sqlWhere['id_vale_entrada'], 'tbl_vales_entrada', $actividad, $data_change);

			} else self::remove_vale_activos('ENTRADA');

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	private function remove_vale_activos($tipo) {
		$sqlWhere 	= $this->input->post(['id_vale_activo']);
		#ELIMIANCIÓN DEL VALE DE ENTRADA ACTIVOS
		$update = $this->db_vales_pro->update_vales_activos(['activo'=>0], $sqlWhere);
		$update OR set_exception();

		#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA ACTIVOS
		$update = $this->db_vales_pro->update_vales_activos_productos(['activo'=>0], $sqlWhere);
		$update OR set_exception();

		$actividad = ($tipo=='ENTRADA')
			? "ha eliminado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
			: "ha eliminado un vale de salida en almacén/almacenes con uso: $_POST[uso]";
		$data_change 	= ['delete'=>['oldData'=>$_POST]];
		registro_bitacora_actividades($sqlWhere['id_vale_activo'], 'tbl_vales_activos', $actividad, $data_change);
	}

	public function process_build_pdf_vale_entrada() {
		$response = ($this->input->post('id_uso') == 5)
			? self::build_pdf_vale_activos('ENTRADA')
			: self::build_pdf_vale_entrada();

		echo json_encode($response);
	}

	public function build_pdf_vale_entrada() {
		$sqlWhere 	= $this->input->post(['id_vale_entrada']);
		$productos 	= $this->db_vp->get_productos_vales_entrada($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'cantidad' 				=> $producto['cantidad']
				,'no_parte' 				=> $producto['no_parte']
				,'unidad_medida' 			=> $producto['unidad_medida']
				,'descripcion' 				=> $producto['descripcion']
				,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
				,'referencia_entrada' 		=> $producto['referencia_entrada']
			];
		}

		$dataView = [
			 'id_vale_entrada' 				=> $producto['id_vale_entrada']
			,'cliente' 						=> $producto['cliente']
			,'id_requisicion' 				=> $producto['id_requisicion']
			,'requisicion' 					=> $producto['requisicion']
			,'custom_fecha' 				=> $producto['custom_fecha']
			,'concepto_entrada' 			=> $producto['concepto_entrada']
			,'vale_salida_correspondiente' 	=> $producto['vale_salida_correspondiente']
			,'observaciones' 				=> $producto['observaciones']
			,'vale_almacen' 				=> $producto['vale_almacen']
			,'recibio' 						=> $producto['recibio']
			,'entrego' 						=> $producto['entrego']
			,'vo_bo' 						=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
			,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function build_pdf_vale_activos($tipo) {
		$sqlWhere 	= $this->input->post(['id_vale_activo']);
		$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'tipo_producto' 	=> $producto['tipo_producto']
				,'unidad_medida' 	=> $producto['unidad_medida']
				,'no_parte' 		=> $producto['no_parte']
				,'descripcion' 		=> $producto['descripcion']
				,'cantidad' 		=> $producto['cantidad']
				,'no_serie' 		=> $producto['no_serie']
				,'estado_producto' 	=> $producto['estado_producto']
				,'costo' 			=> $producto['costo']
				,'moneda' 			=> $producto['moneda']
			];
		}

		$dataView = [
			 'id_vale_activo' 	=> $producto['id_vale_activo']
			,'cliente' 			=> $producto['cliente']
			,'id_requisicion' 	=> $producto['id_requisicion']
			,'requisicion' 		=> $producto['requisicion']
			,'custom_fecha' 	=> $producto['custom_fecha']
			,'concepto' 		=> $producto['concepto']
			,'observaciones' 	=> $producto['observaciones']
			,'vale_almacen' 	=> $producto['vale_almacen']
			,'recibio' 			=> $producto['recibio']
			,'autorizo' 		=> $producto['autorizo']
			,'entrego' 			=> $producto['entrego']
			,'vo_bo' 			=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$tipo = strtolower($tipo);
		$settings = array(
			 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
			,'content_file' => $this->parser_view("ventas/almacenes/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function get_unidades_medida_productos() {
		$sqlWhere = $this->input->post(['id_categoria', 'id_tipo_producto']);
		$response = $this->db_productos->get_unidad_medida_tipo_producto($sqlWhere);

		echo json_encode($response);
	}

	public function get_productos_por_tipo() {
		$sqlWhere = $this->input->post(['id_categoria', 'id_tipo_producto', 'id_unidad_medida']);
		$response = $this->db_productos->get_productos_por_tipo($sqlWhere);

		echo json_encode($response);
	}

	public function get_modal_edit_vale_entrada() {
		$id_uso = $this->input->post('id_uso');
		$dataView = $this->input->post();

		$dataEncription = ($id_uso==5) #VALES ACTIVOS
			? json_encode($this->input->post(['id_vale_activo']))
			: json_encode($this->input->post(['id_vale_entrada', 'id_categoria', 'id_uso']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		$sqlWhere['tipo'] = 'ENTRADA';
		$sqlWhere['selected'] = $this->input->post('id_vale_almacen');
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$sqlWhere['selected'] = $this->input->post('id_vale_estatus');
		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$sqlWhere['selected'] = $this->input->post('id_ve_tipo_entrada');
		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada($sqlWhere);
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		$sqlWhere['selected'] = $this->input->post('id_requisicion');
		$requisiciones = $this->db_ar->get_requisiciones_select2($sqlWhere);
		$dataView['requisiciones'] = $requisiciones;

		if ($id_uso==5) { #VALES ACTIVOS
			$sqlWhere 	= $this->input->post(['id_vale_activo']);
			$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);

		} else {
			$sqlWhere 	= $this->input->post(['id_vale_entrada']);
			$productos 	= $this->db_vp->get_productos_vales_entrada($sqlWhere);
		}
		$dataView['list-productos'] = json_encode($productos);
		// debug($dataView);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_vale_estatus'], $dataView['estatus']);
		unset($dataView['id_vale_almacen'], $dataView['vale_almacen']);
		unset($dataView['id_ve_tipo_entrada']);
		unset($dataView['id_requisicion'], $dataView['folio_requisicion']);

		($id_uso ==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-editar-entrada-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-editar-entrada', $dataView, FALSE);
	}

	public function process_update_productos_entrada() {
		$response = ($this->input->post('id_uso')==5)
			? self::update_productos_activos('ENTRADA')
			: self::update_productos_entrada();

		echo json_encode($response);
	}

	public function update_productos_entrada() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_vale_estatus'
				,'cliente'
				,'id_requisicion'
				,'concepto_entrada'
				,'vale_salida_correspondiente'
				,'id_ve_tipo_entrada'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$sqlWhere = $this->input->post(['id_vale_entrada', 'id_categoria', 'id_uso']);
			$update = $this->db_vales_pro->update_vales_entrada($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_entrada']= $this->input->post('id_vale_entrada');
			$dataView['tipo_entrada'] 	= $this->input->post('tipo_entrada');
			$dataView['vale_almacen'] 	= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_vale_entrada_producto'));
			$sqlWhere = $this->input->post(['id_vale_entrada']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_entrada_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_entrada' 			=> $this->input->post('id_vale_entrada')
					,'id_producto' 				=> $producto['id_producto']
					,'cantidad' 				=> $producto['cantidad']
					,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
					,'referencia_entrada' 		=> $producto['referencia_entrada']
					,'id_usuario_insert' 		=> $this->session->userdata('id_usuario')
					,'timestamp_insert' 		=> timestamp()
				];

				if (!isset($producto['id_vale_entrada_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				#DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_entrada_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría: ".$_POST['categoria'];
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_entrada'], 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}

	public function update_productos_activos($tipo) {
		try {
			$this->db->trans_begin();
			$tipo = strtoupper($tipo);
			#GUARDAMOS VALE DE ACTIVOS
			$sqlData = $this->input->post([
				 'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'concepto'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['autorizo'] = strtoupper($this->input->post('autorizo'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);

			switch ($tipo) {
				case 'ENTRADA': 
					$sqlData['id_requisicion'] = $this->input->post('id_requisicion');
					$sqlData['id_ve_tipo_entrada'] = $this->input->post('id_ve_tipo_entrada');
				break;
				case 'SALIDA': $sqlData['pedido_interno'] = $this->input->post('pedido_interno'); break;
			}

			$sqlWhere = $this->input->post(['id_vale_activo']);
			$update = $this->db_vales_pro->update_vales_activos($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_activo'] = $this->input->post('id_vale_activo');
			$dataView['tipo_entrada'] 	= $this->input->post('tipo_entrada');
			$dataView['vale_almacen'] 	= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_vale_activo_producto'));
			$sqlWhere = $this->input->post(['id_vale_activo']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_activos_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_activo' 	=> $this->input->post('id_vale_activo')
					,'id_tipo_producto' => $producto['id_tipo_producto']
					,'id_unidad_medida' => $producto['id_unidad_medida']
					,'no_parte' 		=> $producto['no_parte']
					,'descripcion' 		=> $producto['descripcion']
					,'cantidad' 		=> $producto['cantidad']
					,'no_serie' 		=> $producto['no_serie']
					,'estado_producto' 	=> $producto['estado_producto']
					,'id_moneda' 		=> $producto['id_moneda']
					,'costo' 			=> $producto['costo']
				];

				if (!isset($producto['id_vale_activo_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				#DATA PARA EL PDF
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$sqlDataPro['moneda'] 			= $producto['moneda'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_activos_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$tipo = strtolower($tipo);
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
				,'content_file' => $this->parser_view("ventas/cotizaciones/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad = ($tipo == 'entrada')
				? "ha editado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
				: "ha editado un vale de salida en almacén/almacenes con uso: $_POST[uso]";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_activo'], 'tbl_vales_activos', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_remove_vale_salida() {
		try {
			$this->db->trans_begin();

			$id_uso = $this->input->post('id_uso');
			if ($id_uso!=5) {
				$sqlWhere 	= $this->input->post(['id_vale_salida']);
				#ELIMIANCIÓN DEL VALE DE SALIDA
				$update = $this->db_vales_pro->update_vales_salida(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE SALIDA
				$update = $this->db_vales_pro->update_vales_salida_productos(['activo'=>0], $sqlWhere);
				$update OR set_exception();
			} else self::remove_vale_activos('SALIDA');

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_build_pdf_vale_salida() {
		$response = ($this->input->post('id_uso') == 5)
			? self::build_pdf_vale_activos('SALIDA')
			: self::build_pdf_vale_salida();

		echo json_encode($response);
	}

	public function build_pdf_vale_salida() {
		$sqlWhere 	= $this->input->post(['id_vale_salida']);
		$productos 	= $this->db_vp->get_productos_vales_salida($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'cantidad' 				=> $producto['cantidad']
				,'no_parte' 				=> $producto['no_parte']
				,'unidad_medida' 			=> $producto['unidad_medida']
				,'descripcion' 				=> $producto['descripcion']
				,'referencia_salida' 		=> $producto['referencia_salida']
			];
		}

		$dataView = [
			 'id_vale_salida' 				=> $producto['id_vale_salida']
			,'cliente' 						=> $producto['cliente']
			,'pedido_interno' 				=> $producto['pedido_interno']
			,'custom_fecha' 				=> $producto['custom_fecha']
			,'concepto_salida' 				=> $producto['concepto_salida']
			,'observaciones' 				=> $producto['observaciones']
			,'vale_almacen' 				=> $producto['vale_almacen']
			,'recibio' 						=> $producto['recibio']
			,'entrego' 						=> $producto['entrego']
			,'vo_bo' 						=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
			,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function get_modal_edit_vale_salida() {
		$id_uso = $this->input->post('id_uso');
		$dataView = $this->input->post();

		$dataEncription = ($id_uso==5) #VALES ACTIVOS
			? json_encode($this->input->post(['id_vale_activo']))
			: json_encode($this->input->post(['id_vale_salida', 'id_categoria', 'id_uso']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		$sqlWhere['tipo'] = 'SALIDA';
		$sqlWhere['selected'] = $this->input->post('id_vale_almacen');
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$sqlWhere['selected'] = $this->input->post('id_vale_estatus');
		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		if ($id_uso==5) { #VALES ACTIVOS
			$sqlWhere 	= $this->input->post(['id_vale_activo']);
			$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);
		} else {
			$sqlWhere 	= $this->input->post(['id_vale_salida']);
			$productos 	= $this->db_vp->get_productos_vales_salida($sqlWhere);
		}
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_vale_estatus'], $dataView['estatus']);
		unset($dataView['id_vale_almacen'], $dataView['vale_almacen']);

		($id_uso ==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-editar-salida-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-editar-salida', $dataView, FALSE);
	}

	public function process_update_productos_salida() {
		$response = ($this->input->post('id_uso')==5)
			? self::update_productos_activos('SALIDA')
			: self::update_productos_salida();

		echo json_encode($response);
	}

	public function update_productos_salida() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE SALIDA
			$sqlData = $this->input->post([
				 'id_vale_estatus'
				,'cliente'
				,'pedido_interno'
				,'concepto_salida'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$sqlWhere = $this->input->post(['id_vale_salida', 'id_categoria', 'id_uso']);
			$update = $this->db_vales_pro->update_vales_salida($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_salida']= $this->input->post('id_vale_salida');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LISTA
			$productosActivos = array_filter(array_column($productos, 'id_vale_salida_producto'));
			$sqlWhere = $this->input->post(['id_vale_salida']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_salida_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_salida' 	=> $this->input->post('id_vale_salida')
					,'id_producto' 		=> $producto['id_producto']
					,'cantidad' 		=> $producto['cantidad']
					,'referencia_salida'=> $producto['referencia_salida']
					,'id_usuario_insert'=> $this->session->userdata('id_usuario')
					,'timestamp_insert' => timestamp()
				];

				if (!isset($producto['id_vale_salida_producto'])) {
					$sqlDataBatch[] = $sqlData;
				}

				#DATA PARA EL PDF
				$sqlData['no_parte'] 	= $producto['no_parte'];
				$sqlData['descripcion'] = $producto['descripcion'];
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insert = $this->db_vales_pro->insert_vales_salida_productos($sqlDataBatch);
				$insert OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}
}

/* End of file Almacen.php */
/* Location: ./application/modules/almacen/controllers/Almacenes.php */