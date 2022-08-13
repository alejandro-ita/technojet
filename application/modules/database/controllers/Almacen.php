<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacen extends SB_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('technojet/Almacen_productos_model', 'db_ap');
		$this->load->model('technojet/Almacen_requisiciones_model', 'db_ar');
		$this->load->model('technojet/Almacenes_vales_model', 'db_av');
		$this->load->model('technojet/Productos_model', 'db_productos');
	}

	public function index() {
		$this->load_view('database/almacen/almacen_view');
	}

	public function productos() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'productos', 'dirname'=>"$pathJS/database/almacen", 'fulldir'=>TRUE];

        $dataTools['categorias']= $this->db_catalogos->get_categorias(['grupo'=>1]);
        $dataView['tpl-tools'] = $this->parser_view('database/almacen/productos/tpl/tpl-tools', $dataTools);

		$this->load_view('database/almacen/productos/productos_view', $dataView, $includes);
	}

	public function get_productos() {
		$sqlWhere = $this->input->post('id_categoria') ? $this->input->post(['id_categoria']) : [];
		$sqlWhere['grupo']=1;
		$sqlWhere['id_sitio']=1;
		$productos = $this->db_productos->get_productos($sqlWhere);
		$productos = $productos ? $productos : [];

		$tplAcciones = $this->parser_view('database/almacen/productos/tpl/tpl-acciones');
		foreach ($productos as &$di) {
			$di['acciones'] = $tplAcciones;
		}

		echo json_encode($productos, JSON_NUMERIC_CHECK);
	}

	public function get_modal_new_producto() {
		$dataView['unidades-medida'] 	= $this->db_catalogos->get_unidades_medida_min();
		$dataView['monedas'] 			= $this->db_catalogos->get_monedas_min();
		$dataView['tipos-productos'] 	= $this->db_catalogos->get_tipos_productos_min();

		$this->parser_view('database/almacen/productos/tpl/modal-new-producto', $dataView, FALSE);
	}

	public function get_modal_update_producto() {
		$sqlWhere = $this->input->post(['id_producto']);
		$dataView = $this->db_productos->get_productos($sqlWhere, FALSE);
		$dataEncription = json_encode(['oldData'=>$dataView]);
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		$sqlWhere['selected'] = $dataView['id_unidad_medida'];
		$dataView['unidades-medida'] = $this->db_catalogos->get_unidades_medida_min($sqlWhere);
		$sqlWhere['selected'] = $dataView['id_moneda'];
		$dataView['monedas'] = $this->db_catalogos->get_monedas_min($sqlWhere);
		$sqlWhere['selected'] = $dataView['id_tipo_producto'];
		$dataView['tipos-productos'] = $this->db_catalogos->get_tipos_productos_min($sqlWhere);

		#ELIMINAMOS CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_unidad_medida'], $dataView['custom_unidad_medida']);
		unset($dataView['id_moneda'], $dataView['custom_moneda']);
		unset($dataView['id_tipo_producto'], $dataView['tipo_producto']);

		$this->parser_view('database/almacen/productos/tpl/modal-update-producto', $dataView, FALSE);
	}

	public function process_save_producto() {
		try {
			$sqlWhere = $this->input->post(['id_categoria', 'no_parte', 'id_tipo_producto', 'id_unidad_medida']);
			$sqlWhere['id_sitio'] = 1;
			$exist = $this->db_productos->get_productos($sqlWhere, FALSE);

			if (!$exist) {
				$sqlData = $this->input->post(['id_categoria', 'no_parte', 'id_tipo_producto', 'id_unidad_medida', 'descripcion', 'precio_inventario', 'id_moneda', 'stock_min', 'stock_max', 'piezas_iniciales']);
				$sqlData['id_sitio'] = 1;
				$insert = $this->db_productos->insert_producto($sqlData);
				$insert OR set_exception();

				$actividad 		= "ha creado un producto en almacén con categoría: ".$_POST['categoria'];
				$data_change 	= ['insert'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($insert, 'tbl_productos', $actividad, $data_change);
				
			} else set_alert(lang('productos_articulo_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('productos_articulo_save_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_update_producto() {
		try {
			$oldData = $this->input->post('oldData');
			$id_categoria = $this->input->post('id_categoria')
				? $this->input->post('id_categoria')
				: $oldData['id_categoria'];

			$sqlWhere = [
				 'notIN' 			=> $this->input->post('id_producto')
				,'no_parte' 		=> $this->input->post('no_parte')
				,'id_tipo_producto' => $this->input->post('id_tipo_producto')
				,'id_unidad_medida' => $this->input->post('id_unidad_medida')
				,'id_categoria' 	=> $id_categoria
				,'id_sitio' 		=> 1
			];
			$exist = $this->db_productos->get_productos($sqlWhere, FALSE);
			if (!$exist) {
				$sqlData = $this->input->post(['no_parte', 'id_tipo_producto', 'id_unidad_medida', 'descripcion', 'precio_inventario', 'id_moneda', 'stock_min', 'stock_max', 'piezas_iniciales']);
				$arrayDiff = compare_data_productos($oldData, $sqlData);

				if ($arrayDiff) {
					$sqlWhere = $this->input->post(['id_producto']);
					$update = $this->db_productos->update_producto($sqlData, $sqlWhere);
					$update OR set_exception();

					$actividad 		= "ha editado un producto en almacén con categoría: ".$_POST['categoria'];
					$data_change 	= ['update'=>['newData'=>$sqlData]];
					registro_bitacora_actividades($sqlWhere['id_producto'], 'tbl_productos', $actividad, $data_change);
				}
			} else set_alert(lang('productos_articulo_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('productos_articulo_update_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_producto() {
		try {
			$sqlWhere = $this->input->post(['id_producto', 'id_categoria']);
			$remove = $this->db_productos->update_producto(['activo'=>0], $sqlWhere);
			$remove OR set_exception();

			$oldData = [];
			foreach (['no_parte', 'id_unidad_medida', 'descripcion', 'precio_inventario', 'id_moneda', 'stock_min', 'stock_max', 'piezas_iniciales'] as $index) {
				$oldData[$index] = $this->input->post($index);
			}

			$actividad 		= "ha eliminado un producto en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['delete'=>['oldData'=>$oldData]];
			registro_bitacora_actividades($sqlWhere['id_producto'], 'tbl_productos', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('productos_articulo_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function requisiciones() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'requisiciones', 'dirname'=>"$pathJS/database/almacen", 'fulldir'=>TRUE];

        $dataTools['categorias']= $this->db_catalogos->get_categorias(['grupo'=>2]);
        $dataView['tpl-tools'] = $this->parser_view('database/almacen/requisiciones/tpl/tpl-tools', $dataTools);

		$this->load_view('database/almacen/requisiciones/requisiciones_view', $dataView, $includes);
	}

	public function get_requisiciones() {
		$sqlWhere = $this->input->post('id_categoria') ? $this->input->post(['id_categoria']) : [];
		$sqlWhere['grupo']=2;
		$requisiciones = $this->db_ar->get_almacen_requisicion_min($sqlWhere);
		$requisiciones = $requisiciones ? $requisiciones : [];

		$tplAcciones = $this->parser_view('database/almacen/requisiciones/tpl/tpl-acciones');
		foreach ($requisiciones as &$rec) {
			$rec['acciones'] = $tplAcciones;
		}

		echo json_encode($requisiciones, JSON_NUMERIC_CHECK);
	}

	public function get_modal_new_requisicion() {
		$this->parser_view('database/almacen/requisiciones/tpl/modal-new-requisicion', [], FALSE);
	}

	public function process_save_requisicion() {
		try {
			$sqlWhere = $this->input->post(['requisicion', 'id_categoria']);
			$exist = $this->db_ar->get_almacen_requisicion_min($sqlWhere, FALSE);

			if (!$exist) {
				$sqlData = $this->input->post(['id_categoria', 'requisicion']);
				$insert = $this->db_ar->insert_almacen_requisicion($sqlData);
				$insert OR set_exception();
				$actividad 		= "ha creado una requisición en almacén con categoría: ".$_POST['categoria'];
				$data_change 	= ['insert'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($insert, 'tbl_almacen_requisiciones', $actividad, $data_change);
				
			} else set_alert(lang('requisicion_articulo_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('requisicion_save_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_update_requisicion() {
		try {
			$sqlWhere = [
				 'notIN' 		=> $this->input->post('id_almacen_requisicion')
				,'requisicion' 	=> $this->input->post('requisicion')
				,'id_categoria' => $this->input->post('id_categoria')
			];
			$exist = $this->db_ar->get_almacen_requisicion_min($sqlWhere, FALSE);

			if (!$exist) {
				$sqlWhere = $this->input->post(['id_almacen_requisicion']);
				$sqlData = $this->input->post(['requisicion']);
				$update = $this->db_ar->update_almacen_requisicion($sqlData, $sqlWhere);
				$update OR set_exception();
				$actividad 		= "ha editado una requisición en almacén con categoría: ".$_POST['categoria'];
				$data_change 	= ['update'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($sqlWhere['id_almacen_requisicion'], 'tbl_almacen_requisiciones', $actividad, $data_change);
				
			} else set_alert(lang('requisicion_articulo_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('requisicion_update_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_modal_update_requisicion() {
		$sqlWhere = $this->input->post(['id_almacen_requisicion']);
		$dataView = $this->db_ar->get_almacen_requisicion_min($sqlWhere, FALSE);

		$this->parser_view('database/almacen/requisiciones/tpl/modal-update-requisicion', $dataView, FALSE);
	}

	public function process_remove_requisicion() {
		try {
			$sqlWhere = $this->input->post(['id_almacen_requisicion', 'id_categoria']);
			$remove = $this->db_ar->update_almacen_requisicion(['activo'=>0], $sqlWhere);
			$remove OR set_exception();
			
			$actividad 		= "ha eliminado una requisición en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_almacen_requisicion'], 'tbl_almacen_requisiciones', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('requisicion_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function vales_entrada() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'/database/almacen/vales-entrada', 'dirname'=>$pathJS, 'fulldir'=>TRUE];

        $dataTools['categorias']= $this->db_catalogos->get_categorias(['grupo'=>4]);
        $dataView['tpl-tools'] = $this->parser_view('database/almacen/vales/tpl/tpl-tools', $dataTools);

		$this->load_view('database/almacen/vales/vales_entrada_view', $dataView, $includes);
	}

	public function get_almacen_vales_entrada() {
		$id_categoria = $this->input->post('id_categoria');
		$response = [];

		if (!$id_categoria OR $id_categoria==19) {
			$ae = self::get_vales_almacen(19);
			$response = $ae;
		}

		if (!$id_categoria OR $id_categoria==20) {
			$status = self::get_vales_estatus(20);
			$response = array_merge($response, $status);
		}
		// debug($response);


		echo json_encode($response);
	}

	private function get_vales_almacen($id_categoria) {
		$sqlWhere = $this->input->post(['tipo']);
		$response = $this->db_av->get_vales_almacenes($sqlWhere);

		$tplAcciones = $this->parser_view('database/almacen/vales/tpl/tpl-acciones');
		foreach ($response as &$almacen) {
			$almacen['id_categoria'] = $id_categoria;
			$almacen['id'] = $almacen['id_vale_almacen'];
			$almacen['titulo'] = $almacen['almacen'];
			$almacen['acciones'] = $tplAcciones;
		}

		return $response;
	}

	public function process_save_vales_almacen() {
		try {
			$sqlWhere = $this->input->post(['almacen', 'tipo']);
			$exist = $this->db_av->get_vales_almacenes($sqlWhere, FALSE);
			!$exist OR set_alert(lang('general_row_exist'));

			$sqlData = $this->input->post(['almacen', 'tipo']);
			$insert = $this->db_av->insert_vale_almacen($sqlData);
			$insert OR set_exception();

			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha creado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha creado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_almacen', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_add_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_update_vales_almacen() {
		try {
			$sqlWhere = $this->input->post(['almacen', 'tipo']);
			$sqlWhere['notIn'] = $this->input->post('id_vale_almacen');
			$exist = $this->db_av->get_vales_almacenes($sqlWhere, FALSE);
			!$exist OR set_alert(lang('general_row_exist'));

			$sqlData = $this->input->post(['almacen', 'tipo']);
			$sqlWhere= $this->input->post(['id_vale_almacen']);
			$insert = $this->db_av->update_vale_almacen($sqlData, $sqlWhere);
			$insert OR set_exception();

			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha editado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha editado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_almacen'], 'tbl_vales_almacen', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_update_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_almacen() {
		try {
			$sqlWhere= $this->input->post(['id_vale_almacen']);
			$update = $this->db_av->update_vale_almacen(['activo'=>0], $sqlWhere);
			$update OR set_exception();
			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha eliminado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha eliminado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_vale_almacen'], 'tbl_vales_almacen', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	private function get_vales_estatus($id_categoria) {
		$sqlWhere = $this->input->post(['tipo']);
		$response = $this->db_av->get_vales_estatus($sqlWhere);

		$tplAcciones = $this->parser_view('database/almacen/vales/tpl/tpl-acciones');
		foreach ($response as &$status) {
			$status['id_categoria'] = $id_categoria;
			$status['id'] = $status['id_vale_estatus'];
			$status['titulo'] = $status['estatus'];
			$status['acciones'] = $tplAcciones;
		}

		return $response;
	}

	public function process_save_vales_estatus() {
		try {
			$sqlWhere = $this->input->post(['estatus', 'tipo']);
			$exist = $this->db_av->get_vales_estatus($sqlWhere, FALSE);
			!$exist OR set_alert(lang('general_row_exist'));

			$sqlData = $this->input->post(['estatus', 'tipo']);
			$insert = $this->db_av->insert_vale_estatus($sqlData);
			$insert OR set_exception();

			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha creado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha creado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_estatus', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_add_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function vales_salida() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'/database/almacen/vales-salida', 'dirname'=>$pathJS, 'fulldir'=>TRUE];

        $dataTools['categorias']= $this->db_catalogos->get_categorias(['grupo'=>5]);
        $dataView['tpl-tools'] = $this->parser_view('database/almacen/vales/tpl/tpl-tools', $dataTools);

		$this->load_view('database/almacen/vales/vales_salida_view', $dataView, $includes);
	}

	public function get_almacen_vales_salida() {
		$id_categoria = $this->input->post('id_categoria');
		$response = [];

		if (!$id_categoria OR $id_categoria==21) {
			$ae = self::get_vales_almacen(21);
			$response = $ae;
		}

		if (!$id_categoria OR $id_categoria==22) {
			$status = self::get_vales_estatus(22);
			$response = array_merge($response, $status);
		}
		// debug($response);


		echo json_encode($response);
	}

	public function process_update_vales_estatus() {
		try {
			$sqlWhere = $this->input->post(['estatus', 'tipo']);
			$sqlWhere['notIn'] = $this->input->post('id_vale_estatus');
			$exist = $this->db_av->get_vales_estatus($sqlWhere, FALSE);
			!$exist OR set_alert(lang('general_row_exist'));

			$sqlData = $this->input->post(['estatus', 'tipo']);
			$sqlWhere= $this->input->post(['id_vale_estatus']);
			$insert = $this->db_av->update_vale_estatus($sqlData, $sqlWhere);
			$insert OR set_exception();
			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha editado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha editado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_estatus'], 'tbl_vales_estatus', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_update_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_estatus() {
		try {
			$sqlWhere= $this->input->post(['id_vale_estatus']);
			$update = $this->db_av->update_vale_estatus(['activo'=>0], $sqlWhere);
			$update OR set_exception();
			$actividad 		= $_POST['tipo']=='ENTRADA'
				? "ha eliminado un vale de entrada en almacén con categoría: ".$_POST['categoria']
				: "ha eliminado un vale de salida en almacén con categoría: ".$_POST['categoria'];
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_vale_estatus'], 'tbl_vales_estatus', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_row_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

}

/* End of file Almacen.php */
/* Location: ./application/modules/technojet/controllers/database/Almacen.php */