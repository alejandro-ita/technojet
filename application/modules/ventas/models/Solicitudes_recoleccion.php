<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudes_recoleccion extends SB_Model {

	public function get_ultima_solicitud(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_solicitud) AS ulitmo_id
				,(IFNULL(MAX(id_solicitud), 0)+1) AS proximo_id", FALSE)
			->from($tbl['solicitud_recoleccion'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_solicitudes_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_solicitud', $where['notIN']);
		!isset($where['id_solicitud']) OR $this->db->where('CT.id_solicitud', $where['id_solicitud']);
		
		$request = $this->db->select("
			CT.id_solicitud,
			CONCAT('SR-', CT.id_solicitud) AS folio,
            DATE(CT.fecha_solicitud) as fecha_solicitud,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus) as estatus,
			CT.id_estatus,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_departamento) as departamento,
			CT.id_departamento,			
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_envio) as tipo_envio,
			CT.id_tipo_envio,
            CT.consignado,
            CT.pi_nc_oc,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_paqueteria) as paqueteria,
			CT.id_paqueteria,
			CT.id_cliente,
			CL.razon_social,
            CL.cliente,
            CT.contacto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_condicion_entrega) as condicion_entrega,
			CT.id_condicion_entrega,
            CT.direccion,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_dep_solicitante) as dep_solicitante,
			CT.id_dep_solicitante,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_almacen_entrante) as almacen_saliente,
			CT.id_almacen_entrante,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_forma_pago) as forma_pago,
			CT.id_forma_pago,
			CT.observaciones,", 
			FALSE)
			->from("$tbl[solicitud_recoleccion] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[solicitud_entrega] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());



		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_solicitud(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['solicitud_recoleccion'], $data);
		} else $this->db->insert_batch($tbl['solicitud_recoleccion'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_solicitud(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['solicitud_recoleccion'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_producto(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['solicitud_recoleccion_prod'], $data);
		} else $this->db->insert_batch($tbl['solicitud_recoleccion_prod'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_solicitud_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_solicitud_recoleccion_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['solicitud_recoleccion_prod'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_solicitud_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_solicitud', $where['notIN']);
		!isset($where['id_solicitud']) OR $this->db->where('TRP.id_solicitud', $where['id_solicitud']);
		$request = $this->db->select("
				TRP.id_solicitud_recoleccion_producto,
				TRP.id_solicitud,
				TRP.id_producto,
				TRP.cantidad,
				CTP.tipo_producto,
				CUM.unidad_medida,				
				TP.no_parte,
				TP.descripcion", FALSE)
			->from("$tbl[solicitud_recoleccion_prod] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

}