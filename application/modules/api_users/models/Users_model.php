<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends SB_Model {

	public function get_autentication(array $where, $all=FALSE) {
		$tbl = $this->tbl;
	
		$request = $this->db->select("
				SU.id_usuario,
				CONCAT_WS(' ', IFNULL(SU.nombre, ''), IFNULL(SU.paterno, ''), IFNULL(SU.materno, '')) AS nombre_completo,
				SU.username,
				SU.id_perfil,
				SU.email,
				IFNULL(SU.crear, 0) AS crear,
				IFNULL(SU.editar, 0) AS editar,
				IFNULL(SU.eliminar, 0) AS eliminar,
				SU.last_password_change,
				SM.link AS default_link
			", FALSE)
			->from("$tbl[usuarios] AS SU")
			->join("$tbl[perfiles] AS SP", 'SP.id_perfil=SU.id_perfil', 'LEFT')
			->join("$tbl[menu] AS SM", 'SM.id_menu=SP.id_menu_default', 'LEFT')
			->where('SU.activo', 1)
			->where('MD5(SU.username)', $where['username']) #DATA ENCRIPTADO EN MD5
			->where('SU.contrasenia', $where['password']) #DATA ENCRIPTADO EN MD5
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_user_access(array $where, $all=TRUE) {
		$tbl 	= $this->tbl;
		$isroot = "((ISNULL(SP.ids_menu) OR SP.ids_menu='') AND SP.id_perfil=1)";

		!isset($where['ids_menu']) OR $this->db->where_in('SM.id_menu', $where['ids_menu']);
		$request = $this->db->distinct()
			->select("
				 SU.id_perfil
				,SP.perfil
				,SM.id_menu
				,SM.texto
				,SM.link
				,SM.icono
				,SM.tipo
				,SM.id_padre
				,SM.seccion
				,IFNULL(SU.crear, 0) AS crear
				,IFNULL(SU.editar, 0) AS editar
				,IFNULL(SU.eliminar, 0) AS eliminar
			", FALSE)
			->from("$tbl[usuarios] AS SU")
			->join("$tbl[perfiles] AS SP", 'SP.id_perfil=SU.id_perfil', 'LEFT')
			->join("$tbl[menu] AS SM", "FIND_IN_SET(SM.id_menu, IF($isroot, SM.id_menu, IFNULL(CONCAT(SU.ids_menu, ',19'), SP.ids_menu)))", 'LEFT', FALSE)
			->where('SU.id_usuario', $where['id_usuario'])
			->where('SU.activo', 1)
			->where('SM.activo', 1)
			->group_by('SM.id_menu')
			->order_by('SM.id_padre, SM.orden')
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_usuario(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] = $this->session->userdata('id_usuario');
			$data['timestamp_insert'] = timestamp();
			$this->db->insert($tbl['usuarios'], $data);
		} else $this->db->insert_batch($tbl['usuarios'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_usuario(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['usuarios'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_users(array $where=[], $all=TRUE) {
		$tbl 	= $this->tbl;

		!isset($where['notIn']) OR $this->db->where_not_in('SU.id_usuario', $where['notIn']);
		!isset($where['id_usuario']) OR $this->db->where('SU.id_usuario', $where['id_usuario']);
		!isset($where['email']) OR $this->db->where('SU.email', $where['email']);
		!isset($where['token']) OR $this->db->where('SU.token', $where['token']);
		$request = $this->db->distinct()
			->select("
				 SU.id_usuario
				,SU.nombre
				,SU.paterno
				,IFNULL(SU.materno, '') AS materno
				,CONCAT_WS(' ', SU.nombre, SU.paterno, IFNULL(SU.materno, '')) AS nombre_completo
				,SU.email
				,SU.username
				,SU.id_perfil
				,SP.perfil
				,SU.ids_menu
				,SU.last_login
				,IFNULL(SU.crear, 0) AS crear
				,IFNULL(SU.editar, 0) AS editar
				,IFNULL(SU.eliminar, 0) AS eliminar
				,DATE_FORMAT(SU.last_login, '".get_var('MySQLtimeStamp')."') AS custom_last_login
			", FALSE)
			->from("$tbl[usuarios] AS SU")
			->join("$tbl[perfiles] AS SP", 'SP.id_perfil=SU.id_perfil', 'LEFT')
			->where('SU.activo', 1)
			#->where('SU.id_perfil !=', 1)
			->order_by('nombre_completo')
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_bitacora_actividades(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario'] = $this->session->userdata('id_usuario');
			$data['timestamp'] = timestamp();
			$this->db->insert($tbl['bitacora_actividades'], $data);
		} else $this->db->insert_batch($tbl['bitacora_actividades'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}
}

/* End of file Users_model.php */
/* Location: ./application/models/Users_model.php */