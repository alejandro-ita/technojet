<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro_actividades extends SB_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('Registro_actividades_model', 'db_actividades');
	}

	public function index() {
		$includes = get_includes_vendor(['moment', 'dataTables', 'dateRangePicker']);
		$pathJS = get_var('path_js').'/administracion';
        $includes['modulo']['js'][] = ['name'=>'registro-actividades', 'dirname'=>$pathJS, 'fulldir'=>TRUE];


		$tplData['date-start'] = date(get_var('PHPdateFormat'), strtotime(date('Y-m-d'). ' - 7 days'));
		$tplData['date-end'] 	= date(get_var('PHPdateFormat'), strtotime(date('Y-m-d')));
        $dataView['tpl-tools'] = $this->parser_view('administracion/registro-actividades/tpl/tpl-tools', $tplData);

		$this->load_view('administracion/registro-actividades/registro-actividades_view', $dataView, $includes);
	}

	public function get_registro_actividades() {
		$sqlWhere 	 = $this->input->post(['startDate', 'endDate']);
		$actividades = $this->db_actividades->get_registro_actividades($sqlWhere);
		// debug($actividades);
		$tplAcciones = $this->parser_view('administracion/registro-actividades/tpl/tpl-acciones');
		foreach ($actividades as &$actividad) {
			$actividad['timestamp_custom']  = $actividad['date_custom']."<br><small>$actividad[time_custom]</small>";
			$actividad['usuario_custom'] 	= $actividad['nombre_completo']."<br><small>$actividad[email]</small>";
			// $actividad['articulo_custom'] 	= $actividad['actividad']."<br><small>$actividad[no_parte]</small>";
			$actividad['acciones'] 			= $tplAcciones;
		}

		echo json_encode($actividades);
	}

	public function get_modal_registro_actividad() {
		$dataView = $this->input->post(['nombre_completo', 'actividad', 'id_registro']);
		$descripcion = $this->input->post('descripcion');
		$dataView['insert'] = 0;
		$dataView['update'] = 0;
		$dataView['delete'] = 0;

		if (isset($descripcion['insert'])) {
			$dataView['insert']  = 1;
			$dataView['newData'] = json_encode($descripcion['insert']['newData']);
		}

		if (isset($descripcion['update'])) {
			$dataView['update']  = 1;
			// $dataView['oldData'] = json_encode($descripcion['update']['oldData']);
			$dataView['newData'] = json_encode($descripcion['update']['newData']);
		}

		if (isset($descripcion['delete'])) {
			$dataView['delete']  = 1;
			$dataView['oldData'] = json_encode($descripcion['delete']['oldData']);
		}

		$this->parser_view('administracion/registro-actividades/tpl/modal-registro-actividad', $dataView, FALSE);
	}
}

/* End of file Registro_actividades.php */
/* Location: ./application/modules/technojet/controllers/administracion/Registro_actividades.php */