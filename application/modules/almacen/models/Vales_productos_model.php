<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vales_productos_model extends SB_Model {

	public function get_productos_min(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_categoria']) OR $this->db->where('VWP.id_categoria', $where['id_categoria']);
		$request = $this->db->select("
				 VWP.id_producto
				,VWP.id_categoria
				,VWP.no_parte
				,VWP.id_tipo_producto
				,VWP.descripcion
			", FALSE)
			->get("$tbl[vw_productos] AS VWP");

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_ultimo_vale_entrada(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_vale_entrada) AS ulitmo_id
				,(IFNULL(MAX(id_vale_entrada), 0)+1) AS proximo_id
			", FALSE)
			->from($tbl['vales_entrada'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_ultimo_vale_salida(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_vale_salida) AS ulitmo_id
				,(IFNULL(MAX(id_vale_salida), 0)+1) AS proximo_id
			", FALSE)
			->from($tbl['vales_salida'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_vales_entrada(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		!isset($where['id_vale_entrada']) OR $this->db->where('id_vale_entrada', $where['id_vale_entrada']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['vales_entrada']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vales_entrada(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_entrada'], $data);
		} else $this->db->insert_batch($tbl['vales_entrada'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_entrada(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_entrada'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_vales_activos(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_activos'], $data);
		} else $this->db->insert_batch($tbl['vales_activos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_activos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_activos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_vales_entrada_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_vale_entrada']) OR $this->db->where('id_vale_entrada', $where['id_vale_entrada']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['vales_entrada_productos']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vales_entrada_productos(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_entrada_productos'], $data);
		} else $this->db->insert_batch($tbl['vales_entrada_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_entrada_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_vale_entrada_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_entrada_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_vales_activos_productos(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_activos_productos'], $data);
		} else $this->db->insert_batch($tbl['vales_activos_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_activos_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_vale_activo_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_activos_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_vales_salida(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		!isset($where['id_vale_salida']) OR $this->db->where('id_vale_salida', $where['id_vale_salida']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['vales_salida']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vales_salida(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_salida'], $data);
		} else $this->db->insert_batch($tbl['vales_salida'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_salida(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_salida'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_vales_salida_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_vale_entrada']) OR $this->db->where('id_vale_entrada', $where['id_vale_entrada']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['vales_salida_productos']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vales_salida_productos(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_salida_productos'], $data);
		} else $this->db->insert_batch($tbl['vales_salida_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vales_salida_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_vale_salida_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_salida_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}
}

/* End of file vales_productos_model.php */
/* Location: ./application/modules/almacen/models/vales_productos_model.php */