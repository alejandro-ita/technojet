<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('debug')) {
	/**
	 * Funcion para imprimir el debug php en 2 formas
	 * 1.- print_r 
	 * 2.- var_dump
	 * defaul 1
	 * @param $data Datos a imprimir cualquier tipo de dato.
	 * @param INT $type Forma de mostrar el dato var_dump o print_r
	 * @param Bollean $die bandera para finalizar el proceso TRUE/FALSE
	 */
	function debug($data, $type = 1, $die = TRUE) {
		echo "<pre>";
		if ($type === 2) {
			var_dump($data);
		} else {
			print_r($data);
		}
		echo "</pre>";

		$die AND die();
	}
}

if(!function_exists('LogTxt')){
	function LogTxt($userData=array()) {
		$CI 	=& get_instance();

        $log_path_access= rtrim(config_item('log_path_access'), '/');
		file_exists($log_path_access) OR mkdir($log_path_access, DIR_WRITE_MODE, TRUE);
		
		$ip_loc = '';
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if($pos=strpos($_SERVER["HTTP_X_FORWARDED_FOR"], " ")) {
				$ip_loc = substr($_SERVER["HTTP_X_FORWARDED_FOR"], 0, $pos);
			}
	    }

		$archivo = "$log_path_access/log_$userData[id_pais_nomina]_$userData[id_empresa_nomina]_".date("Ymd").'.'.config_item('log_file_extension');
		$fp = fopen($archivo, "a+");

		$txtData = array(
			 'ID_COLABORADOR' 	=> $userData['id_colaborador']
			,'NOMBRE' 			=> $userData['nombre_completo']
			,'ID_PERFIL' 		=> isset($userData['id_perfil']) ? $userData['id_perfil'] : ''
			,'PERFIL' 			=> isset($userData['perfil']) ? $userData['perfil'] : ''
			,'ID_PAIS_NOMINA'	=> $userData['id_pais_nomina']
			,'ID_EMPRESA_NOMINA'=> $userData['id_empresa_nomina']
			,'IP_PUBLICA' 		=> $CI->input->ip_address()
			,'IP_LOCAL' 		=> $ip_loc
			,'NOMBRE_PC' 		=> $_SERVER['HTTP_HOST']
			,'NAVEGADOR' 		=> $_SERVER['HTTP_USER_AGENT']
			,'URL_ANTERIOR' 	=> (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : ''
			,'URL_ACTUAL' 		=> $_SERVER['PHP_SELF']
			,'URL_PARAMS'		=> (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : ''
		);
		$modoIncognito = $CI->session->userdata('modoIncognito');
		if ($modoIncognito) {
			$userDataLogIn = (array) json_decode($CI->session->userdata('userDataLogIn'));
			fclose($fp);
			$archivo = "$log_path_access/log_$userDataLogIn[id_pais_nomina]_$userDataLogIn[id_empresa_nomina]_".date("Ymd").'.'.config_item('log_file_extension');
			$fp = fopen($archivo, "a+");

			$txtData = array_merge([
				 'modoIncognito' => 'modo Incognito'
				,'userLogIn' 	 => "User logIn: $userDataLogIn[id_colaborador] - $userDataLogIn[nombre_completo]"
			], $txtData);
		}

		$log  = implode('|', array_merge(['FECHA'=>date("d-m-Y H:i:s")], $txtData));
		$log .= "\r\n";			
		$write = fputs($fp, $log);
		fclose($fp);
	}
}

if(!function_exists('authentication_validate')) {
    function authentication_validate() {
        $CI =& get_instance();
        if (isset($CI->urlWhiteList) && $CI->urlWhiteList) return;

        #CARGAMOS LA VISTA PARA LA AUTENTICACION DEL USUARIO
        $isLogged   = $CI->session->userdata('isLogged');
        if (!$isLogged && !$CI->input->is_ajax_request()) {
            $login = modules::run('technojet/login/index', TRUE);
            if ($login) {
	            echo $login;
	            die();
            } else die('Module controller failed to run: technojet/login/index');
        }
    }
}

if(!function_exists('set_exception')) {
	/**
	 * generamos la excepcion 
	 * @param String $msg description
	 * @param Intedger $code description
	 * @param Boolean $previous description
	 **/
	function set_exception($message='', $title=NULL, $typeMsg=NULL, $class=NULL) {
		#OPCIONES NECESARIOS
		$options = [
			 'message' 	=> $message
			,'title' 	=> $title
			,'typeMsg' 	=> $typeMsg
			,'class' 	=> $class
		];

		#SE AGREGAN NUEVAS OPCIONES A LA EXCEPCION
		$arguments = func_get_args();
		if (count($arguments)===1 && is_array($arguments[0])) {
			unset($options['message']);
			foreach ($arguments[0] as $key => $value) {
				$options[$key] = $value;
			}
		}

		throw new SB_Exception($options);
	}
}

if(!function_exists('get_exception')) {
	function get_exception($exception) {
		return [
			 'success' 	=> FALSE
			,'title' 	=> $exception->getTitle()
			,'msg' 		=> $exception->getMessage()
			,'icon' 	=> $exception->getTypeMessage()
		];
	}
}

if(!function_exists('set_alert')) {
	function set_alert($msg='', $title='', $typeMsg='warning') {
		$title = $title ? $title : lang('general_alerta');
		set_exception(['message'=>$msg, 'title'=>$title, 'typeMsg'=>$typeMsg, 'saveTrace'=>FALSE]);
	}
}

if(!function_exists('get_system_lang')) {
	 function get_system_lang() {
	 	$CI =& get_instance();
	 	$systemLang = json_encode($CI->lang->language, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK);

	 	# @url https://www.it-swarm-es.com/es/jquery/problema-al-recuperar-texto-en-formato-json-que-contiene-saltos-de-linea-con-jquery/958142651/
	 	$systemLang = str_replace(
	 		 ["\r", "\n", "<", ">", "&", "\'"] 					#SEARCH
    		,['\r', '\n', '\u003c', '\u003e', '\u0026', '\''] 	#REPLACE
    		,addslashes($systemLang) #SE AGREGÓ EL addslashes para poder parsear el JSON String del lado de JavaScript
    	);

		/*?>
	 	<script type="text/javascript">
	 		var strSystemConfig= '<?= $systemLang ?>';
	 		console.log(JSON.parse(strSystemConfig));

	 	</script>
	 	<?php
	 	exit;*/

        return $systemLang;
    }
}

function get_userData($key) {
	$CI =& get_instance();
	$userData = $CI->session->userdata();
	if ($key=='avatar') {
		$userData['avatar'] = strtoupper(substr($userData['username'], 0, 2));
	}

	return (isset($userData[$key])?$userData[$key]:'');
}

function get_avatar($username) {
	$avatar = strtoupper(substr($username, 0, 2));

	return $avatar;
}

if(!function_exists('timestamp')) {
	function timestamp($format='Y-m-d H:i:s') {
		$CI =& get_instance();
		$zona_horaria = $CI->session->userdata('time_zone')? $CI->session->userdata('time_zone') : 'America/Mexico_City';
		$date = new DateTime("now", new DateTimeZone($zona_horaria));  
		return $date->format($format);
	}
}

if(!function_exists('sanitizar_string')) {
	/**
	 * Reemplazó de caracteres especiales
	 */
	function sanitizar_string($string) {
	    $string = trim($string);
	 
	    $string = str_replace(
	        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
	        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
	        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
	        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
	        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
	        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ñ', 'Ñ', 'ç', 'Ç'),
	        array('n', 'N', 'c', 'C',),
	        $string
	    );
	 
	    $string = str_replace(
	        array("\\", "º", "-", "~",
	             "#", "@", "|", "!", "\"",
	             "·", "$", "%", "&", "/",
	             "(", ")", "?", "'", "¡",
	             "¿", "[", "^", "<code>", "]",
	             "+", "}", "{", "¨", "´",
	             ">", "< ", ";", ",", ":"),
	        '-',
	        $string
	    );
	    
		return $string;
	}
}

if(!function_exists('remove_eol')) {
	/**
	 * Removelos los daltos de linea|tabuladores de un String
	 * @param  String|Array $data 
	 */
	function remove_eol(&$data) {
	    if (is_array($data)) {
	    	foreach ($data as &$val) {
				$val = str_replace(array("\r\n\t", "\r", "\n", "\t"), '', $val);
	    	}
	    } else {
	    	$data = str_replace(array("\r\n\t", "\r", "\n", "\t"), '', $data);
	    }
	}
}

if(!function_exists('is_root')) {
	function is_root() {
		$CI 	=& get_instance();
		$perfil = $CI->session->userdata('id_perfil');
		return (md5(strtolower($perfil)) == 'c4ca4238a0b923820dcc509a6f75849b');
	}
}

if(!function_exists('show_404_on_illegal_ajax')) {
	function show_404_on_illegal_ajax() {
		$ci =& get_instance();
		if(!$ci->input->is_ajax_request()) show_404();
	}
}

if(!function_exists('get_includes_vendor')) {
	function get_includes_vendor(array $includes=[]) {
		$config =& get_config();
		$vendor = $config['vendor'];
		array_unshift($includes, 'core_theme');
		array_push($includes, 'core_SB', 'components');
		$response = [];

		foreach ($includes as $include) {
			if (isset($vendor[$include])) {
				$response = array_merge_recursive($response, $vendor[$include]);
			}
		}

		return $response;
	}
}

if (!function_exists('registro_bitacora_actividades')) {
	function registro_bitacora_actividades($id_registro, $tabla, $actividad, $data_change) {
		$CI =& get_instance();
		if (!is_root()) {
			$ip_local = '';
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				if($pos=strpos($_SERVER["HTTP_X_FORWARDED_FOR"], " ")) {
					$ip_local = substr($_SERVER["HTTP_X_FORWARDED_FOR"], 0, $pos);
				}
		    }

			$sqlData = [
				 'id_registro' 	=> $id_registro
				,'tabla' 		=> $tabla
				,'actividad' 	=> $actividad
				,'data_change' 	=> json_encode($data_change)
				,'browser'		=> $_SERVER['HTTP_USER_AGENT']
				,'ip' 			=> $ip_local
			];
			$insert = $CI->db_users->insert_bitacora_actividades($sqlData);
			$insert OR log_message('error', 'Error al registrar en la bitacora de actividades, DATA: '. json_encode($sqlData));
		}
	}
}

if (!function_exists('compare_data_productos')) {
	function compare_data_productos($oldData, $newData) {
		$arrayDiff = ['oldData'=>[], 'newData'=>[]];
		foreach ($newData as $index => $value) {
			if ($value != $oldData[$index]) {
				$arrayDiff['oldData'][$index] = $oldData[$index];
				$arrayDiff['newData'][$index] = $value;
			}
		}
		
		return ($arrayDiff['oldData'] ? $arrayDiff : []);
	}
}

#For PHP <= 7.3.0 :
if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }
       
        return array_keys($array)[count($array)-1];
    }
}
/* End of file System_helper.php */
/* Location: ./application/helpers/System_helper.php */