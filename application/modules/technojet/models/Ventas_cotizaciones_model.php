<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas_cotizaciones_model extends SB_Model {

	public function get_ventas_cotizacion_min(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TAR.id_ventas_cotizacion', $where['notIN']);
		!isset($where['id_ventas_cotizacion']) OR $this->db->where('TAR.id_ventas_cotizacion', $where['id_ventas_cotizacion']);
		!isset($where['id_categoria']) OR $this->db->where('TAR.id_categoria', $where['id_categoria']);
		!isset($where['c_cotizacion']) OR $this->db->where('TAR.c_cotizacion', $where['c_cotizacion']);
		!isset($where['grupo']) OR $this->db->where('CC.grupo', $where['grupo']);
		$request = $this->db->select("
				 TAR.*,
				,CC.categoria
			", FALSE)
			->from("$tbl[ventas_cotizaciones] AS TAR")
			->join("$tbl[categorias] AS CC", 'CC.id_categoria=TAR.id_categoria', 'LEFT')
			->where('TAR.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_ventas_cotizacion(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['ventas_cotizaciones'], $data);
		} else $this->db->insert_batch($tbl['ventas_cotizaciones'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_ventas_cotizacion(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['ventas_cotizaciones'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_ventas_cotizacion_select(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_ventas_cotizacion']) OR $this->db->where('id_ventas_cotizacion', $where['id_ventas_cotizacion']);
		!isset($where['id_categoria']) OR $this->db->where('TAR.id_categoria', $where['id_categoria']);

		$request = $this->db->select("
				*,
				IF(TAR.id_ventas_cotizacion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/", FALSE)
			->from("$tbl[ventas_cotizaciones] AS TAR")
			->where('TAR.activo', 1)
			->join("$tbl[categorias] AS CC", 'CC.id_categoria=TAR.id_categoria', 'LEFT')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	####################################################################################

	public function get_ultimo_requisicion(array $where=[], $all=FALSE) {
		$tbl = $this->tbl;

		$request = $this->db->select("
				 MAX(id_requisicion) AS ulitmo_id
				,(IFNULL(MAX(id_requisicion), 0)+1) AS proximo_id
			", FALSE)
			->from($tbl['requisiciones'])
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_tipos_requisiciones(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;


		!isset($where['id_tipo_requisicion']) OR $this->db->where('id_almacen_requisicion', $where['id_tipo_requisicion']);
		$request = $this->db->select("
				 id_almacen_requisicion AS id_tipo_requisicion
				,requisicion AS tipo_requisicion
				,IF(id_almacen_requisicion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->where('id_categoria', 11)
			->get($tbl['almacen_requisiciones']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_departamentos_solicitantes(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;


		!isset($where['id_departamento_solicitante']) OR $this->db->where('id_almacen_requisicion', $where['id_departamento_solicitante']);
		$request = $this->db->select("
				 id_almacen_requisicion AS id_departamento_solicitante
				,requisicion AS departamento_solicitante
				,IF(id_almacen_requisicion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->where('id_categoria', 9)
			->get($tbl['almacen_requisiciones']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_almacenes_solicitantes(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_almacen_solicitante']) OR $this->db->where('id_almacen_requisicion', $where['id_almacen_solicitante']);
		$request = $this->db->select("
				 id_almacen_requisicion AS id_almacen_solicitante
				,requisicion AS almacen_solicitante
				,IF(id_almacen_requisicion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->where('id_categoria', 7)
			->get($tbl['almacen_requisiciones']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_departamentos_encargados_surtir(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_departamento_encargado_surtir']) OR $this->db->where('id_almacen_requisicion', $where['id_departamento_encargado_surtir']);
		$request = $this->db->select("
				 id_almacen_requisicion AS id_departamento_encargado_surtir
				,requisicion AS departamento_encargado_surtir
				,IF(id_almacen_requisicion='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->where('id_categoria', 8)
			->get($tbl['almacen_requisiciones']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_requisiciones(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TR.id_requisicion', $where['notIN']);
		!isset($where['id_requisicion']) OR $this->db->where('TR.id_requisicion', $where['id_requisicion']);
		$request = $this->db->select("
				 TR.id_requisicion
				,CONCAT('RQ-', TR.id_requisicion) AS folio
				,'' AS vale_entrada
				,TR.fecha_solicitud
				,TR.pedido_interno
				,TR.cliente
				,'' AS fecha_entrega
				,TR.persona_solicitante
				,TR.encargado_almacen
				,'' AS tipo_entrega
				,TR.observaciones
				,TR.nombre_almacen
				,TR.nombre_compras
				,TR.nombre_autorizacion
				,VWAS.id_almacen_solicitante
				,VWAS.almacen_solicitante
				,VWDES.id_departamento_encargado_surtir
				,VWDES.departamento_encargado_surtir
				,VWDS.id_departamento_solicitante
				,VWDS.departamento_solicitante
				,VWTR.id_tipo_requisicion
				,VWTR.tipo_requisicion
				,ER.estatus_requisicion
				,ER.folios_ve
				,ER.fechas_ve
				,ER.tipo_entrega_ve
			", FALSE)
			->from("$tbl[requisiciones] AS TR")
			/*OBTENEMOS EL ESTATUS DE LA REQUISICION*/
			->join("(
				SELECT 
					 TPE.id_requisicion
					,(CASE
						WHEN SUM(TPE.total_entregado)=0 THEN 'No entregada'
						WHEN SUM(TPE.total_faltante)>0 THEN 'Entrega parcial'
						ELSE 'Entregada' END
					) AS estatus_requisicion
					,GROUP_CONCAT(DISTINCT ' ', folio) AS folios_ve
					,GROUP_CONCAT(DISTINCT ' ', fecha) AS fechas_ve
					,'' AS tipo_entrega_ve
				FROM (
						SELECT TRP.id_requisicion
							,TRP.id_producto
							,CONCAT('VE-', TVE.id_vale_entrada) AS folio
							,DATE(TVE.fecha_hora) AS fecha
							,TRP.cantidad AS total_pedido
							,SUM(IFNULL(TVEP.cantidad, 0)) AS total_entregado
							,IF(TRP.cantidad>SUM(IFNULL(TVEP.cantidad, 0)), TRP.cantidad-SUM(IFNULL(TVEP.cantidad, 0)), 0) AS total_faltante
							,IF(TRP.cantidad<SUM(IFNULL(TVEP.cantidad, 0)), SUM(IFNULL(TVEP.cantidad, 0))-TRP.cantidad, 0) AS total_exedente
						FROM (
							SELECT id_requisicion, id_producto, SUM(cantidad) AS cantidad
							FROM $tbl[requisiciones_productos]
							WHERE activo=1
							GROUP BY id_requisicion, id_producto
						) AS TRP
						LEFT JOIN $tbl[vales_entrada] AS TVE
							ON TVE.id_requisicion=TRP.id_requisicion
								AND TVE.activo=1
						LEFT JOIN (
							SELECT id_vale_entrada, id_producto, SUM(cantidad) AS cantidad
							FROM $tbl[vales_entrada_productos]
							WHERE activo=1
							GROUP BY id_vale_entrada, id_producto
						) AS TVEP
							ON TVEP.id_vale_entrada=TVE.id_vale_entrada
								AND TVEP.id_producto=TRP.id_producto
						GROUP BY TRP.id_requisicion, TRP.id_producto
				) AS TPE
				GROUP BY TPE.id_requisicion
			) AS ER", 'ER.id_requisicion=TR.id_requisicion', 'LEFT')
			->join("$tbl[vw_almacenes_solicitantes] AS VWAS", 'VWAS.id_almacen_solicitante=TR.id_almacen_solicitante', 'LEFT')
			->join("$tbl[vw_departamentos_encargados_surtir] AS VWDES", 'VWDES.id_departamento_encargado_surtir=TR.id_departamento_encargado_surtir', 'LEFT')
			->join("$tbl[vw_departamentos_solicitantes] AS VWDS", 'VWDS.id_departamento_solicitante=TR.id_departamento_solicitante', 'LEFT')
			->join("$tbl[vw_tipos_requisiciones] AS VWTR", 'VWTR.id_tipo_requisicion=TR.id_tipo_requisicion', 'LEFT')
			->where('TR.activo', 1)
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_requisiciones(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['requisiciones'], $data);
		} else $this->db->insert_batch($tbl['requisiciones'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_requisicion(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['requisiciones'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_requisicion_productos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) OR $this->db->where_not_in('TRP.id_requisicion', $where['notIN']);
		!isset($where['id_requisicion']) OR $this->db->where('TRP.id_requisicion', $where['id_requisicion']);
		$request = $this->db->select("
				 TRP.id_requisicion_producto
				,TRP.id_requisicion
				,TRP.id_tipo_producto
				,CTP.tipo_producto
				,CUM.unidad_medida
				,TP.id_producto
				,TP.no_parte
				,TP.descripcion
				,TRP.cantidad
			", FALSE)
			->from("$tbl[requisiciones_productos] AS TRP")
			->join("$tbl[productos] AS TP", 'TP.id_producto=TRP.id_producto', 'LEFT')
			->join("$tbl[tipos_productos] AS CTP", 'CTP.id_tipo_producto=TRP.id_tipo_producto', 'LEFT')
			->join("$tbl[unidades_medida] AS CUM", 'CUM.id_unidad_medida=TP.id_unidad_medida', 'LEFT')
			->where('TRP.activo', 1)
			->get();

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_requisiciones_productos(array $data, $batch=TRUE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['requisiciones_productos'], $data);
		} else $this->db->insert_batch($tbl['requisiciones_productos'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_requisicion_productos(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		if (isset($where['notIn'])) {
			$this->db->where_not_in('id_requisicion_producto', $where['notIn']);
			unset($where['notIn']);
		}

		$data['id_usuario_update'] = $this->session->userdata('id_usuario');
		$data['timestamp_update'] = timestamp();
		$this->db->update($tbl['requisiciones_productos'], $data, $where);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_requisiciones_select2(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['notIN']) OR $this->db->where_not_in('id_requisicion', $where['notIN']);
		!isset($where['id_requisicion']) OR $this->db->where('id_requisicion', $where['id_requisicion']);
		$request = $this->db->select("
				 id_requisicion
				,CONCAT('RQ-', id_requisicion) AS folio_requisicion
				,IF(id_requisicion='$selected', 1, 0) AS selected
			", FALSE)
			->where('activo', 1)
			->get($tbl['requisiciones']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_estatus_requisicion(array $where, $all=FALSE) {
		$tbl = $this->tbl;

		$filter = (isset($where['id_requisicion']) ? " AND TRP.id_requisicion IN($where[id_requisicion])" : '');
		$request = $this->db->query("
			SELECT 
				 TPE.id_requisicion
				,(CASE
					WHEN SUM(TPE.total_entregado)=0 THEN 'No entregada'
					WHEN SUM(TPE.total_faltante)>0 THEN 'Entrega parcial'
					ELSE 'Entregada' END
				) AS estatus_requisicion
				,GROUP_CONCAT(DISTINCT ' ', folio) AS folios_ve
				,GROUP_CONCAT(DISTINCT ' ', fecha) AS fechas_ve
				,'' AS tipo_entrega_ve
			FROM (
					SELECT TRP.id_requisicion
						,TRP.id_producto
						,CONCAT('VE-', TVE.id_vale_entrada) AS folio
						,DATE(TVE.fecha_hora) AS fecha
						,TRP.cantidad AS total_pedido
						,SUM(IFNULL(TVEP.cantidad, 0)) AS total_entregado
						,IF(TRP.cantidad>SUM(IFNULL(TVEP.cantidad, 0)), TRP.cantidad-SUM(IFNULL(TVEP.cantidad, 0)), 0) AS total_faltante
						,IF(TRP.cantidad<SUM(IFNULL(TVEP.cantidad, 0)), SUM(IFNULL(TVEP.cantidad, 0))-TRP.cantidad, 0) AS total_exedente
					FROM $tbl[requisiciones_productos] AS TRP
					LEFT JOIN $tbl[vales_entrada] AS TVE
						ON TVE.id_requisicion=TRP.id_requisicion
							AND TVE.activo=1
					LEFT JOIN $tbl[vales_entrada_productos] AS TVEP
						ON TVEP.id_vale_entrada=TVE.id_vale_entrada
							AND TVEP.id_producto=TRP.id_producto
							AND TVEP.activo=1
					WHERE TRP.activo=1
						$filter
					GROUP BY TRP.id_requisicion, TRP.id_producto
			) AS TPE
			GROUP BY TPE.id_requisicion
		");

		return $all ? $request->result_array() : $request->row_array();
	}
}

/* End of file Almacen_requisiciones_model.php */
/* Location: ./application/modules/technojet/models/Almacen_requisiciones_model.php */