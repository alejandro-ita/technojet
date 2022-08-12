<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends SB_Controller {

	public function __construct() {
		parent::__construct();
        //Models import
        $this->load->model('technojet/Clientes_model', 'db_cliente');
	}

	public function catalogo() {
        $pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'clientes', 'dirname'=>"$pathJS/database/clientes", 'fulldir'=>TRUE];

        $dataView['tpl-tools']= $this->parser_view('database/clientes/tpl/tpl-tools');

		$this->load_view('database/clientes/clientes_view', $dataView, $includes);
	}

    public function get_clientes(){
        $clientes = $this->db_cliente->get_clientes_main();
		$clientes = $clientes ? $clientes : [];

		$tplAcciones = $this->parser_view('database/clientes/tpl/tpl-acciones');
		foreach ($clientes as &$di) {
			$di['acciones'] = $tplAcciones;
		}

		echo json_encode($clientes, JSON_NUMERIC_CHECK);
    }

    public function get_modal_new_cliente(){
        $this->parser_view('database/clientes/tpl/modal-new-cliente', [], FALSE);
    }

    public function process_save_cliente() {
		try {
			$sqlWhere = $this->input->post(['cliente']);
			$exist = $this->db_cliente->get_clientes_main($sqlWhere, FALSE);

			if (!$exist) {
				$sqlData = $this->input->post(['cliente', 'razon_social', 'rfc']);
				$insert = $this->db_cliente->insert_cliente($sqlData);
				$insert OR set_exception();
				$actividad 		= "ha creado un nuevo cliente";
				$data_change 	= ['insert'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($insert, 'tbl_clientes', $actividad, $data_change);
				
			} else set_alert(lang('cliente_registro_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('cliente_save_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

    public function get_modal_update_cliente() {
		$sqlWhere = $this->input->post(['id_cliente']);
		$dataView = $this->db_cliente->get_clientes_main($sqlWhere, FALSE);

		$this->parser_view('database/clientes/tpl/modal-update-cliente', $dataView, FALSE);
	}

    public function process_update_cliente() {
		try {
			$sqlWhere = [
				'notIN' 		=> $this->input->post('id_cliente'),
				'cliente' 	=> $this->input->post('cliente'),
			];
			$exist = $this->db_cliente->get_clientes_main($sqlWhere, FALSE);

			if (!$exist) {
				$sqlWhere = $this->input->post(['id_cliente']);
				$sqlData = $this->input->post(['cliente', 'razon_social', 'rfc']);
				$update = $this->db_cliente->update_cliente($sqlData, $sqlWhere);
				$update OR set_exception();
				$actividad 		= "ha editado un cliente ";
				$data_change 	= ['update'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($sqlWhere['id_cliente'], 'tbl_clientes', $actividad, $data_change);
				
			} else set_alert(lang('cliente_registro_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('cliente_update_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

    public function process_remove_cliente() {
		try {
			$sqlWhere = $this->input->post(['id_cliente']);
			$remove = $this->db_cliente->update_cliente(['activo'=>0], $sqlWhere);
			$remove OR set_exception();
			
			$actividad 		= "ha eliminado un cliente";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_cliente'], 'tbl_clientes', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('cliente_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

}