<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario extends SB_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Tareas_model', 'db_tareas');
	}

	public function get_eventos_calendario() {
		$sqlWhere = [
			'id_usuario' 	=> $this->session->userdata('id_usuario')
			,'mc' 			=> 1
			,'fecha_inicio' => $this->input->post('fecha_inicio')
			,'fecha_fin' 	=> $this->input->post('fecha_fin')
		];

		$eventos = $this->db_tareas->get_tareas_info($sqlWhere);
		$id_usuario = $this->session->userdata('id_usuario');
		$response = [];
		foreach ($eventos as $evento) {
			$responsables = explode(',', $evento['id_responsables']);
			$participantes= explode(',', $evento['id_participantes']);
			#$evento['can-edit']  = (int)(in_array($id_usuario, array_merge($responsables, [$evento['id_usuario_insert']])));
			#$evento['read-only'] = (int)(in_array($id_usuario, explode(',', $evento['id_participantes'])));
			$evento['can-edit']  = (int)($id_usuario==$evento['id_usuario_insert']);
			$evento['read-only'] = (int)(!$evento['can-edit'] && in_array($id_usuario, array_merge($responsables, $participantes)));
			$dataEncription = $this->encryption->encrypt(json_encode(['oldData'=>$evento]));

			$hora_inicio = ($evento['hora_inicio'] ? $evento['hora_inicio'] : '00:00:00');
			$hora_fin 	 = ($evento['hora_fin'] ? $evento['hora_fin'] : '23:59:59');
			$response[] = [
				 'id' 			=> $evento['id_tarea']
				,'start' 		=> "$evento[fecha_inicio] $hora_inicio"
				,'end' 			=> "$evento[fecha_fin] $hora_fin"
				,'title' 		=> $evento['titulo']
				,'description' 	=> $evento['descripcion']
				,'dataEncription' => $dataEncription
				,'can-edit' 	=> $evento['can-edit']
				,'read-only' 	=> $evento['read-only']
			];
		}

		echo json_encode($response, JSON_NUMERIC_CHECK);
	}

	public function get_modal_new_event() {
		$tplData = $this->input->post(['fecha_inicio', 'fecha_fin']);
		$users = $this->db_users->get_users(["notIn" => 1]);
		$tplData['users'] = $users;
		$tplData['list-estatus'] = $this->db_catalogos->get_tareas_estatus();
		$tplData['list-prioridad'] = $this->db_catalogos->get_tareas_prioridad();
		$this->parser_view('technojet/dashboard/tpl/modal-nuevo-evento', $tplData, FALSE);
	}

	public function get_modal_update_event() {
		$tplData = $this->input->post('oldData');

		$users = $this->db_users->get_users(['notIn'=>1]);
		$listResponsables=[];
		foreach ($users as $key => $user) {
			$user['selected'] = (int) in_array($user['id_usuario'], explode(',', $tplData['id_responsables']));
			$listResponsables[] = $user;
		}
		$tplData['list-responsables'] = $listResponsables;

		$participantes = explode(',', $tplData['id_participantes']);
		$listParticipantes=[];
		foreach ($users as $key => $user) {
			$user['selected'] = (int) in_array($user['id_usuario'], $participantes);
			$listParticipantes[] = $user;
		}
		$tplData['list-participantes'] = $listParticipantes;

		$sqlWhere=['selected'=>$tplData['id_estatus']];
		$tplData['list-estatus'] = $this->db_catalogos->get_tareas_estatus($sqlWhere);
		$sqlWhere=['selected'=>$tplData['id_prioridad']];
		$tplData['list-prioridad'] = $this->db_catalogos->get_tareas_prioridad($sqlWhere);

		$dataEncription = json_encode(['oldData'=>$this->input->post('oldData')]);
		$tplData['dataEncription'] = $this->encryption->encrypt($dataEncription);
		$tplData['comentarios'] = modules::run('technojet/tareas/get_comment_task');

		unset($tplData['id_estatus'], $tplData['id_prioridad']);#CORRECCION DE CONFLICTOS
		$this->parser_view('technojet/dashboard/tpl/modal-editar-evento', $tplData, FALSE);
	}

	public function process_save_event() {
		try {
			$mc = ($this->input->post('mc')?1:0);
			$sqlData = $this->input->post(['id_responsables', 'titulo', 'descripcion', 'id_estatus', 'id_prioridad']);
			$sqlData['mc'] = $mc;
			$sqlData = array_merge($sqlData, ($mc
				? $this->input->post(['fecha_inicio', 'hora_inicio', 'fecha_fin', 'hora_fin'])
				: ['fecha_inicio'=>NULL, 'hora_inicio'=>NULL, 'fecha_fin'=>NULL, 'hora_fin'=>NULL]
			));
			$insert = $this->db_tareas->insert_tarea($sqlData);
			$insert OR set_exception();

			$nuevosParticipantes = explode(',', $this->input->post('id_participantes'));
			!$nuevosParticipantes OR modules::run('technojet/tareas/save_participantes', $nuevosParticipantes, $insert);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_save_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_event() {
		try {
			$oldData = $this->input->post('oldData');
			$sqlWhere = ['id_tarea'=>$oldData['id_tarea']];
			$update = $this->db_tareas->update_tarea(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$response = [
				 'success'	=> TRUE
				,'msg' 		=> lang('general_remove_success')
				,'icon' 	=> 'success'
				,'event'	=> ['id'=>$oldData['id_tarea']]
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_update_event() {
		try {
			$this->db->trans_begin();
			$oldData = $this->input->post('oldData');
			isset($oldData['id_tarea']) OR set_exception();

			$mc = ($this->input->post('mc')?1:0);
			$sqlData = $this->input->post(['id_responsables', 'titulo', 'descripcion', 'id_prioridad', 'id_estatus']);
			$sqlData['mc'] = $mc;
			$sqlData = array_merge($sqlData, ($mc
				? $this->input->post(['fecha_inicio', 'hora_inicio', 'fecha_fin', 'hora_fin'])
				: ['fecha_inicio'=>NULL, 'hora_inicio'=>NULL, 'fecha_fin'=>NULL, 'hora_fin'=>NULL]
			));

			$sqlWhere = ['id_tarea'=>$oldData['id_tarea'], 'id_usuario_insert'=>$oldData['id_usuario_insert']];
			$update = $this->db_tareas->update_tarea($sqlData, $sqlWhere);
			$update OR set_exception();

			$oldParticipantes = array_diff(explode(',', $oldData['id_participantes']), explode(',', $this->input->post('id_participantes')));
			foreach ($oldParticipantes as $participante) {
				$sqlWhere = ['id_tarea'=>$oldData['id_tarea'], 'id_participante'=>$participante, 'activo'=>1];
				$this->db_tareas->update_participante(['activo'=>0], $sqlWhere);
			}

			$nuevosParticipantes = array_diff(explode(',', $this->input->post('id_participantes')), explode(',', $oldData['id_participantes']));
			!$nuevosParticipantes OR modules::run('technojet/tareas/save_participantes', $nuevosParticipantes, $sqlWhere['id_tarea']);

			$response = [
				 'success'	=> TRUE
				,'msg' 		=> lang('general_save_success')
				,'icon' 	=> 'success'
				,'event_id' => $sqlWhere['id_tarea']
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}
}

/* End of file Calendario.php */
/* Location: ./application/modules/technojet/controllers/Calendario.php */