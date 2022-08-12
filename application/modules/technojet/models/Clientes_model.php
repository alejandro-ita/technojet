<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes_model extends SB_Model {

	public function get_clientes_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

        !isset($where['notIN']) OR $this->db->where_not_in('CLI.id_cliente', $where['notIN']);
		!isset($where['id_cliente']) OR $this->db->where('CLI.id_cliente', $where['id_cliente']);
		!isset($where['cliente']) OR $this->db->where('CLI.cliente', $where['cliente']);

		$request = $this->db->select("CLI.*", FALSE)
			->from("$tbl[clientes] AS CLI")
			->where('CLI.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_cliente(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['clientes'], $data);
		} else $this->db->insert_batch($tbl['clientes'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_cliente(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['clientes'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}
}