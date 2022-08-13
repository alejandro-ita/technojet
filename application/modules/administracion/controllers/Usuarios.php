<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends SB_Controller {

	public function index() {
		$includes = get_includes_vendor(['dataTables', 'jQValidate', 'jstree']);
		$pathJS = get_var('path_js').'/administracion';
        $includes['modulo']['js'][] = ['name'=>'usuarios', 'dirname'=>$pathJS, 'fulldir'=>TRUE];

        $dataView['tpl-tools'] = $this->parser_view('administracion/usuarios/tpl/tpl-tools');

		$this->load_view('administracion/usuarios/usuarios_view', $dataView, $includes);
	}

	public function get_usuarios() {
		$sqlWhere['notIn'] = [1, $this->session->userdata('id_usuario')];
		$usuarios = $this->db_users->get_users($sqlWhere);

		$tplAcciones = $this->parser_view('administracion/usuarios/tpl/tpl-acciones');
		foreach ($usuarios as &$usuario) {
			$usuario['acciones'] = $tplAcciones;
		}

		echo json_encode($usuarios, JSON_NUMERIC_CHECK);
	}

	public function get_modal_new_usuario() {
		$sqlWhere = $this->session->userdata(['id_usuario']);
		$allLinks = $this->db_users->get_user_access($sqlWhere);
		$perfiles = $this->db_catalogos->get_perfiles(['notIN'=>1]);

		$userAccess = [
			 'text' 	=> lang('usuarios_select_all')
            ,'icon' 	=> 'd-none'
            ,'state' 	=> ['opened'=>TRUE]
            ,'children' => self::build_tree_menu($allLinks, 0, [1, 100])
            ,'li_attr'	=> ['data-id'=>0]
		];

		$dataView['perfiles'] = $perfiles;
		$dataView['userAccess'] = json_encode($userAccess, JSON_NUMERIC_CHECK);
		$this->parser_view('administracion/usuarios/tpl/modal-new-user', $dataView, FALSE);
	}

	private function build_tree_menu($listMenu=[], $id_padre=0, $selected=[]) {
		$treeMenu = [];
		foreach ($listMenu as $key => $menu) {
			if ($menu['id_padre']==$id_padre) {
				$treeMenu[] = [
					 'text' 	=> lang($menu['texto'])
					,'icon' 	=> 'd-none'
					,'li_attr'  => ['data-id'=>$menu['id_menu']]
					,'state' 	=> ['selected'=>in_array($menu['id_menu'], $selected)]
				];
				
				unset($listMenu[$key]);
				$children = self::build_tree_menu($listMenu, $menu['id_menu'], $selected);
				if ($children) {
					$lastMenu = array_key_last($treeMenu);
					$treeMenu[$lastMenu]['children'] = $children;
					$treeMenu[$lastMenu]['state']['opened'] = TRUE;
					$treeMenu[$lastMenu]['state']['selected'] = FALSE;
				}
			}

		}

		return $treeMenu;
	}

	public function process_save_user() {
		try {
			$sqlWhere = $this->input->post(['email']);
			$exist = $this->db_users->get_users($sqlWhere, FALSE);
			if (!$exist) {
				$sqlData = $this->input->post(['nombre', 'paterno', 'materno', 'email', 'crear', 'editar', 'eliminar', 'ids_menu']);
				$sqlData['id_perfil'] 	= 3; #COLABORADOR
				$sqlData['username'] 	= $sqlData['email'];
				$sqlData['token'] 		= md5($sqlData['email']);
				$sqlData['nombre'] 		= strtoupper($sqlData['nombre']);
				$sqlData['paterno'] 	= strtoupper($sqlData['paterno']);
				$sqlData['materno'] 	= strtoupper($sqlData['materno']);
				$sqlData['activo'] 		= 1;

				#OBTENEMOS LOS MENUS PADRE
				if ($sqlData['ids_menu']) {
					sort($sqlData['ids_menu']);
					$sqlData['ids_menu'] =  implode(',', $sqlData['ids_menu']);
				}

				$insert = 0;
				$update = $this->db_users->update_usuario($sqlData, ['email'=>$sqlData['email']]);
				if (!$update) {
					$insert = $this->db_users->insert_usuario($sqlData);
					$insert OR set_exception();
				}

				#NOTIFICACIÓN POR CORREO
				$nombre = trim(implode(' ', $this->input->post(['nombre', 'paterno', 'materno'])));
				$dataView = [
					'nombre_completo' => strtoupper($nombre),
					'link-check-email' => base_url("reset-password/$sqlData[token]")
				];
				$data = [
					 'email-body' => $this->parser_view('email/registro-colaborador', $dataView)
					,'asunto' 	=> lang('usuarios_check_user')
					,'para' 	=> [['nombre'=>$nombre, 'email'=>$sqlData['email']]]
				];
			
				// Send email
				$resultado = modules::run('technojet/correos/send_email', $data);
				
			} else set_alert(lang('usuarios_existe'));

			$actividad 		= "ha creado un usuario con correo: $sqlData[email]";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'sys_usuarios', $actividad, $data_change);
			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('usuarios_save_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_modal_update_usuario() {
		$sqlWhere = $this->input->post(['id_usuario']);
		$dataView = $this->db_users->get_users($sqlWhere, FALSE);
		$dataView['checked_crear'] = ($dataView['crear']?'checked':'');
		$dataView['checked_editar'] = ($dataView['editar']?'checked':'');
		$dataView['checked_eliminar'] = ($dataView['eliminar']?'checked':'');

		$sqlWhere = $this->session->userdata(['id_usuario']);
		$allLinks = $this->db_users->get_user_access($sqlWhere);
		$ids_menu = explode(',',  $dataView['ids_menu']);
		$userAccess = [
			 'text' 	=> lang('usuarios_select_all')
            ,'icon' 	=> 'd-none'
            ,'state' 	=> ['opened'=>TRUE, 'selected'=>FALSE/*in_array(0, $ids_menu)*/]
            ,'children' => self::build_tree_menu($allLinks, 0, $ids_menu)
            ,'li_attr'	=> ['data-id'=>0]
		];

		$sqlWhere = ['notIN'=>1, 'selected' =>$dataView['id_perfil']];
		$perfiles = $this->db_catalogos->get_perfiles($sqlWhere);
		$dataView['perfiles'] = $perfiles;

		$dataView['userAccess'] = json_encode($userAccess, JSON_NUMERIC_CHECK);
		unset($dataView['id_perfil'], $dataView['perfil']); #CORRECCIÓN DE CONFLICTOS
		$this->parser_view('administracion/usuarios/tpl/modal-update-user', $dataView, FALSE);
	}

	public function process_update_user() {
		try {
			$sqlWhere = $this->input->post(['id_usuario']);
			$sqlData = $this->input->post(['nombre', 'paterno', 'materno', 'crear', 'editar', 'eliminar', 'ids_menu']);
			if ($sqlData['ids_menu']) {
				sort($sqlData['ids_menu']);
				$sqlData['ids_menu'] =  implode(',', $sqlData['ids_menu']);
			}
			$sqlData['nombre'] = strtoupper($sqlData['nombre']);
			$sqlData['paterno'] = strtoupper($sqlData['paterno']);
			$sqlData['materno'] = strtoupper($sqlData['materno']);
			$update = $this->db_users->update_usuario($sqlData, $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha editado un usuario";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_usuario'], 'sys_usuarios', $actividad, $data_change);
			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('usuarios_update_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_user() {
		try {
			$sqlWhere = $this->input->post(['id_usuario', 'email']);
			$remove = $this->db_users->update_usuario(['activo'=>0], $sqlWhere);
			$remove OR set_exception();

			$userData = $this->input->post();
			$actividad 		= "ha eliminado un usuario con correo: $userData[email]";
			$data_change 	= ['delete'=>['oldData'=>$userData]];
			registro_bitacora_actividades($sqlWhere['id_usuario'], 'sys_usuarios', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('usuarios_remove_success'),
				'icon' 		=> 'success'
			];
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		echo json_encode($response);
	}
}

/* End of file Usuarios.php */
/* Location: ./application/modules/technojet/controllers/administracion/Usuarios.php */