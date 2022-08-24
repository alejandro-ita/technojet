<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notas_credito extends SB_Model {

	//Mostrador
	public function get_ultimo_pi_mostrador(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_nc_mostrador) AS ulitmo_id
				,(IFNULL(MAX(id_nc_mostrador), 0)+1) AS proximo_id", FALSE)
			->from($tbl['nc_mostrador'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_pi_mostrador_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_nc_mostrador', $where['notIN']);
		!isset($where['id_nc_mostrador']) OR $this->db->where('CT.id_nc_mostrador', $where['id_nc_mostrador']);
		
		$request = $this->db->select("
			CT.id_nc_mostrador,
			CONCAT('NCM-', CT.id_nc_mostrador) AS folio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_pi) as estatus_pi,
			CT.id_estatus_pi,
            CT.id_cotizacion,
			CT.id_cliente,
			CL.razon_social,
            CT.contacto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_departamento) as departamento,
			CT.id_departamento,
			DATE(CT.fecha_pi) as fecha_pi,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_medio) as medio,
			CT.id_medio,
			CT.id_oc as oc,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_forma_envio) as forma_envio,
			CT.id_forma_envio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_producto) as tipo_producto,
			CT.id_tipo_producto,
			CT.id_tipo_entrega,
			CT.incluir_iva,
			CT.notas_internas,
			CT.notas_remision,
			CT.fact_remision,
			CT.tipo_cambio,
			CT.motivo_credito,
			CT.observaciones,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_condiciones) as condiciones,
			CT.id_condiciones,", 
			FALSE)
			->from("$tbl[nc_mostrador] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_pi_mostrador(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['nc_mostrador'], $data);
		} else $this->db->insert_batch($tbl['nc_mostrador'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_pi(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['nc_mostrador'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_pi_producto(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['nc_mostrador_productos'], $data);
		} else $this->db->insert_batch($tbl['nc_mostrador_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_pin_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_nc_mostrador_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['nc_mostrador_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_pi_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_nc_mostrador', $where['notIN']);
		!isset($where['id_nc_mostrador']) OR $this->db->where('TRP.id_nc_mostrador', $where['id_nc_mostrador']);
		$request = $this->db->select("
				TRP.id_nc_mostrador_producto,
				TRP.id_nc_mostrador,
				TRP.id_producto,
				TRP.cantidad,
				TRP.precio_unitario,
				TRP.descuento_pieza,
                TRP.descuento_total,
				TRP.total,
				TRP.comision_vendedor,
				CTP.tipo_producto,
				CUM.unidad_medida,				
				TP.no_parte,
				TP.descripcion", FALSE)
			->from("$tbl[nc_mostrador_productos] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	//Facturas
	public function get_ultimo_pi_factura(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_nc_factura) AS ulitmo_id
				,(IFNULL(MAX(id_nc_factura), 0)+1) AS proximo_id", FALSE)
			->from($tbl['nc_factura'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_pi_factura_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_nc_factura', $where['notIN']);
		!isset($where['id_nc_factura']) OR $this->db->where('CT.id_nc_factura', $where['id_nc_factura']);
		
		$request = $this->db->select("
			CT.id_nc_factura,
			CONCAT('NCF-', CT.id_nc_factura) AS folio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_pi) as estatus_pi,
			CT.id_estatus_pi,
            CT.id_cotizacion,
			CT.id_cliente,
			CL.razon_social,
            CT.contacto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_departamento) as departamento,
			CT.id_departamento,
			DATE(CT.fecha_pi) as fecha_pi,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_medio) as medio,
			CT.id_medio,
			CT.oc,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_forma_envio) as forma_envio,
			CT.id_forma_envio,
			CT.notas_internas,
			CT.notas_facturacion,
			CT.tipo_cambio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_condiciones) as condiciones,
			CT.id_condiciones,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_uso_cfdi) as uso_cfdi,
			CT.id_uso_cfdi,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_forma_pago) as forma_pago,
			CT.id_forma_pago,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_metodo_pago) as metodo_pago,
			CT.id_metodo_pago,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_producto) as tipo_producto,
			CT.id_tipo_producto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_entrega) as tipo_entrega,
			CT.id_tipo_entrega,
			CT.motivo_credito,
			CT.email_factura,
			CT.fact_remision,
			CT.observaciones", 
			FALSE)
			->from("$tbl[nc_factura] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_pi_factura(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['nc_factura'], $data);
		} else $this->db->insert_batch($tbl['nc_factura'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function insert_pi_producto_factura(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['nc_factura_productos'], $data);
		} else $this->db->insert_batch($tbl['nc_factura_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function get_pi_factura_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_nc_factura', $where['notIN']);
		!isset($where['id_nc_factura']) OR $this->db->where('TRP.id_nc_factura', $where['id_nc_factura']);
		$request = $this->db->select("
				TRP.id_nc_factura_producto,
				TRP.id_nc_factura,
				TRP.id_producto,
				TRP.cantidad,
				TRP.precio_unitario,
				TRP.descuento_pieza,
                TRP.descuento_total,
				TRP.total,
				CTP.tipo_producto,
				CUM.unidad_medida,				
				TP.no_parte,
				TP.descripcion", FALSE)
			->from("$tbl[nc_factura_productos] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function update_pi_factura(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['nc_factura'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function update_pi_factura_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_nc_factura_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['nc_factura_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

}
