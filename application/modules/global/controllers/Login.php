<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends SB_Controller {

	public function __construct() {
		parent::__construct();
		//Do your magic here
	}

	public function index($return=FALSE) {
        $this->set_language_subdomain();
        $dataView['reloadPage']     = $return;
        $includes['js'][]           = array('name'=>'login', 'dirname'=>get_var('path_js'), 'fulldir'=>TRUE);
        $dataView['includes-header']= $this->parser_view("auth/includes-header");     
        $dataView['includes-footer']= $this->parser_view("auth/includes-footer", [], TRUE, $includes);     
        $dataView['PRELOADER']      = $this->parser_view("tpl-main/preloader");
        $dataView['meta-social-media']      = $this->parser_view("tpl-main/meta-social-media"); 
        $URL_ACCESS             = $this->get_url_user_access();
        $dataView['URL_ACCESS'] = json_encode($URL_ACCESS);
        $dataView['SYSTEM_LANG']= json_encode($this->lang->language, JSON_HEX_APOS);
        $configPais = get_var(FALSE, [], "config");
        $parse['SYSTEM_CONFIG']= json_encode($configPais, JSON_HEX_APOS);
        $dataView['year'] = date('Y');

        $dataView['img_arch'] = ($_SERVER['HTTP_HOST'] == get_var('subdomain1')) ? '_archone' : '';

		$view = $this->parser_view('auth/login', $dataView);

        if ($return) return $view;
        echo $view;
	}

    public function logout() {
        if($this->session->userdata('id_colaborador')) $this->insertInbox(3,3,null,null);
        $this->session->sess_destroy();
        redirect(base_url('login'), 'refresh');
    }

    /**
     * Autenticación del usuario en el sistema.
     * @return JSON result
     */
    function auth() {
        try {
            $_POST OR set_exception(['message'=>lang('clave_wrong'), 'saveTrace'=>FALSE]);

            $sqlData = $this->input->post(['usuario', 'password']);
            isset($sqlData) OR show_error('Method access is forbidden.', 403);

            // Busca el usuarios
            $userData = $this->db_login->get_autentication($sqlData);

            //enviamos error de usuario|contraseña
            count($userData) OR set_alert(lang('clave_wrong'));

            //GENERAMOS LA SESSION
            self::setSession($userData);
            $response = array(
            	 'success' 	=> TRUE
            	,'redirect' => $userData['urlDefault']
            );

        } catch (SB_Exception $e) {
            $response = get_exception($e); #Sin autorización
        }

        echo json_encode($response);
    }

	private function setSession($userData=array()) {

    	// Establece los datos de la sesión de usuario
        $sessionData = [
             'id_colaborador'    => $userData['id_colaborador']
            ,'id_perfil'         => $userData['id_perfil']
            ,'nombre_completo'   => $userData['full_name']
            ,'urlDefault'        => $userData['urlDefault']
            ,'email'             => $userData['mail']
        ];
        
        $this->session->set_userdata($sessionData);
    }

    public function user_lock() {
        $dataView=$this->session->userdata();
        $this->parser_view('user-lock-screen', $dataView, FALSE);
    }
}


/* End of file Login.php */
/* Location: ./application/controllers/Login.php */