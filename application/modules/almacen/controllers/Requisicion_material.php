<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Requisicion_material extends SB_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->load->model('technojet/Almacen_requisiciones_model', 'db_ar');
		$this->load->model('almacen/Vales_productos_model', 'db_vp');
	}

	public function index() {
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS 	= get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'material-requisicion', 'dirname'=>"$pathJS/almacen", 'fulldir'=>TRUE];

		$this->load_view('almacen/requisicion-material/requisicion-material-view', [], $includes);
	}

	public function get_productos_requisicion_material() {
		$response = $this->db_ar->get_requisiciones();

		$tplAcciones = $this->parser_view('almacen/requisicion-material/tpl/tpl-acciones');
		foreach ($response as &$requisicion) {
			$requisicion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_requisicion() {
		#TIPOS DE REQUISICION
		$dataView['tipos-requisiciones'] = $this->db_ar->get_tipos_requisiciones();
		$dataView['departamentos-solicitantes'] = $this->db_ar->get_departamentos_solicitantes();
		$dataView['almacenes-solicitantes'] = $this->db_ar->get_almacenes_solicitantes();
		$dataView['departamento-encargado-surtir'] = $this->db_ar->get_departamentos_encargados_surtir();
		// $dataView['vales-entrada'] = $this->db_vp->get_vales_entrada([], TRUE);
		
		#OBTENEMOS EL CONSECUTIVO DEL VALE DE REQUISICION DE MATERIAL
		$folio = $this->db_ar->get_ultimo_requisicion();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('almacen/requisicion-material/tpl/modal-nueva-requisicion', $dataView, FALSE);
	}

	public function get_modal_add_producto_requisicion() {
		$dataView['tipos-productos'] = $this->db_catalogos->get_tipos_productos_min();

		$unidades_medida = $this->db_catalogos->get_unidades_medida_min();
		$dataView['unidades-medida'] = $unidades_medida;

		$this->parser_view('almacen/requisicion-material/tpl/modal-add-producto-requisicion', $dataView, FALSE);
	}

	public function process_save_productos_requisicion() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'fecha_solicitud'
				,'id_tipo_requisicion'
				,'pedido_interno'
				,'cliente'
				// ,'fecha_entrega'
				// ,'id_vale_entrada'
				,'id_departamento_solicitante'
				,'id_almacen_solicitante'
				// ,'tipo_entrega'
				,'id_departamento_encargado_surtir'
				,'observaciones'
				,'nombre_almacen'
				,'nombre_compras'
				,'nombre_autorizacion'
			]);
			$sqlData['persona_solicitante'] = strtoupper($this->input->post('persona_solicitante'));
			$sqlData['encargado_almacen'] 	= strtoupper($this->input->post('encargado_almacen'));
			$sqlData['nombre_almacen'] 		= strtoupper($this->input->post('nombre_almacen'));
			$sqlData['nombre_compras'] 		= strtoupper($this->input->post('nombre_compras'));
			$sqlData['nombre_autorizacion'] = strtoupper($this->input->post('nombre_autorizacion'));
			$insert = $this->db_ar->insert_requisiciones($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $insert;
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_requisicion' 		=> $insert
					,'id_tipo_producto' 	=> $producto['id_tipo_producto']
					,'id_producto' 			=> $producto['id_producto']
					,'cantidad' 			=> $producto['cantidad']
					,'id_usuario_insert' 	=> $this->session->userdata('id_usuario')
					,'timestamp_insert' 	=> timestamp()
				];
				$sqlDataBatch[] = $sqlData;

				#DATA PARA EL PDF
				$sqlData['no_parte'] 	= $producto['no_parte'];
				$sqlData['descripcion'] = $producto['descripcion'];
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$sqlData['tipo_producto'] = $producto['tipo_producto'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_ar->insert_requisiciones_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$dataView['folios_ve'] 		= '';
			$dataView['fechas_ve'] 		= '';
			$dataView['tipo_entrega_ve']= '';
			$settings = array(
				 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
				,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= "ha creado una requisición de material en almacén";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_requisiciones', $actividad, $data_change);

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

		echo json_encode($response);
	}

	public function process_remove_requisicion() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_requisicion']);
			#ELIMIANCIÓN DEL VALE DE ENTRADA
			$update = $this->db_ar->update_requisicion(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA
			$update = $this->db_ar->update_requisicion_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una requisición de material en almacén";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_requisicion'], 'tbl_requisiciones', $actividad, $data_change);
			
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

	public function process_build_pdf_requsicion() {
		$sqlWhere 	= $this->input->post(['id_requisicion']);
		$productos 	= $this->db_ar->get_requisicion_productos($sqlWhere);
		$dataView 	= $this->input->post();

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'cantidad' 				=> $producto['cantidad']
				,'no_parte' 				=> $producto['no_parte']
				,'unidad_medida' 			=> $producto['unidad_medida']
				,'descripcion' 				=> $producto['descripcion']
			];
		}

		/*$dataView = [
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
		];*/
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
			,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	public function get_modal_edit_requisicion() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_requisicion']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#TIPOS DE REQUISICION
		$sqlWhere['selected'] = $this->input->post('id_tipo_requisicion');
		$dataView['tipos-requisiciones'] = $this->db_ar->get_tipos_requisiciones($sqlWhere);
		$sqlWhere['selected'] = $this->input->post('id_departamento_solicitante');
		$dataView['departamentos-solicitantes'] = $this->db_ar->get_departamentos_solicitantes($sqlWhere);
		$sqlWhere['selected'] = $this->input->post('id_almacen_solicitante');
		$dataView['almacenes-solicitantes'] = $this->db_ar->get_almacenes_solicitantes($sqlWhere);
		$sqlWhere['selected'] = $this->input->post('id_departamento_encargado_surtir');
		$dataView['departamento-encargado-surtir'] = $this->db_ar->get_departamentos_encargados_surtir($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_requisicion']);
		$productos 	= $this->db_ar->get_requisicion_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_tipo_requisicion'], $dataView['tipo_requisicion']);
		unset($dataView['id_departamento_solicitante'], $dataView['departamento_solicitante']);
		unset($dataView['id_almacen_solicitante'], $dataView['almacen_solicitante']);
		unset($dataView['id_departamento_encargado_surtir'], $dataView['departamento_encargado_surtir']);

		$this->parser_view('almacen/requisicion-material/tpl/modal-editar-requisicion', $dataView, FALSE);
	}

	public function process_update_productos_requisicion() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'fecha_solicitud'
				,'id_tipo_requisicion'
				,'pedido_interno'
				,'cliente'
				// ,'fecha_entrega'
				// ,'id_vale_entrada'
				,'id_departamento_solicitante'
				,'id_almacen_solicitante'
				// ,'tipo_entrega'
				,'id_departamento_encargado_surtir'
				,'observaciones'
				,'nombre_almacen'
				,'nombre_compras'
				,'nombre_autorizacion'
			]);
			$sqlData['persona_solicitante'] = strtoupper($this->input->post('persona_solicitante'));
			$sqlData['encargado_almacen'] 	= strtoupper($this->input->post('encargado_almacen'));
			$sqlData['nombre_almacen'] 		= strtoupper($this->input->post('nombre_almacen'));
			$sqlData['nombre_compras'] 		= strtoupper($this->input->post('nombre_compras'));
			$sqlData['nombre_autorizacion'] = strtoupper($this->input->post('nombre_autorizacion'));

			$sqlWhere = $this->input->post(['id_requisicion']);
			$update = $this->db_ar->update_requisicion($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_requisicion_producto'));
			$sqlWhere = $this->input->post(['id_requisicion']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_ar->update_requisicion_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_requisicion' 		=> $this->input->post('id_requisicion')
					,'id_tipo_producto' 	=> $producto['id_tipo_producto']
					,'id_producto' 			=> $producto['id_producto']
					,'cantidad' 			=> $producto['cantidad']
					,'id_usuario_update' 	=> $this->session->userdata('id_usuario')
					,'timestamp_update' 	=> timestamp()
				];

				if (!isset($producto['id_requisicion_producto'])) {
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
				$insertBatch = $this->db_ar->insert_requisiciones_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$sqlWhere 	= $this->input->post(['id_requisicion']);
			$estatusRQ 	= $this->db_ar->get_estatus_requisicion($sqlWhere);
			$dataView 	= array_merge($dataView, $estatusRQ);
			$settings = array(
				 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
				,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado un requisición de material en almacén";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_requisicion'], 'tbl_requisiciones', $actividad, $data_change);

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

		echo json_encode($response);
	}
}

/* End of file Requisicion_material.php */
/* Location: ./application/modules/almacen/controllers/Requisicion_material.php */