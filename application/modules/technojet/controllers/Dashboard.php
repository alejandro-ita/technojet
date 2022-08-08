<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends SB_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$includes = get_includes_vendor(['fullcalendar', 'moment', 'css-skeletons', 'jQValidate']);
		
		$pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name'=>'tareas', 'dirname'=>"$pathJS/dashboard", 'fulldir'=>TRUE];
        $includes['modulo']['js'][] = ['name'=>'calendario', 'dirname'=>"$pathJS/dashboard", 'fulldir'=>TRUE];
		$this->load_view('dashboard/dashboard-view', [], $includes);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/modules/technojet/controllers/Dashboard.php */