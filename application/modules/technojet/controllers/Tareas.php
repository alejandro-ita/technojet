<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tareas extends SB_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Tareas_model', 'db_tareas');
	}

	public function task() {
		$estatus = $this->db_catalogos->get_tareas_estatus();
		foreach ($estatus as &$es) {
			$es['selected'] = (int) in_array($es['id_estatus'], [1,2,4]);
		}
		$dataView['list-estatus'] 	= $estatus;
		$dataView['list-prioridad'] = $this->db_catalogos->get_tareas_prioridad();

		$includes = get_includes_vendor(['moment', 'jQValidate']);
		$pathtmp= 'assets/template/dashlite/demo2/assets/js';
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'fullcalendarbf47', 'dirname'=>"$pathtmp/libs", 'fulldir'=>TRUE];
        $includes['modulo']['js'][] = ['name'=>'tareas', 'dirname'=>"$pathJS/dashboard", 'fulldir'=>TRUE];

		$this->load_view('technojet/dashboard/task-view', $dataView, $includes);
	}

	public function get_modal_new_task() {
		$users = $this->db_users->get_users(["notIn" => 1]);
		$tplData['users'] = $users;
		$tplData['list-estatus'] = $this->db_catalogos->get_tareas_estatus();
		$tplData['list-prioridad'] = $this->db_catalogos->get_tareas_prioridad();
		$this->parser_view('technojet/dashboard/tpl/modal-nueva-tarea', $tplData, FALSE);
	}

	public function get_modal_update_task() {
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
		$tplData['comentarios'] = self::get_comment_task();

		unset($tplData['id_estatus'], $tplData['id_prioridad']);#CORRECCION DE CONFLICTOS
		$this->parser_view('technojet/dashboard/tpl/modal-editar-tarea', $tplData, FALSE);
	}

	public function get_modal_view_task() {
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
		$tplData['comentarios'] = self::get_comment_task();

		unset($tplData['id_estatus'], $tplData['id_prioridad']);#CORRECCION DE CONFLICTOS
		$this->parser_view('technojet/dashboard/tpl/modal-editar-tarea', $tplData, FALSE);
	}

	public function get_modal_add_comment() {
		$tplData = $this->input->post('oldData');
		$dataEncription = json_encode(['id_tarea'=>$tplData['id_tarea']]);
		$tplData['dataEncription'] = $this->encryption->encrypt($dataEncription);
		$this->parser_view('technojet/dashboard/tpl/modal-add-comment', $tplData, FALSE);
	}

	public function get_mis_tareas() {
		$sqlWhere = [
			 'id_usuario' 	=> $this->session->userdata('id_usuario')
			,'id_estatus' 	=> [1,2,4]
			,'mc' 			=> 0
		];
		$tareas = $this->db_tareas->get_tareas_info($sqlWhere);

		$id_usuario = $this->session->userdata('id_usuario');
		foreach ($tareas as &$tarea) {
			$responsables = explode(',', $tarea['id_responsables']);
			$participantes= explode(',', $tarea['id_participantes']);
			#$tarea['can-edit'] 	= (int)(in_array($id_usuario, array_merge($responsables, [$tarea['id_usuario_insert']])));
			#$tarea['read-only'] = (int)(in_array($id_usuario, explode(',', $tarea['id_participantes'])));
			$tarea['can-edit'] 	= (int)($id_usuario==$tarea['id_usuario_insert']);
			$tarea['read-only'] = (int)(!$tarea['can-edit'] && in_array($id_usuario, array_merge($responsables, $participantes)));
			$dataEncription = json_encode(['oldData'=>$tarea]);
			$tarea['dataEncription'] = $this->encryption->encrypt($dataEncription);
		}

		$tplData['mis-tareas'] = $tareas;
		$tplData['total-tareas'] = count($tareas);

		$this->parser_view('dashboard/tpl/tpl-mis-tareas', $tplData, FALSE);
	}

	public function get_all_task() {
		$response = [];

		$id_estatus = $this->input->post('id_estatus');
		$id_prioridad = $this->input->post('id_prioridad');
		$sqlWhere = [
			 'id_usuario' 	=> $this->session->userdata('id_usuario')
			,'id_estatus' 	=> $id_estatus ? explode(',', $id_estatus) : NULL
			,'id_prioridad' => $id_prioridad ? $id_prioridad : NULL
			,'fecha_inicio' => $this->input->post('fecha_inicio')
			,'fecha_fin' 	=> $this->input->post('fecha_fin')
			,'mc' 			=> 0
		];
		$tareas = $this->db_tareas->get_tareas_info($sqlWhere);
		if ($tareas) {
			$allUsers = $this->db_users->get_users();
			$team = [];
			foreach ($allUsers as $user) {
				$team[$user['id_usuario']] = $user;
			}
		}

		$id_usuario = $this->session->userdata('id_usuario');
		foreach ($tareas as &$tarea) {
			$dataEncription = json_encode(['oldData'=>$tarea]);
			$tarea['dataEncription'] = $this->encryption->encrypt($dataEncription);
			$responsables = explode(',', $tarea['id_responsables']);
			$participantes= explode(',', $tarea['id_participantes']);
			#$tarea['can-edit'] 	= (int)(in_array($id_usuario, array_merge($responsables, [$tarea['id_usuario_insert']])));
			#$tarea['read-only'] = (int)(in_array($id_usuario, explode(',', $tarea['id_participantes'])));
			$tarea['can-edit'] 	= (int)($id_usuario==$tarea['id_usuario_insert']);
			$tarea['read-only'] = (int)(!$tarea['can-edit'] && in_array($id_usuario, array_merge($responsables, $participantes)));
			$tarea['descripcion_corto'] = substr($tarea['descripcion'], 0, 100);
			$tarea['custom_estatus'] 	= $this->parser_view('dashboard/tpl/tpl-estatus', $tarea);
			$tarea['custom_prioridad'] 	= $this->parser_view('dashboard/tpl/tpl-prioridad', $tarea);
			$tarea['acciones'] 			= $this->parser_view('dashboard/tpl/tpl-actions-task', $tarea);

			$participantesData = [];
			foreach (explode(',', $tarea['id_participantes']) as $user) {
				if (isset($team[$user])) $participantesData[] = $team[$user];
			}
			$tarea['participantesData'] = $participantesData;
			$tarea['custom_participantes'] = $this->parser_view('dashboard/tpl/tpl-team', ['teamData'=>$participantesData]);

			$responsablesData = [];
			foreach (explode(',', $tarea['id_responsables']) as $user) {
				if (isset($team[$user])) $responsablesData[] = $team[$user];
			}
			$tarea['responsablesData'] = $responsablesData;
			$tarea['custom_responsable'] = $this->parser_view('dashboard/tpl/tpl-team', ['teamData'=>$responsablesData]);

			unset($tarea['dataEncription']);
			$response[] = $tarea;
		}

		echo json_encode($response);
	}

	public function process_save_task() {
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
			!$nuevosParticipantes OR self::save_participantes($nuevosParticipantes, $insert);

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

	public function process_remove_task() {
		try {
			$oldData = $this->input->post('oldData');
			$oldData OR set_exception();

			$sqlWhere = [
				 'id_tarea' 		=>$oldData['id_tarea']
				,'id_usuario_insert'=>$oldData['id_usuario_insert']
			];
			$update = $this->db_tareas->update_tarea(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_remove_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_update_task() {
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
			!$nuevosParticipantes OR self::save_participantes($nuevosParticipantes, $sqlWhere['id_tarea']);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('general_save_success'),
				'icon' 		=> 'success'
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	/**
	 * Guardamos los participantes de la tarea y le enviamos notificación por correo
	 **/
	public function save_participantes(array $participantes, $id_tarea) {
		$nuevos 	= [];
		$existente 	= [];
		foreach ($participantes as $participante) {
			$sqlData = ['activo'=>1];
			$sqlWhere= ['id_tarea'=>$id_tarea, 'id_participante'=>$participante];

			$update = $this->db_tareas->update_participante($sqlData, $sqlWhere);
			if (!$update) {
				$insert = $this->db_tareas->insert_participante(array_merge($sqlData, $sqlWhere));
				$insert OR set_exception();
				$nuevos[] = $participante;
			} else $existente[] = $participante;
		}

		/**
		 * @todo Enviar notificación por correo a los participantes de la tarea
		 * 
		 */
	}

	public function process_save_comment() {
		try {
			if(isset($_POST['oldData'])) $_POST['id_tarea'] = $_POST['oldData']['id_tarea'];
			isset($_POST['id_tarea']) OR set_exception();
			$sqlData = $this->input->post(['id_tarea', 'comentario']);
			$insert = $this->db_tareas->insert_comentario($sqlData);
			$insert OR set_exception();

			$dialog='';
			if ($this->input->post('returnDaigalog')) {
				$dataEncription = json_encode(['id_tarea_comentario'=>$insert, 'id_tarea'=>$_POST['id_tarea']]);
				$dialogData = [
					 'dataEncription' 			=> $this->encryption->encrypt($dataEncription)
					,'id_usuario_insert' 		=> $this->session->userdata('id_usuario')
					,'comentario' 				=> $this->input->post('comentario')
					,'usuario' 					=> $this->session->userdata('nombre_completo')
					,'custom_timestamp_insert' 	=> date(get_var('PHPtimeStamp'), strtotime(timestamp()))
				];
				$tplData = ['comentarios'=>[$dialogData]];
				$dialog = $this->parser_view('technojet/dashboard/tpl/tpl-comments', $tplData);
			}

			$response = [
				 'success'	=> TRUE
				,'msg' 		=> lang('general_save_success')
				,'icon' 	=> 'success'
				,'dialog' 	=> $dialog
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_comment_task() {
		$oldData = $this->input->post('oldData');
		$sqlWhere = ['id_tarea'=>$oldData['id_tarea']];
		$comentarios = $this->db_tareas->get_comentarios_info($sqlWhere);
		foreach ($comentarios as &$comentario) {
			$dataEncription = json_encode(['id_tarea_comentario'=>$comentario['id_tarea_comentario'], 'id_tarea'=>$comentario['id_tarea']]);
			$comentario['dataEncription'] = $this->encryption->encrypt($dataEncription);
		}

		$tplData['comentarios'] = $comentarios;
		return $this->parser_view('technojet/dashboard/tpl/tpl-comments', $tplData);
	}

	public function process_rm_comment() {
		try {
			isset($_POST['id_tarea']) OR set_exception();
			$sqlWhere = $this->input->post(['id_tarea_comentario', 'id_tarea']);
			$update = $this->db_tareas->update_comentario(['activo'=>0], $sqlWhere);
			$update OR set_exception();
			
			$response = [
				 'success'	=> TRUE
				,'msg' 		=> lang('general_row_rm_success')
				,'icon' 	=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}
}

/* End of file Tareas.php */
/* Location: ./application/modules/technojet/controllers/Tareas.php */