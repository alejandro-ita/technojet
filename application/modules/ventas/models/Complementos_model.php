<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complementos_model extends SB_Model {

	public function get_ultimo_complemento(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_complemento_pago) AS ulitmo_id
				,(IFNULL(MAX(id_complemento_pago), 0)+1) AS proximo_id", FALSE)
			->from($tbl['complementos_pago'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_complementos_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_complemento_pago', $where['notIN']);
		!isset($where['id_complemento_pago']) OR $this->db->where('CT.id_complemento_pago', $where['id_complemento_pago']);
		
		$request = $this->db->select("
			CT.id_complemento_pago,
			CONCAT('CP-', CT.id_complemento_pago) AS folio,
			CT.id_estatus_complemento,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_complemento) as estatus_complemento,
			DATE(CT.fecha_complemento) as fecha_complemento,
			CT.id_cliente,
			CL.razon_social,
			CL.cliente,
			CT.id_factura,
			(SELECT CONCAT('F-', CT.id_factura) FROM $tbl[facturacion] as VC WHERE VC.id_factura = CT.id_factura) as factura,
			(SELECT DATE(fecha_elaboracion) FROM $tbl[facturacion] as VC WHERE VC.id_factura = CT.id_factura) as fecha_factura,
			DATE(CT.fecha_pago) as fecha_pago,
			CT.importe_pago,
			CT.importe_restante,
			CT.id_moneda,
			MN.moneda,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_numero_parcialidad) as num_parcialidad,
			CT.id_numero_parcialidad,
			CT.observaciones", 
			FALSE)
			->from("$tbl[complementos_pago] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->join("$tbl[monedas] AS MN", 'CT.id_moneda = MN.id_moneda', 'INNER')
			->where('CT.activo', 1)
			->get();

		
            /*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[complementos_pago] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_complemento(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['complementos_pago'], $data);
		} else $this->db->insert_batch($tbl['complementos_pago'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_complemento(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['complementos_pago'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}
}
