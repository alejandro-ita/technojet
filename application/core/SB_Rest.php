<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ************************************************
 * !!!!!!!!!!!!!!!!!!!!!!ALERTA!!!!!!!!!!!!!!!!!!!!
 * ************************************************
 * Cada vez que se ejecuta el [[[composer install|update]] es necesario modificar el archivo de yidas\rest\Controller
 * Cambiar el [[extends \CI_Controller]] por [[extends \MX_Controller]] a la CLASE Controller
 * esto es para poder ocupar el HMVC en las APIS
 * ************************************************
 */
class SB_Rest extends yidas\rest\Controller {

	public function __construct() {
		parent::__construct();

		$this->isWeb = $this->input->post('isWeb');
		if (!$this->isWeb) {
			self::authentication();
			self::set_response_format();
		}
	}

	private function authentication() {
		try {
			switch (get_var('api_authType')) {
				case 'basic':
					if (get_var('api_auth_db')) {
						list($form_id, $key) = $this->request->getAuthCredentialsWithBasic();
						$sqlWhere = ['form_id'=>$form_id, 'key'=>$key];
						$formData = $this->db_facebook->get_page_form($sqlWhere);
						$formData OR set_alert(lang('api_error_auth'));
						if (!trim($key) || $formData['archivada']) {
							$this->status = 403;
							set_alert(lang('api_error_forbidden'));
						}
						$sqlWhere = ['page_id'=>$formData['page_id']];
						$pageData = $this->db_facebook->get_users_pages($sqlWhere);
						if ($pageData['archivada']) {
							$this->status = 403;
							set_alert(lang('api_error_forbidden'));
						}

						$_POST['page_id'] = $formData['page_id'];
						$_POST['form_id'] = $formData['form_id'];
					} else {
						list($username, $password) = $this->request->getAuthCredentialsWithBasic();
						if ($username!==get_var('api_user') || $password!==get_var('api_pass'))
							set_exception(lang('api_error_auth'));
					}
					break;

				default: #apikey
			 		$keyAuth = $this->input->get_request_header('keyAuth');

					if ($keyAuth!==get_var('api_keyAuth'))
						set_exception(lang('api_error_auth'));
			 		break; 
			}
		} catch (SB_Exception $e) {
			$statusCode = isset($this->status) ? $this->status : 401;
			$data = $this->pack([], $statusCode, $e->getMessage());
			return $this->response->json($data, 202);
		}

		return TRUE;
	}

	private function set_response_format() {
		$format = $this->input->post('format');

		switch ($format) {
			case 'raw':
				$this->format = 'raw'; 
				$responseFormat = \yidas\http\Response::FORMAT_RAW;
				break;
			case 'html':
				$this->format = 'html'; 
				$responseFormat = \yidas\http\Response::FORMAT_HTML;
				break;
			case 'xml':
				$this->format = 'xml'; 
				$responseFormat = \yidas\http\Response::FORMAT_XML;
				break;
			case 'jsonp':
				$this->format = 'jsonp'; 
				$responseFormat = \yidas\http\Response::FORMAT_JSONP;
				break;
			
			default:
				$this->format = 'json'; 
				$responseFormat = \yidas\http\Response::FORMAT_JSON;
				break;
		}

		$this->response->setFormat($responseFormat);
	}

	protected function response(array $data, $code=200) {
		if (!$this->isWeb) {
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
			header("Access-Control-Allow-Origin: *");
			header("X-Frame-Options: 'deny'");
	    	#$this->response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
	    	#$this->response->withAddedHeader('Access-Control-Allow-Orign', '*');
	    	#$this->response->withAddedHeader('X-Frame-Options', 'deny');

			switch ($this->format) {
				case 'xml':
					$response = array_to_xml($data);
					break;

				case 'html':
					$response = $data;
					break;
				
				default: #json
					$response = $data;
					break;
			}

			$this->response->setStatusCode($code);
			$this->response->setdata($response);
			$this->response->send();
		} else return $data;
	}
}

/* End of file IS_Rest.php */
/* Location: ./application/core/IS_Rest.php */