<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalogos extends SB_Controller {

	public function get_almacenes_categorias() {
		$id_uso = $this->input->post('id_uso');
		switch ($id_uso) {
			case 1: #NUEVO
			case 2: #USADO
				$sqlWhere['grupo'] = 3;
				$sqlWhere['notIN'] = [14, 16];
				$response = $this->db_catalogos->get_categorias($sqlWhere);
				break;

			case 3: #uso diario
				$sqlWhere['grupo'] = 1;
				$sqlWhere['notIN'] = [4];
				$response = $this->db_catalogos->get_categorias($sqlWhere);
				break;

			case 4: #producción
				$sqlWhere['grupo'] = 1;
				$sqlWhere['id_categoria'] = 4;
				$response = $this->db_catalogos->get_categorias($sqlWhere);
				break;
			
			default:
				$response = [];
				break;
		}

		echo json_encode($response);
	}

}

/* End of file Catalogos.php */
/* Location: ./application/modules/technojet/controllers/Catalogos.php */