<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas_productos_model extends SB_Model {

	public function get_producto_min(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) 			OR $this->db->where_not_in('TP.id_producto', $where['notIN']);
		!isset($where['id_producto']) 		OR $this->db->where('TP.id_producto', $where['id_producto']);
		!isset($where['id_categoria']) 		OR $this->db->where('TP.id_categoria', $where['id_categoria']);
		!isset($where['no_parte']) 			OR $this->db->where('TP.no_parte', $where['no_parte']);
		!isset($where['id_tipo_producto']) 	OR $this->db->where('TP.id_tipo_producto', $where['id_tipo_producto']);
		!isset($where['id_unidad_medida']) 	OR $this->db->where('TP.id_unidad_medida', $where['id_unidad_medida']);
		!isset($where['grupo']) 			OR $this->db->where('CC.grupo', $where['grupo']);
		!isset($where['id_sitio']) 			OR $this->db->where('TP.id_sitio', $where['id_sitio']);
		$request = $this->db->select("
				 TP.*,
				,CM.moneda
				,CONCAT(CM.moneda, '(', CM.clave,')') AS custom_moneda
				,CUM.unidad_medida
				,CONCAT(CUM.unidad_medida, '(', CUM.clave, ')') AS custom_unidad_medida
				,CC.categoria
				,CTP.tipo_producto
			", FALSE)
			->from("$tbl[productos] AS TP")
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->join("$tbl[categorias] AS CC", 'CC.id_categoria=TP.id_categoria', 'LEFT')
			->join("$tbl[monedas] AS CM", 'CM.id_moneda=TP.id_moneda', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TP.activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_producto(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['productos'], $data);
		} else $this->db->insert_batch($tbl['productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_producto(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['productos'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_productos_vales_entrada(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_uso']) OR $this->db->where('TVE.id_uso', $where['id_uso']);
		!isset($where['id_categoria']) OR $this->db->where('TVE.id_categoria', $where['id_categoria']);
		!isset($where['id_vale_entrada']) OR $this->db->where('TVE.id_vale_entrada', $where['id_vale_entrada']);
		$request = $this->db->select("
				 TVE.id_vale_entrada
				,TVEP.id_vale_entrada_producto
				,CONCAT('VE-', TVE.id_vale_entrada) AS folio
				,TVE.cliente
				,TVE.id_requisicion
				,CONCAT('RQ-', TVE.id_requisicion) AS requisicion
				,TVE.vale_salida_correspondiente
				,TVE.observaciones
				,TVEP.referencia_entrada
				,TVEP.referencia_alfanumerica
				,DATE_FORMAT(TVE.fecha_hora, '".get_var('MySQLdateFormat')."') AS custom_fecha
				,TVEP.id_producto
				,TVEP.cantidad
				,TP.no_parte
				,TP.descripcion
				,CUM.unidad_medida
				,TVE.concepto_entrada
				,TVE.recibio
				,TVE.entrego
				,TVE.vo_bo
				,CVE.id_vale_estatus
				,CVE.estatus
				,TVA.id_vale_almacen
				,TVA.almacen AS vale_almacen
				,CTP.tipo_producto
				,TVE.id_ve_tipo_entrada
			", FALSE)
			->from("$tbl[vales_entrada] AS TVE")
			->join("$tbl[vales_entrada_productos] AS TVEP", 'TVEP.id_vale_entrada=TVE.id_vale_entrada', 'LEFT')
			->join("$tbl[productos] AS TP", 'TP.id_producto=TVEP.id_producto', 'LEFT')
			->join("$tbl[vales_almacen] AS TVA", 'TVA.id_vale_almacen=TVE.id_vale_almacen', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->join("$tbl[vales_estatus] AS CVE", 'CVE.id_vale_estatus=TVE.id_vale_estatus', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->where('TVE.activo', 1)
			->where('TVEP.activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_productos_vales_activos(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['tipo']) OR $this->db->where('TVA.tipo', $where['tipo']);
		!isset($where['id_uso']) OR $this->db->where('TVA.id_uso', $where['id_uso']);
		!isset($where['id_vale_activo']) OR $this->db->where('TVA.id_vale_activo', $where['id_vale_activo']);
		$request = $this->db->select("
				 TVA.id_vale_activo
				,TVAP.id_vale_activo_producto
				,CONCAT('VE-', TVA.id_vale_activo) AS folio
				,TVA.cliente
				,TVA.pedido_interno
				,TVA.id_requisicion
				,CONCAT('RQ-', TVA.id_requisicion) AS requisicion
				,TVA.observaciones
				,TVAP.estado_producto
				,TVAP.no_serie
				,DATE_FORMAT(TVA.fecha_hora, '".get_var('MySQLdateFormat')."') AS custom_fecha
				,TVAP.cantidad
				,TVAP.no_parte
				,TVAP.descripcion
				,TVAP.costo
				,CUM.id_unidad_medida
				,CUM.unidad_medida
				,TVA.concepto
				,TVA.concepto AS concepto_entrada
				,TVA.concepto AS concepto_salida
				,TVA.recibio
				,TVA.autorizo
				,TVA.entrego
				,TVA.vo_bo
				,CVE.id_vale_estatus
				,CVE.estatus
				,TVA.id_vale_almacen
				,TVAL.almacen AS vale_almacen
				,CTP.id_tipo_producto
				,CTP.tipo_producto
				,TVA.id_ve_tipo_entrada
				,CM.id_moneda
				,CM.moneda
				,CONCAT(CM.moneda, '(', CM.clave,')') AS custom_moneda
			", FALSE)
			->from("$tbl[vales_activos] AS TVA")
			->join("$tbl[vales_activos_productos] AS TVAP", 'TVAP.id_vale_activo=TVA.id_vale_activo', 'LEFT')
			->join("$tbl[vales_almacen] AS TVAL", 'TVAL.id_vale_almacen=TVA.id_vale_almacen', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TVAP.id_unidad_medida', 'LEFT')
			->join("$tbl[vales_estatus] AS CVE", 'CVE.id_vale_estatus=TVA.id_vale_estatus', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TVAP.id_tipo_producto', 'LEFT')
			->join("$tbl[monedas] AS CM", 'CM.id_moneda=TVAP.id_moneda', 'LEFT')
			->where('TVA.activo', 1)
			->where('TVAP.activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_productos_vales_salida(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_uso']) OR $this->db->where('TVS.id_uso', $where['id_uso']);
		!isset($where['id_categoria']) OR $this->db->where('TVS.id_categoria', $where['id_categoria']);
		!isset($where['id_vale_salida']) OR $this->db->where('TVS.id_vale_salida', $where['id_vale_salida']);
		$request = $this->db->select("
				 TVS.id_vale_salida
				,CONCAT('VS-', TVS.id_vale_salida) AS folio
				,TVS.cliente
				,TVS.pedido_interno
				,TVS.observaciones
				,DATE_FORMAT(TVS.fecha_hora, '".get_var('MySQLdateFormat')."') AS custom_fecha
				,TVSP.id_producto
				,TVSP.cantidad
				,TP.no_parte
				,TP.descripcion
				,CUM.unidad_medida
				,TVS.concepto_salida
				,TVS.recibio
				,TVS.entrego
				,TVSP.referencia_salida
				,TVS.vo_bo
				,CVE.id_vale_estatus
				,CVE.estatus
				,TVA.id_vale_almacen
				,TVA.almacen AS vale_almacen
				,CTP.tipo_producto
			", FALSE)
			->from("$tbl[vales_salida] AS TVS")
			->join("$tbl[vales_salida_productos] AS TVSP", 'TVSP.id_vale_salida=TVS.id_vale_salida', 'LEFT')
			->join("$tbl[productos] AS TP", 'TP.id_producto=TVSP.id_producto', 'LEFT')
			->join("$tbl[vales_almacen] AS TVA", 'TVA.id_vale_almacen=TVS.id_vale_almacen', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->join("$tbl[vales_estatus] AS CVE", 'CVE.id_vale_estatus=TVS.id_vale_estatus', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->where('TVS.activo', 1)
			->where('TVSP.activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}
}

/* End of file Ventas_productos_model.php */
/* Location: ./application/modules/technojet/models/Ventas_productos_model.php */