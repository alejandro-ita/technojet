<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendedores extends SB_Controller {

	public function __construct() {
		parent::__construct();
        //Models import
        $this->load->model('technojet/Vendedores_model', 'db_vendedor');
	}

	public function catalogo() {
        $pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'vendedores', 'dirname'=>"$pathJS/database/vendedores", 'fulldir'=>TRUE];

        $dataView['tpl-tools']= $this->parser_view('database/vendedores/tpl/tpl-tools');

		$this->load_view('database/vendedores/vendedores_view', $dataView, $includes);
	}

    public function get_vendedores(){
        $vendedores = $this->db_vendedor->get_vendedores_main();
		$vendedores = $vendedores ? $vendedores : [];

		$tplAcciones = $this->parser_view('database/vendedores/tpl/tpl-acciones');
		foreach ($vendedores as &$di) {
			$di['acciones'] = $tplAcciones;
		}

		echo json_encode($vendedores, JSON_NUMERIC_CHECK);
    }

    public function get_modal_new_vendedor(){
        $this->parser_view('database/vendedores/tpl/modal-new-vendedor', [], FALSE);
    }

    public function process_save_vendedor() {
		try {
			$sqlWhere = $this->input->post(['vendedor']);
			$exist = $this->db_vendedor->get_vendedores_main($sqlWhere, FALSE);

			if (!$exist) {
				$sqlData = $this->input->post(['vendedor', 'departamento', 'correo', 'comision']);
				$insert = $this->db_vendedor->insert_vendedor($sqlData);
				$insert OR set_exception();
				$actividad 		= "ha creado un nuevo vendedor";
				$data_change 	= ['insert'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($insert, 'tbl_vendedores', $actividad, $data_change);
				
			} else set_alert(lang('vendedor_registro_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vendedor_save_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

    public function get_modal_update_vendedor() {
		$sqlWhere = $this->input->post(['id_vendedor']);
		$dataView = $this->db_vendedor->get_vendedores_main($sqlWhere, FALSE);

		$this->parser_view('database/vendedores/tpl/modal-update-vendedor', $dataView, FALSE);
	}

    public function process_update_vendedor() {
		try {
			$sqlWhere = [
				'notIN' 		=> $this->input->post('id_vendedor'),
				'vendedor' 	=> $this->input->post('vendedor'),
			];
			$exist = $this->db_vendedor->get_vendedores_main($sqlWhere, FALSE);

			if (!$exist) {
				$sqlWhere = $this->input->post(['id_vendedor']);
				$sqlData = $this->input->post(['vendedor', 'departamento', 'correo', 'comision']);
				$update = $this->db_vendedor->update_vendedor($sqlData, $sqlWhere);
				$update OR set_exception();
				$actividad 		= "ha editado un vendedor ";
				$data_change 	= ['update'=>['newData'=>$sqlData]];
				registro_bitacora_actividades($sqlWhere['id_vendedor'], 'tbl_vendedores', $actividad, $data_change);
				
			} else set_alert(lang('vendedor_registro_exist'));

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vendedor_update_success'),
				'icon' 		=> 'success'
			];

		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

    public function process_remove_vendedor() {
		try {
			$sqlWhere = $this->input->post(['id_vendedor']);
			$remove = $this->db_vendedor->update_vendedor(['activo'=>0], $sqlWhere);
			$remove OR set_exception();
			
			$actividad 		= "ha eliminado un vendedor";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_vendedor'], 'tbl_vendedores', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vendedor_rm_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

}