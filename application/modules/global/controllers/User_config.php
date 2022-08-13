<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends SB_Controller {

	public function __construct() {
		parent::__construct();
		//Do your magic here
	}

	public function index($return=FALSE) {
        $dataView['reloadPage']     = $return;
        $dataView['includes-header'] = $this->parser_view('tpl-main/includes-header-view');
        $dataView['meta-view']       = $this->parser_view('tpl-main/meta-view');
        $dataView['preloader-view']  = $this->parser_view('tpl-main/preloader-view');

        $includes['js'][] = ['name' => 'login', 'dirname' => 'assets/js', 'fulldir' => 1];
        $dataView['includes-footer'] = $this->parser_view('tpl-main/includes-footer-view', [], 1 , $includes);
        $dataView['includes-vendor'] = $this->parser_view('tpl-main/includes-vendor-view');

        $lang = array_merge(
            $this->lang->fileContent['library'],
            $this->lang->fileContent['general'], 
            $this->lang->fileContent['login'], 
            $this->lang->fileContent['error']
        );
        $dataView['SYSTEM_LANG']     = json_encode($lang, JSON_HEX_APOS);
        $dataView['base_url']        = base_url();

        
        $this->parser_view('login/login_view', $dataView, 0);
	}

    public function logout() {
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
            $userData = $this->db_user->get_autentication($sqlData);

            //enviamos error de usuario|contraseña
            $userData OR set_alert(lang('clave_wrong'));

            //GENERAMOS LA SESSION
            self::setSession($userData);
            $response = array(
            	 'success' 	=> TRUE
            	,'redirect' => $this->session->userdata('urlDefault')
            );

        } catch (SB_Exception $e) {
            $response = get_exception($e); #Sin autorización
        }

        echo json_encode($response);
    }

	private function setSession($userData=array()) {
    	// Establece los datos de la sesión de usuario
        $sessionData = [
             'id_usuario'   => $userData['id_usuario']
            ,'language'     => 'mx'
            ,'id_seller'    => $userData['id_seller']
            ,'seller_id'    => $userData['seller_id'] #ID MELI
            ,'country_id'   => $userData['country_id'] ? $userData['country_id'] : 'mx'
            ,'nickname'     => $userData['nickname'] ? $userData['nickname'] : ''
            ,'picture_url'  => $userData['picture_url'] ? $userData['picture_url'] : base_url(get_var('path_img').'/profile.png')
            ,'full_name'    => "$userData[nombre] $userData[apellidos]"
            ,'nombre'       => $userData['nombre']
            ,'apellidos'    => $userData['apellidos']
            ,'id_perfil'    => $userData['id_perfil']
            ,'urlDefault'   => $userData['link']
            ,'email'        => $userData['email']
            ,'last_password_change' => $userData['last_password_change']
            ,'isLogged'     => 1
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