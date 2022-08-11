<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizaciones extends SB_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('technojet/Productos_model', 'db_productos');
	}

	public function index() {
		$this->load_view('database/ventas/ventas_view');
	}

	public function productos() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'template_helper', 'dirname'=>"$pathJS/helpers", 'fulldir'=>TRUE];
        $includes['modulo']['js'][] = ['name'=>'productos', 'dirname'=>"$pathJS/database/ventas", 'fulldir'=>TRUE];

        $dataTools['categorias']= $this->db_catalogos->get_categorias(['grupo'=>3]);
        $dataView['tpl-tools'] = $this->parser_view('database/ventas/productos/tpl/tpl-tools', $dataTools);

		$this->load_view('database/ventas/productos/productos_view', $dataView, $includes);
	}

	public function get_productos() {
		$sqlWhere = $this->input->post('id_categoria') ? $this->input->post(['id_categoria']) : [];
		$sqlWhere['grupo']=3;
		$sqlWhere['id_sitio']=2;
		$productos = $this->db_productos->get_productos($sqlWhere);
		$productos = $productos ? $productos : [];

		$tplAcciones = $this->parser_view('database/ventas/productos/tpl/tpl-acciones');
		foreach ($productos as &$di) {
			$di['acciones'] = $tplAcciones;
		}

		echo json_encode($productos, JSON_NUMERIC_CHECK);
	}

	public function get_modal_new_producto() {
		$dataView['unidades-medida'] = $this->db_catalogos->get_unidades_medida_min();
		$dataView['monedas'] = $this->db_catalogos->get_monedas_min();
		$dataView['tipos-productos'] 	= $this->db_catalogos->get_tipos_productos_min();

		$this->parser_view('database/ventas/productos/tpl/modal-new-producto', $dataView, FALSE);
	}

	public function process_save_producto() {
		try {
			$sqlWhere = $this->input->post(['id_categoria', 'no_parte', 'id_tipo_producto', 'id_unidad_medida']);
			$sqlWhere['id_sitio'] = 2;
			$exist = $this->db_productos->get_productos($sqlWhere, FALSE);

			if (!$exist) {
				$sqlData = $this->input->post(['id_categoria', 'no_parte', 'id_tipo_producto', 'id_unidad_medida', 'descripcion', 'precio_inventario', 'id_moneda', 'stock_min', 'stock_max', 'piezas_iniciales']);
				$sqlData['id_sitio'] = 2;
				$insert = $this->db_productos->insert_producto($sqlData);
				$insert OR set_exception();

				$actividad 		= "ha creado un producto en ventas con categoría de: ".$_POST['categoria'];
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

		$this->parser_view('database/ventas/productos/tpl/modal-update-producto', $dataView, FALSE);
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
				,'id_sitio' 		=> 2
			];
			$exist = $this->db_productos->get_productos($sqlWhere, FALSE);
			if (!$exist) {
				$sqlData = $this->input->post(['no_parte', 'id_tipo_producto', 'id_unidad_medida', 'descripcion', 'precio_inventario', 'id_moneda', 'stock_min', 'stock_max', 'piezas_iniciales']);
				$arrayDiff = compare_data_productos($oldData, $sqlData);

				if ($arrayDiff) {
					$sqlWhere = $this->input->post(['id_producto']);
					$update = $this->db_productos->update_producto($sqlData, $sqlWhere);
					$update OR set_exception();

					$actividad 		= "ha editado un producto en ventas con categoría de: ".$_POST['categoria'];
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

			$actividad 		= "ha eliminado una requisición en almacén con categoría: ".$_POST['categoria'];
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
}

/* End of file Ventas.php */
/* Location: ./application/modules/technojet/controllers/database/Ventas.php */