<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacenes_vales_model extends SB_Model {

	public function get_vales_almacenes(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['tipo']) OR $this->db->where('tipo', $where['tipo']);
		!isset($where['almacen']) OR $this->db->where('almacen', $where['almacen']);
		!isset($where['notIn']) OR $this->db->where_not_in('id_vale_almacen', $where['notIn']);
		!isset($where['id_vale_almacen']) OR $this->db->where('id_vale_almacen', $where['id_vale_almacen']);
		$request = $this->db->select("
				 id_vale_almacen
				,almacen
				,tipo
				,IF(id_vale_almacen='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['vales_almacen']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vale_almacen(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_almacen'], $data);
		} else $this->db->insert_batch($tbl['vales_almacen'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vale_almacen(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_almacen'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_vales_estatus(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['tipo']) OR $this->db->where('tipo', $where['tipo']);
		!isset($where['estatus']) OR $this->db->where('estatus', $where['estatus']);
		!isset($where['notIn']) OR $this->db->where_not_in('id_vale_estatus', $where['notIn']);
		!isset($where['id_vale_estatus']) OR $this->db->where('id_vale_estatus', $where['id_vale_estatus']);
		$request = $this->db->select("
				 id_vale_estatus
				,estatus
				,tipo
				,IF(id_vale_estatus='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['vales_estatus']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_vale_estatus(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['vales_estatus'], $data);
		} else $this->db->insert_batch($tbl['vales_estatus'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_vale_estatus(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['vales_estatus'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

}

/* End of file Almacenes_vales_model.php */
/* Location: ./application/modules/technojet/models/Almacenes_vales_model.php */