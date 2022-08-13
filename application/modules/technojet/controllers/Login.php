<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends SB_Controller {

    public function __construct() {
        parent::__construct();
        //Do your magic here
    }

	public function index($reloadPage=FALSE) {
        #debug($this->session->userdata());
        $dataview['reloadPage']= $reloadPage;
        $dataview['page-preloader'] = $this->parser_view("tpl-main/page-preloader");

        $includes = get_includes_vendor(['md5', 'jQValidate']);
        $includes['modulo']['js'][] = ['name'=>'login', 'dirname'=>get_var('path_js'), 'fulldir'=> TRUE];
        $dataview = array_merge($dataview, $this->prepare_includes_vendor($includes));

		$this->parser_view('technojet/login', $dataview, FALSE);
	}

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url(''), 'refresh');
    }

    /**
     * Autenticación del usuario en el sistema.
     * @return JSON result
     */
    public function auth() {
        try {
            (isset($_POST['username']) && isset($_POST['password'])) OR set_exception(['message'=>lang('clave_wrong'), 'saveTrace'=>FALSE]);

            // Busca el usuario por medio del API
            $userData = modules::run('api_users/api_users/get_authentication');

            //enviamos error de usuario|contraseña
            $userData['success'] OR set_alert($userData['msg']);

            //GENERAMOS LA SESSION
            self::setSession($userData);
            $sqlWhere = $this->session->userdata(['id_usuario']);
            $this->db_users->update_usuario(['last_login'=>timestamp()], $sqlWhere);

            $response = ['success'=>TRUE, 'redirect'=>$userData['default_link']];
        } catch (SB_Exception $e) {
            $response = get_exception($e); #Sin autorización
        }

        echo json_encode($response);
    }

    private function setSession($userData=array()) {
        // Establece los datos de la sesión de usuario
        $sessionData = [
             'id_usuario'   => $userData['id_usuario']
            ,'language'     => 'es'
            ,'time_zone'    => $this->input->post('time_zone')
            ,'nombre_completo' => $userData['nombre_completo']
            ,'username'     => $userData['username']
            ,'email'        => $userData['email']
            ,'crear'        => $userData['crear']
            ,'editar'       => $userData['editar']
            ,'eliminar'     => $userData['eliminar']
            ,'id_perfil'    => $userData['id_perfil']
            ,'picture_link' => base_url(get_var('path_img').'/profile.png')
            ,'default_link' => $userData['default_link']
            ,'last_password_change' => $userData['last_password_change']
            ,'isLogged'     => 1
        ];
        
        $this->session->set_userdata($sessionData);
    }

    public function user_lock() {
        $dataView=$this->session->userdata();
        $this->parser_view('user-lock-screen', $dataView, FALSE);
    }

    public function reset_password($token) {
        $this->session->sess_destroy();
        $dataview['page-preloader'] = $this->parser_view("tpl-main/page-preloader");
        $includes = get_includes_vendor(['md5', 'jQValidate']);
        $includes['modulo']['js'][] = ['name'=>'login', 'dirname'=>get_var('path_js'), 'fulldir'=> TRUE];
        $dataview = array_merge($dataview, $this->prepare_includes_vendor($includes));

        $userData = $this->db_users->get_users(['token'=>$token]);
        if ($userData) {
            $dataview['token'] = $token;
            $this->parser_view('technojet/reset-password', $dataview, FALSE);

        } else $this->parser_view('errors/404', $dataview, FALSE);
    }

    public function process_change_password() {
        try {
            $sqlWhere = $this->input->post(['token']);
            $sqlData = [
                 'contrasenia'  => $this->input->post('password')
                ,'token'        => NULL
                ,'last_password_change' => timestamp()
            ];
            $update = $this->db_users->update_usuario($sqlData, $sqlWhere);
            $update OR set_exception();
            
            $response = [
                'success'   => TRUE,
                'msg'       => strtr(lang('login_note11'), ['{site_name}'=>get_var('site_name')]),
                'icon'      => 'success'
            ];
        } catch (SB_Exception $e) {
            $response = get_exception($e);
        }

        echo json_encode($response);
    }
}