<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuenta extends SB_Controller {

	public function index() {
		$this->load_view('administracion/cuenta/cuenta_view');
	}

}

/* End of file Cuenta.php */
/* Location: ./application/modules/technojet/controllers/administracion/Cuenta.php */