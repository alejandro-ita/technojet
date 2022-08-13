<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes_sistemas_model extends SB_Model {

	public function get_reportes_sistemas(array $where=[], $all=TRUE) {
		$tbl 	= $this->tbl;

		$request = $this->db->select("
				 TRS.*
				,DATE_FORMAT(TRS.fecha, '".get_var('MySQLdateFormat')."') AS custom_fecha
			", FALSE)
			->from("$tbl[reportes_sistemas] AS TRS")
			->where('TRS.activo', 1)
			->order_by('TRS.fecha', 'DESC')
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_ultimo_reporte_sistema(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_reporte_sistema) AS ulitmo_id
				,(IFNULL(MAX(id_reporte_sistema), 0)+1) AS proximo_id
			", FALSE)
			->from($tbl['reportes_sistemas'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_reporte_sistema(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['reportes_sistemas'], $data);
		} else $this->db->insert_batch($tbl['reportes_sistemas'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_reporte_sistema(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['reportes_sistemas'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_reporte_sistema_estados(array $where=[], $all=TRUE) {
		$tbl 	= $this->tbl;

		!isset($where['id_reporte_sistema']) OR $this->db->where('TRSE.id_reporte_sistema', $where['id_reporte_sistema']);
		$request = $this->db->select("TRSE.*", FALSE)
			->from("$tbl[reportes_sistemas_estado] AS TRSE")
			->where('TRSE.activo', 1)
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_reporte_sistema_estado(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['reportes_sistemas_estado'], $data);
		} else $this->db->insert_batch($tbl['reportes_sistemas_estado'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_reporte_sistema_estado(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['reportes_sistemas_estado'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_reporte_sistema_productos(array $where=[], $all=TRUE) {
		$tbl 	= $this->tbl;

		!isset($where['id_reporte_sistema']) OR $this->db->where('TRSP.id_reporte_sistema', $where['id_reporte_sistema']);
		$request = $this->db->select("TRSP.*, CUM.unidad_medida", FALSE)
			->from("$tbl[reportes_sistemas_productos] AS TRSP")
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TRSP.id_unidad_medida', 'LEFT')
			->where('TRSP.activo', 1)
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_reporte_sistema_productos(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['reportes_sistemas_productos'], $data);
		} else $this->db->insert_batch($tbl['reportes_sistemas_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_reporte_sistema_producto(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['reportes_sistemas_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

}

/* End of file Reportes_sistemas_model.php */
/* Location: ./application/modules/administracion/models/Reportes_sistemas_model.php */