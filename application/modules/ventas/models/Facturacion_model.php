<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facturacion_model extends SB_Model {

	public function get_ultima_factura(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_factura) AS ulitmo_id
				,(IFNULL(MAX(id_factura), 0)+1) AS proximo_id", FALSE)
			->from($tbl['facturacion'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_facturas_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_factura', $where['notIN']);
		!isset($where['id_factura']) OR $this->db->where('CT.id_factura', $where['id_factura']);
		
		$request = $this->db->select("
			CT.id_factura,
			CONCAT('F-', CT.id_factura) AS folio,
			CT.id_estatus_factura,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_factura) as estatus_factura,
			DATE(CT.fecha_elaboracion) as fecha_elaboracion,
			CT.id_uso_cfdi,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_uso_cfdi) as uso_cfdi,
			CT.no_pi,
			CT.id_cliente,
			CL.razon_social,
			CT.subtotal,
			CT.descuento,
			CT.iva,
			CT.id_moneda,
			MN.moneda,
			CT.concepto,
			CT.id_metodo_pago,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_metodo_pago) as metodo_pago,
			CT.id_estatus_entrega,
			CT.semana,
			CT.mes,
			CT.anio", 
			FALSE)
			->from("$tbl[facturacion] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->join("$tbl[monedas] AS MN", 'CT.id_moneda = MN.id_moneda', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_factura(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['facturacion'], $data);
		} else $this->db->insert_batch($tbl['facturacion'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_factura(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['facturacion'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}
}
