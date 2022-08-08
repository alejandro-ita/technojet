<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos_model extends SB_Model {

	public function get_productos(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) 			OR $this->db->where_not_in('TP.id_producto', $where['notIN']);
		!isset($where['id_producto']) 		OR $this->db->where('TP.id_producto', $where['id_producto']);
		!isset($where['id_categoria']) 		OR $this->db->where("TP.id_categoria IN($where[id_categoria])");
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

	public function get_productos_info(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		#!isset($where['id_uso']) OR $this->db->where('TVE.id_uso', $where['id_uso']);
		!isset($where['id_categoria']) OR $this->db->where('TP.id_categoria', $where['id_categoria']);
		#!isset($where['id_vale_estatus']) OR $this->db->where('TVE.id_vale_estatus', $where['id_vale_estatus']);

		$request = $this->db->select("
				 TP.*
				,CUM.unidad_medida
				,CM.moneda
				,IFNULL(TEP.entradas, 0) AS entradas
				,IFNULL(TSP.salidas, 0) AS salidas
				,((IFNULL(TEP.entradas, 0) - IFNULL(TSP.salidas, 0)) + TP.piezas_iniciales) AS total_piezas
				,(((IFNULL(TEP.entradas, 0) - IFNULL(TSP.salidas, 0)) + TP.piezas_iniciales) * TP.precio_inventario) AS costo
				,CTP.tipo_producto
			", FALSE)
			->from("$tbl[productos] AS TP")
			->join("(
				SELECT 
					 TVEP.id_producto
					,TVE.id_categoria
					,SUM(TVEP.cantidad) AS entradas
				FROM $tbl[vales_entrada] AS TVE
				LEFT JOIN $tbl[vales_entrada_productos] AS TVEP
					ON TVE.id_vale_entrada=TVEP.id_vale_entrada
				WHERE TVE.id_uso='$where[id_uso]'
					AND TVE.id_vale_estatus='$where[id_vale_estatus_entrada]'
					AND TVE.id_categoria='$where[id_categoria]'
					AND TVE.activo=1
					AND TVEP.activo=1
				GROUP BY TVEP.id_producto, TVE.id_categoria
			) AS TEP", 'TEP.id_producto=TP.id_producto AND TEP.id_categoria=TP.id_categoria', 'LEFT')
			->join("(
				SELECT 
					 TVSP.id_producto
					,TVS.id_categoria
					,SUM(TVSP.cantidad) AS salidas
				FROM $tbl[vales_salida] AS TVS
				LEFT JOIN $tbl[vales_salida_productos] AS TVSP
					ON TVS.id_vale_salida=TVSP.id_vale_salida
				WHERE TVS.id_uso='$where[id_uso]'
					AND TVS.id_vale_estatus='$where[id_vale_estatus_salida]'
					AND TVS.id_categoria='$where[id_categoria]'
					AND TVS.activo=1
					AND TVSP.activo=1
				GROUP BY TVSP.id_producto, TVS.id_categoria
			) AS TSP", 'TSP.id_producto=TP.id_producto AND TSP.id_categoria=TP.id_categoria', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->join("$tbl[monedas] AS CM", 'CM.id_moneda=TP.id_moneda', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TP.id_tipo_producto', 'LEFT')
			->group_by('TP.id_producto')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_productos_activos_info(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 TVA.cliente
				,TVA.id_requisicion
				,TVA.concepto
				,TVA.concepto AS concepto_entrada
				,TVA.concepto AS concepto_salida
				,TVA.observaciones
				,TVA.recibio
				,TVA.autorizo
				,TVA.entrego
				,TVA.vo_bo
				,CUM.unidad_medida
				,CM.moneda
				,SUM(IF(TVA.tipo='ENTRADA', TVAP.cantidad, 0)) AS entradas
				,SUM(IF(TVA.tipo='SALIDAS', TVAP.cantidad, 0)) AS salidas
				,SUM(IF(TVA.tipo='SALIDAS', TVAP.cantidad, 0)) AS total_piezas
				,CTP.tipo_producto
				,TVAP.no_parte
				,TVAP.descripcion
				,((SUM(IF(TVA.tipo='ENTRADA', TVAP.cantidad, 0)) - SUM(IF(TVA.tipo='SALIDAS', TVAP.cantidad, 0))) * TVAP.costo) AS costo
			", FALSE)
			->from("$tbl[vales_activos] AS TVA")
			->join("$tbl[vales_activos_productos] AS TVAP", 'TVAP.id_vale_activo=TVA.id_vale_activo', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TVAP.id_unidad_medida', 'LEFT')
			->join("$tbl[monedas] AS CM", 'CM.id_moneda=TVAP.id_moneda', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TVAP.id_tipo_producto', 'LEFT')
			->group_by('TVAP.no_parte')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_unidad_medida_tipo_producto(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_categoria']) OR $this->db->where('TP.id_categoria', $where['id_categoria']);
		!isset($where['id_tipo_producto']) OR $this->db->where('TP.id_tipo_producto', $where['id_tipo_producto']);
		$request = $this->db->distinct()
			->select("
				 CUM.id_unidad_medida
				,CUM.unidad_medida
				,CONCAT(CUM.unidad_medida, '(', CUM.clave, ')') AS custom_unidad_medida
				,IF(CUM.id_unidad_medida='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->from("$tbl[productos] AS TP")
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('CUM.activo', 1)
			->order_by('CUM.unidad_medida')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_productos_por_tipo(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_categoria']) OR $this->db->where('id_categoria', $where['id_categoria']);
		!isset($where['id_tipo_producto']) OR $this->db->where('id_tipo_producto', $where['id_tipo_producto']);
		!isset($where['id_unidad_medida']) OR $this->db->where('id_unidad_medida', $where['id_unidad_medida']);
		$request = $this->db->distinct()
			->select("
				 id_producto
				,no_parte
				,descripcion
				,IF(id_producto='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->order_by('no_parte')
			->get($tbl['productos']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}
}

/* End of file Productos_model.php */
/* Location: ./application/modules/technojet/models/Productos_model.php */