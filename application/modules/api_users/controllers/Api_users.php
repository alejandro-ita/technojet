<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_users extends SB_Rest {

	public function __construct() {
		parent::__construct();

		$this->load->model('Users_model', 'db_users');
	}

	public function get_authentication() {
		try {
			$this->input->valueDefault = '0';
			$sqlWhere = $this->input->post(['username', 'password']);
	        $sqlWhere OR show_error(lang('general_api_forbidden'), 403);

	        // Busca el usuarios
	        $userData = $this->db_users->get_autentication($sqlWhere);
	        $userData OR set_alert(lang('clave_wrong'));

	        $userData['success'] = TRUE;
	        $response = $userData;
        } catch (SB_Exception $e) {
            $response = get_exception($e);
        }

		return $this->response($response);
	}

}

/* End of file Api_usuario.php */
/* Location: ./application/modules/api_users/Api_users.php */