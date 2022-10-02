<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizaciones_model extends SB_Model {

	public function get_ultima_cotizacion(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_cotizacion) AS ulitmo_id
				,(IFNULL(MAX(id_cotizacion), 0)+1) AS proximo_id", FALSE)
			->from($tbl['cotizaciones'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_cotizaciones_main(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('CT.id_cotizacion', $where['id_cotizacion']);
		
		$request = $this->db->select("
			CT.id_cotizacion,
			CONCAT('CT-', CT.id_cotizacion) AS folio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_vigencia) as estatus_vigencia,
			CT.id_estatus_vigencia,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_entrega) as estatus_entrega,
			CT.id_estatus_entrega,
			DATE(CT.fecha_elaboracion) as fecha_elaboracion,
			CT.atencion,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.departamento) as depto,
			CT.departamento,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_condiciones_pago) as condiciones_pago,
			CT.id_condiciones_pago,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tiempo_entrega) as tiempo_entrega,
			CT.id_tiempo_entrega,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_lugar_entrega) as lugar_entrega,
			CT.id_lugar_entrega,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_vigencia) as vigencia,
			CT.id_vigencia,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_producto) as tipo_producto,
			CT.id_tipo_producto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_precio) as precio,
			CT.id_precio,
			(SELECT PI.id_cotizacion FROM $tbl[pi_mostrador] as PI WHERE CT.id_cotizacion = PI.id_cotizacion GROUP BY PI.id_cotizacion) as referencia_pi_mostrador,
			(SELECT PIF.id_cotizacion FROM $tbl[pi_factura] as PIF WHERE CT.id_cotizacion = PIF.id_cotizacion GROUP BY PIF.id_cotizacion) as referencia_pi_factura,
			CT.id_cliente,
			CL.razon_social,
			CL.rfc,
			CL.direccion,
			CL.municipio,
			CL.estado,
			CL.telefono,
			CL.cp,
			CL.contacto,
			CL.departamento as depto_cliente,
			CL.cliente,
			CT.id_moneda,
			MN.moneda,
			CT.id_vendedor,
			VE.vendedor,
			VE.departamento as depto_vendedor,
			VE.correo,
			CT.creador_cotizacion,
			DATE(CT.fecha_recepcion) as fecha_recepcion,
			WEEKOFYEAR(CT.fecha_elaboracion) as semana,
			YEAR(CT.fecha_elaboracion) as anio,
			MONTH(CT.fecha_elaboracion) as mes,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 1 and CTP.activo = 1) as solvente,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 2 and CTP.activo = 1) as tinta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 3 and CTP.activo = 1) as solucion_limpieza,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 4 and CTP.activo = 1) as cartucho_solvente,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 5 and CTP.activo = 1) as cartucho_tinta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 6 and CTP.activo = 1) as ribbon,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 7 and CTP.activo = 1) as kit_aditivos,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 8 and CTP.activo = 1) as etiqueta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 9 and CTP.activo = 1) as equipo,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 10 and CTP.activo = 1) as equipo_renta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 11 and CTP.activo = 1) as refaccion,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 12 and CTP.activo = 1) as servicio,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 13 and CTP.activo = 1) as accesorio,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 14 and CTP.activo = 1) as otro,", 
			FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->join("$tbl[monedas] AS MN", 'CT.id_moneda = MN.id_moneda', 'INNER')
			->join("$tbl[vendedores] AS VE", 'CT.id_vendedor = VE.id_vendedor', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_cotizaciones_consecutivo(array $where = [], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('CT.id_cotizacion', $where['id_cotizacion']);
		
		$request = $this->db->select("
			CT.id_cotizacion,
			CONCAT('CT-', CT.id_cotizacion) AS folio,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_vigencia) as estatus_vigencia,
			CT.id_estatus_vigencia,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_estatus_entrega) as estatus_entrega,
			CT.id_estatus_entrega,
			DATE(CT.fecha_elaboracion) as fecha_elaboracion,
			CT.atencion,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.departamento) as depto,
			CT.departamento,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_condiciones_pago) as condiciones_pago,
			CT.id_condiciones_pago,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tiempo_entrega) as tiempo_entrega,
			CT.id_tiempo_entrega,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_lugar_entrega) as lugar_entrega,
			CT.id_lugar_entrega,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_vigencia) as vigencia,
			CT.id_vigencia,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_tipo_producto) as tipo_producto,
			CT.id_tipo_producto,
			(SELECT c_cotizacion FROM $tbl[ventas_cotizaciones] as VC WHERE VC.id_ventas_cotizacion = CT.id_precio) as precio,
			CT.id_precio,
			CT.id_cliente,
			CL.razon_social,
			CL.cliente,
			CT.id_moneda,
			MN.moneda,
			CT.id_vendedor,
			VE.departamento as depto_vendedor,
			VE.vendedor,
			VE.correo,
			CT.creador_cotizacion,
			DATE(CT.fecha_recepcion) as fecha_recepcion,
			WEEKOFYEAR(CT.fecha_elaboracion) as semana,
			YEAR(CT.fecha_elaboracion) as anio,
			MONTH(CT.fecha_elaboracion) as mes,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 1) as solvente,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 2) as tinta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 3) as solucion_limpieza,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 4) as cartucho_solvente,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 5) as cartucho_tinta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 6) as ribbon,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 7) as kit_aditivos,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 8) as etiqueta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 9) as equipo,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 10) as equipo_renta,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 11) as refaccion,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 12) as servicio,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 13) as accesorio,
			(SELECT SUM(total) FROM $tbl[cotizaciones_productos] as CTP INNER JOIN $tbl[productos] as PR ON CTP.id_producto = PR.id_producto WHERE CTP.id_cotizacion = CT.id_cotizacion and PR.id_tipo_producto = 14) as otro,", 
			FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->join("$tbl[clientes] AS CL", 'CL.id_cliente = CT.id_cliente', 'INNER')
			->join("$tbl[monedas] AS MN", 'CT.id_moneda = MN.id_moneda', 'INNER')
			->join("$tbl[vendedores] AS VE", 'CT.id_vendedor = VE.id_vendedor', 'INNER')
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_cotizacion(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['cotizaciones'], $data);
		} else $this->db->insert_batch($tbl['cotizaciones'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_cotizacion(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['cotizaciones'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_cotizacion_producto(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['cotizaciones_productos'], $data);
		} else $this->db->insert_batch($tbl['cotizaciones_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_cotizacion_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_cotizacion_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['cotizaciones_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_cotizacion_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('TRP.id_cotizacion', $where['id_cotizacion']);
		$request = $this->db->select("
				TRP.id_cotizacion_producto,
				TRP.id_cotizacion,
				TRP.id_producto,
				TRP.cantidad,
				TRP.precio_unitario,
				TRP.descuento,
				TRP.total,
				
				TRP.comision_vendedor,
				CTP.tipo_producto,
				CUM.unidad_medida,				
				TP.no_parte,
				TRP.opcional,
				TP.descripcion", FALSE)
			->from("$tbl[cotizaciones_productos] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_cotizacion_productos_pdf(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('TRP.id_cotizacion', $where['id_cotizacion']);
		$request = $this->db->select("
				TRP.id_cotizacion_producto,
				TRP.id_cotizacion,
				TRP.id_producto,
				TRP.cantidad,
				TRP.precio_unitario,
				TRP.descuento,
				TRP.total,
				TRP.incluye,
				TRP.comision_vendedor,
				CTP.tipo_producto,
				CUM.unidad_medida,				
				TP.no_parte,
				TRP.opcional,
				TP.descripcion", FALSE)
			->from("$tbl[cotizaciones_productos] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_all_id_cotizaciones($all=TRUE){
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('CT.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('CT.id_cotizacion', $where['id_cotizacion']);
		
		$request = $this->db->select("
			CT.id_cotizacion,
			CONCAT('CT-', CT.id_cotizacion) AS folio",
			FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();

		/*$request = $this->db->select("CT.*", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('CT.activo', 1)
			->get();*/
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_cotizacion_select(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_cotizacion']) OR $this->db->where('id_cotizacion', $where['id_cotizacion']);

		$request = $this->db->select("
				CT.id_cotizacion,
				CONCAT('CT-', CT.id_cotizacion) AS folio,
				IF(id_cotizacion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/", FALSE)
			->from("$tbl[cotizaciones] AS CT")
			->where('activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_cotizacion_nota(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['cotizaciones_notas'], $data);
		} else $this->db->insert_batch($tbl['cotizaciones_notas'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function get_cotizacion_notas(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_cotizacion', $where['notIN']);
		!isset($where['id_cotizacion']) OR $this->db->where('TRP.id_cotizacion', $where['id_cotizacion']);
		$request = $this->db->select("
				TRP.id_nota,
				TRP.nota,
				TRP.descripcion", FALSE)
			->from("$tbl[cotizaciones_notas] AS TRP")
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function update_cotizacion_notas(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_nota', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['cotizaciones_notas'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function delete_cotizacion_notas(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['cotizaciones_notas'], $data, $where);

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