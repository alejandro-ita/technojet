<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_sistemas extends SB_Controller {

	public function __construct() {
		parent::__construct();
		//Do your magic here
		$this->load->model('administracion/Reportes_sistemas_model', 'db_rs');
	}

	public function index() {
		$includes = get_includes_vendor(['moment', 'dataTables', 'jQValidate']);
		$pathJS = get_var('path_js').'/administracion';
        $includes['modulo']['js'][] = ['name'=>'reporte-sistemas', 'dirname'=>$pathJS, 'fulldir'=>TRUE];

        $dataView['tpl-tools'] = $this->parser_view('administracion/reporte/tpl/tpl-tools');

		$this->load_view('administracion/reporte/sistemas-view', $dataView, $includes);
	}

	public function get_reportes_sistemas() {
		$response = $this->db_rs->get_reportes_sistemas();

		$acciones = $this->parser_view('administracion/reporte/tpl/tpl-acciones');
		foreach ($response as &$resp) {
			$resp['acciones'] = $acciones;
		}

		echo json_encode($response);
	}

	public function get_modal_nuevo_reporte() {
		#OBTENEMOS EL CONSECUTIVO DEL VALE DE ENTRADA
		$folio = $this->db_rs->get_ultimo_reporte_sistema();
		$dataView = $folio;

		$dataView['tbl-reporte-oficina'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-oficina');
		$dataView['tbl-reporte-taller'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-taller');
		$dataView['tbl-reporte-area-produccion'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-area-produccion');
		$dataView['tbl-reporte-gimnasio'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-gimnasio');
		$dataView['tbl-reporte-roof-garden'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-roof-garden');

		$this->parser_view('administracion/reporte/tpl/modal-nuevo-reporte-sistemas', $dataView, FALSE);
	}

	public function process_save_nuevo_reporte() {
		try {
			$this->db->trans_begin();
			$sqlData = [
				 'fecha' 					=> $this->input->post('fecha')
				,'responsable' 				=> $this->input->post('responsable')
				,'encargado_elaboracion'	=> $this->input->post('encargado_elaboracion')
				,'vo_bo' 					=> $this->input->post('vo_bo')
				,'oficina_observaciones' 	=> $this->input->post('oficina_observaciones')
				,'oficina_am' 				=> $this->input->post('oficina_am')
				,'taller_observaciones' 	=> $this->input->post('taller_observaciones')
				,'taller_am' 				=> $this->input->post('taller_am')
				,'area_pro_observaciones' 	=> $this->input->post('area_pro_observaciones')
				,'area_pro_am' 				=> $this->input->post('area_pro_am')
				,'gimnasio_observaciones' 	=> $this->input->post('gimnasio_observaciones')
				,'gimnasio_am' 				=> $this->input->post('gimnasio_am')
				,'roof_garden_observaciones'=> $this->input->post('roof_garden_observaciones')
				,'roof_garden_am' 			=> $this->input->post('roof_garden_am')
			];
			$insert = $this->db_rs->insert_reporte_sistema($sqlData);
			$insert OR set_exception();
			$dataView = $sqlData;
			$dataView['id_reporte_sistema'] = $insert;
			$dataView['custom_fecha'] = date('d/m/Y', strtotime($sqlData['fecha']));

			#REGISTRO DE ESTADO DE LOS ITEMS
			$areaEstado = [
				 'oficina'			=> 'Oficina'
				,'taller'			=> 'Taller'
				,'area_produccion' 	=> 'Área De Producción'
				,'gimnasio' 		=> 'Gimnasio'
				,'roof_garden' 		=> 'Roof Garden'
			];
			$sqlDataBatch = [];
			foreach ($areaEstado as $indexPost => $category) {
				$area = $this->input->post($indexPost);
				$itemsTable = [];
				foreach ($area as $obj) {
					$sqlDataBatch[] = [
						 'id_reporte_sistema' 	=> $insert
						,'categoria' 			=> $category
						,'item' 				=> $obj['item']
						,'descripcion_estado' 	=> $obj['descripcion_estado']
						,'estado' 				=> $obj['estado']
						,'id_usuario_insert' 	=> $this->session->userdata('id_usuario')
						,'timestamp_insert' 	=> timestamp()
					];

					$lastItem 	= end($sqlDataBatch);
					$lastItem['estado'] = ($obj['estado']=='Bueno'?1:0);
					$itemsTable[] = $lastItem;
				}

				$dataView["items_{$indexPost}"] = $itemsTable;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_rs->insert_reporte_sistema_estado($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#REGISTRO DE LOS PRODUCTOS
			$productos = $this->input->post('productos');
			if ($productos) {
				$sqlDataBatch = [];
				foreach ($productos as $producto) {
					$sqlDataBatch[] = [
						 'id_reporte_sistema' 	=> $insert
						,'cantidad' 			=> $producto['cantidad']
						,'no_parte' 			=> $producto['no_parte']
						,'id_unidad_medida' 	=> $producto['id_unidad_medida']
						,'descripcion' 			=> $producto['descripcion']
						,'precio_unitario' 		=> $producto['precio_unitario']
						,'descuento' 			=> $producto['descuento']
						,'importe' 				=> $producto['importe']
						,'autorizado' 			=> $producto['autorizado']
						,'id_usuario_insert' 	=> $this->session->userdata('id_usuario')
						,'timestamp_insert' 	=> timestamp()
					];
				}
				if ($sqlDataBatch) {
					$insertBatch = $this->db_rs->insert_reporte_sistema_productos($sqlDataBatch);
					$insertBatch OR set_exception();
				}
			}
			$dataView['productos'] = ($productos?$productos:[]);

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Reporte_computo_'.date('YmdHis')
				,'content_file' => $this->parser_view('administracion/reporte/tpl/tpl-pdf-reporte-sistemas', $dataView)
				,'load_file' 	=> FALSE
				,'size' 		=> 'legal'
			);

			$sqlData['estado'] = $sqlDataBatch;
			$actividad 		= "ha creado un Reporte de sistemas";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_reportes_sistemas', $actividad, $data_change);

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

	public function process_remove_reporte() {
		try {
			$this->db->trans_begin();
			$sqlWhere = ['id_reporte_sistema'=>$this->input->post('id_reporte_sistema'), 'activo'=>1];
			$remove = $this->db_rs->update_reporte_sistema(['activo'=>0], $sqlWhere);
			$remove OR set_exception();

			$remove = $this->db_rs->update_reporte_sistema_estado(['activo'=>0], $sqlWhere);
			$remove OR set_exception();

			$actividad 		= "ha eliminado un Reporte de sistemas";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_reporte_sistema'], 'tbl_reportes_sistemas', $actividad, $data_change);
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

	public function process_build_pdf_reporte_sistemas() {
		$sqlWhere = $this->input->post(['id_reporte_sistema']);
		$reporteData = $this->db_rs->get_reportes_sistemas($sqlWhere, FALSE);
		$reporteData OR set_exception();
		$dataView = $reporteData;

		$areaEstado = [
			 'Oficina' 			=> 'oficina'
			,'Taller' 			=> 'taller'
			,'Área De Producción' => 'area_produccion'
			,'Gimnasio' 		=> 'gimnasio'
			,'Roof Garden' 		=> 'roof_garden'
		];
		$sistemaEstados = $this->db_rs->get_reporte_sistema_estados($sqlWhere);
		$sistemaEstados OR set_exception();
		foreach ($sistemaEstados as $obj) {
			$indexPost = $areaEstado[$obj['categoria']];
			$dataView["items_{$indexPost}"][] = [
				 'item' 				=> $obj['item']
				,'descripcion_estado' 	=> $obj['descripcion_estado']
				,'estado' 				=> ($obj['estado']=='Bueno'?1:0)
			];
		}

		$productos = $this->db_rs->get_reporte_sistema_productos($sqlWhere);
		$dataView['productos'] = ($productos?$productos:[]);

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Reporte_computo_'.date('YmdHis')
			,'content_file' => $this->parser_view('administracion/reporte/tpl/tpl-pdf-reporte-sistemas', $dataView)
			,'load_file' 	=> FALSE
			,'size' 		=> 'legal'
		);

		echo json_encode([
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		]);
	}

	public function get_modal_edit_reporte_sistemas() {
		$sqlWhere = $this->input->post(['id_reporte_sistema']);
		$dataView = $this->db_rs->get_reportes_sistemas($sqlWhere, FALSE);
		$dataEncription = json_encode($sqlWhere);
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);


		$estados = $this->db_rs->get_reporte_sistema_estados($sqlWhere);
		$dataTpl = [
			 'oficina_observaciones' 	=> $dataView['oficina_observaciones']
			,'oficina_am' 				=> $dataView['oficina_am']
			,'taller_observaciones' 	=> $dataView['taller_observaciones']
			,'taller_am' 				=> $dataView['taller_am']
			,'area_pro_observaciones' 	=> $dataView['area_pro_observaciones']
		    ,'area_pro_am' 				=> $dataView['area_pro_am']
		    ,'gimnasio_observaciones' 	=> $dataView['gimnasio_observaciones']
		    ,'gimnasio_am' 				=> $dataView['gimnasio_am']
		    ,'roof_garden_observaciones'=> $dataView['roof_garden_observaciones']
		    ,'roof_garden_am' 			=> $dataView['roof_garden_am']
		];
		foreach ($estados as $item) {
			$keyidEstado 	= strtolower(strtr(sanitizar_string("id_$item[categoria]_$item[item]"), [' '=> '_']));
			$keyDescripcion = strtolower(strtr(sanitizar_string("$item[categoria]_$item[item]_desc"), [' '=> '_']));
			$keyEstado 		= strtolower(strtr(sanitizar_string("$item[categoria]_$item[item]_estado"), [' '=> '_']));

			$dataTpl[$keyidEstado] 		= $item['id'];
			$dataTpl[$keyDescripcion] 	= $item['descripcion_estado'];
			$dataTpl[$keyEstado] 		= ($item['estado']=='Bueno'?1:0);
		}

		$dataView['tbl-reporte-oficina'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-oficina-editar', $dataTpl);
		$dataView['tbl-reporte-taller'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-taller-editar', $dataTpl);
		$dataView['tbl-reporte-area-produccion'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-area-produccion-editar', $dataTpl);
		$dataView['tbl-reporte-gimnasio'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-gimnasio-editar', $dataTpl);
		$dataView['tbl-reporte-roof-garden'] = $this->parser_view('administracion/reporte/tpl/tbl-reporte-roof-garden-editar', $dataTpl);

		$productos = $this->db_rs->get_reporte_sistema_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos?$productos:[]);

		$this->parser_view('administracion/reporte/tpl/modal-editar-reporte-sistemas', $dataView, FALSE);
	}

	public function process_update_reporte_sistemas() {
		try {
			$this->db->trans_begin();
			$sqlWhere = $this->input->post(['id_reporte_sistema']);
			$sqlData = [
				 'fecha' 					=> $this->input->post('fecha')
				,'responsable' 				=> $this->input->post('responsable')
				,'encargado_elaboracion'	=> $this->input->post('encargado_elaboracion')
				,'vo_bo' 					=> $this->input->post('vo_bo')
				,'oficina_observaciones' 	=> $this->input->post('oficina_observaciones')
				,'oficina_am' 				=> $this->input->post('oficina_am')
				,'taller_observaciones' 	=> $this->input->post('taller_observaciones')
				,'taller_am' 				=> $this->input->post('taller_am')
				,'area_pro_observaciones' 	=> $this->input->post('area_pro_observaciones')
				,'area_pro_am' 				=> $this->input->post('area_pro_am')
				,'gimnasio_observaciones' 	=> $this->input->post('gimnasio_observaciones')
				,'gimnasio_am' 				=> $this->input->post('gimnasio_am')
				,'roof_garden_observaciones'=> $this->input->post('roof_garden_observaciones')
				,'roof_garden_am' 			=> $this->input->post('roof_garden_am')
			];
			$update = $this->db_rs->update_reporte_sistema($sqlData, $sqlWhere);
			$update OR set_exception();
			$dataView = $sqlData;
			$dataView['id_reporte_sistema'] = $this->input->post('id_reporte_sistema');
			$dataView['custom_fecha'] = date('d/m/Y', strtotime($sqlData['fecha']));

			#REGISTRO DE ESTADO DE LOS PRODUCTOS
			$areaEstado = [
				 'oficina'			=> 'Oficina'
				,'taller'			=> 'Taller'
				,'area_produccion' 	=> 'Área De Producción'
				,'gimnasio' 		=> 'Gimnasio'
				,'roof_garden' 		=> 'Roof Garden'
			];
			$listEstados = [];
			foreach ($areaEstado as $indexPost => $category) {
				$area = $this->input->post($indexPost);
				$itemsTable = [];
				foreach ($area as $obj) {
					$sqlData = [
						 'item' 				=> $obj['item']
						,'descripcion_estado' 	=> $obj['descripcion_estado']
						,'estado' 				=> $obj['estado']
					];
					$update = $this->db_rs->update_reporte_sistema_estado($sqlData, ['id'=>$obj['id']]);
					$update OR set_exception();

					$sqlData['estado'] = ($obj['estado']=='Bueno'?1:0);
					$itemsTable[] = $sqlData;
				}

				$dataView["items_{$indexPost}"] = $itemsTable;
				$listEstados = array_merge($listEstados, ["items_{$indexPost}"=>$dataView["items_{$indexPost}"]]);
			}

			#REGISTRO DE LOS PRODUCTOS
			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = $productos ? array_filter(array_column($productos, 'id')) : [];
			$sqlWhere = $this->input->post(['id_reporte_sistema']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_rs->update_reporte_sistema_producto(['activo'=>0], $sqlWhere);

			if ($productos) {
				$sqlDataBatch = [];
				foreach ($productos as $producto) {
					$sqlData = [
						 'id_reporte_sistema' 	=> $this->input->post('id_reporte_sistema')
						,'cantidad' 			=> $producto['cantidad']
						,'no_parte' 			=> $producto['no_parte']
						,'id_unidad_medida' 	=> $producto['id_unidad_medida']
						,'descripcion' 			=> $producto['descripcion']
						,'precio_unitario' 		=> $producto['precio_unitario']
						,'descuento' 			=> $producto['descuento']
						,'importe' 				=> $producto['importe']
						,'autorizado' 			=> $producto['autorizado']
						,'id_usuario_insert' 	=> $this->session->userdata('id_usuario')
						,'timestamp_insert' 	=> timestamp()
					];

					if (!isset($producto['id_reporte_sistema'])) {
						$sqlDataBatch[] = $sqlData;
					}
				}

				if ($sqlDataBatch) {
					$insertBatch = $this->db_rs->insert_reporte_sistema_productos($sqlDataBatch);
					$insertBatch OR set_exception();
				}
			}
			$dataView['productos'] = ($productos?$productos:[]);

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Reporte_computo_'.date('YmdHis')
				,'content_file' => $this->parser_view('administracion/reporte/tpl/tpl-pdf-reporte-sistemas', $dataView)
				,'load_file' 	=> FALSE
				,'size' 		=> 'legal'
			);

			$sqlData['estado'] = $listEstados;
			$actividad 		= "ha editado un Reporte de sistemas";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_reporte_sistema'], 'tbl_reportes_sistemas', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_update_success'),
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

	public function get_modal_add_producto_reporte() {
		$unidades_medida = $this->db_catalogos->get_unidades_medida_min();
		$dataView['unidades-medida'] = $unidades_medida;

		$this->parser_view('administracion/reporte/tpl/modal-add-producto-reporte-sistemas', $dataView, FALSE);
	}
}

/* End of file Reporte_sistemas.php */
/* Location: ./application/modules/administracion/controllers/Reporte_sistemas.php */