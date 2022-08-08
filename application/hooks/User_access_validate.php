<?php if (!defined( 'BASEPATH')) exit('No direct script access allowed'); 
class User_access_validate {

	/**
	* Validación de sessión del usuario
	*/
	public function check_illegal_ajax() {
		$CI =& get_instance();
		$isLogged = $CI->session->userdata('isLogged');

		//BLOQUEO PETICIONES AJAX, RETORNA EL ESTATUS 401
        if (!$isLogged && $CI->input->is_ajax_request() && !self::validate_uri_whiteList()) {
        	set_status_header(401);
            die();
        }
	}

	private function query_uri_string($CI) {
		// Retorna el nombre del controlador al que se accesa vía URI
		$new_uri  	= "";
		$uriString  = $CI->uri->uri_string();		
		$uriString  = explode('/', $uriString);
		foreach($uriString as $value){
			if(!is_numeric($value)){
				$new_uri .= $value.'/';
			}
		}
		return trim($new_uri, '/');
	}

	public function check_authorized_sites() {
		$CI 			=& get_instance();
		$uriString 		= $this->query_uri_string($CI);
		$isLogged 		= $CI->session->userdata('isLogged');
		$CI->userAccess = [];

		if (!$CI->input->is_ajax_request() && !self::validate_uri_whiteList() && $isLogged) {
			$sqlWhere = $CI->session->userdata(['id_usuario']);
			$allLinks = $CI->db_users->get_user_access($sqlWhere);
	        if($allLinks) $CI->session->set_userdata([
	        	'id_perfil' 	=> $allLinks[0]['id_perfil'],
	        	'perfil'		=> $allLinks[0]['perfil'],
	        	'custom_perfil' => $allLinks[0]['perfil'],
	        	'crear' 		=> $allLinks[0]['crear'],
	        	'editar' 		=> $allLinks[0]['editar'],
	        	'eliminar' 		=> $allLinks[0]['eliminar']
	        ]);
	        if(!$allLinks) redirect('user-lock','refresh');

	        $CI->userAccess = $allLinks;
	        $whiteList = array_column($allLinks, 'link');
	        if(is_root()) return TRUE;

	        $urlDefault = $CI->session->userdata('urlDefault');
	        // debug($urlDefault);
	        $userAccess = in_array($uriString, $whiteList);

	        if(!$userAccess && $urlDefault == $uriString) $urlDefault = $whiteList[0];
	        $userAccess OR redirect($urlDefault,'refresh');
		
		#BLOQUEO DE INICIO DE SESION Y RECUPERACIÓN DE CONTRASEÑA
		} elseif($isLogged && in_array($uriString, ['', 'login', 'forgot-password'], TRUE)) {
	        $default_link = $CI->session->userdata('default_link');
			redirect(base_url($default_link));
		}
	}

	private function validate_uri_whiteList() {
		$CI 		=& get_instance();
		$uriString 	= $this->query_uri_string($CI);
		$whiteList = [ '' #DOMAIN
			,'error'
			,'login'
			,'logout'
			,'error-ie'
			,'user-lock'
	        ,'login/auth'
			,'404_override'
	        ,'error/error404'
	        ,'process_change_password'
		];
	    $isValid = in_array($uriString, $whiteList);
	    #URI con parametros
        if (!$isValid) {
        	$URI = ['pruebas', 'api\/', 'reset-password\/'];
        	foreach ($URI as $access) {
        		if ($isValid=preg_match("/^$access/", $uriString)) {
        			break;
        		}
        	}
        }

        $CI->urlWhiteList = $isValid;
        return $isValid;
	}

	private function debug($var = false){
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		die();
	}

}