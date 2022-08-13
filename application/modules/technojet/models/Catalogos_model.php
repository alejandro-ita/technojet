<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalogos_model extends SB_Model {

	public function get_perfiles(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['notIN']) OR $this->db->where_not_in('id_perfil', $where['notIN']);
		$request = $this->db->select("
				 id_perfil
				,perfil
				,IF(id_perfil='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['perfiles']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_categorias(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_categoria']) OR $this->db->where('id_categoria', $where['id_categoria']);
		!isset($where['grupo']) OR $this->db->where('grupo', $where['grupo']);
		!isset($where['notIN']) OR $this->db->where_not_in('id_categoria', $where['notIN']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['categorias']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_unidades_medida_min(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_unidad_medida']) OR $this->db->where('id_unidad_medida', $where['id_unidad_medida']);
		$request = $this->db->select("*
				,CONCAT(unidad_medida, '(', clave, ')') AS custom_unidad_medida
				,IF(id_unidad_medida='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['unidades_medida']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_monedas_min(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_moneda']) OR $this->db->where('id_moneda', $where['id_moneda']);
		$request = $this->db->select("*
				,CONCAT(moneda, '(', clave,')') AS custom_moneda
				,IF(id_moneda='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['monedas']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_tipo_consumibles(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_tipo_consumible']) OR $this->db->where('id_tipo_consumible', $where['id_tipo_consumible']);
		$request = $this->db->select("
				 id_tipo_consumible
				,tipo_consumible
				,IF(id_tipo_consumible='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['tipo_consumibles']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_tipo_consumible(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['tipo_consumibles'], $data);
		} else $this->db->insert_batch($tbl['tipo_consumibles'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function get_usos(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_uso']) OR $this->db->where('id_uso', $where['id_uso']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['usos']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_ve_tipos_entrada(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_ve_tipo_entrada']) OR $this->db->where('id_ve_tipo_entrada', $where['id_ve_tipo_entrada']);
		$request = $this->db->select("
				 id_ve_tipo_entrada
				,tipo_entrada
				,IF(id_ve_tipo_entrada='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['ve_tipos_entrada']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_tipos_productos_min(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_tipo_producto']) OR $this->db->where('id_tipo_producto', $where['id_tipo_producto']);
		$request = $this->db->select("
				 id_tipo_producto
				,tipo_producto
				,IF(id_tipo_producto='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['tipos_productos']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_tareas_estatus(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_tarea_estatus']) OR $this->db->where('id_tarea_estatus', $where['id_tarea_estatus']);
		$request = $this->db->select("
				 id_tarea_estatus AS id_estatus
				,estatus
				,IF(id_tarea_estatus='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['tarea_estatus']);

		return $all ? $request->result_array() : $request->row_array();
	}

	public function get_tareas_prioridad(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;
		$selected = isset($where['selected'])?$where['selected']:0;

		!isset($where['id_tarea_prioridad']) OR $this->db->where('id_tarea_prioridad', $where['id_tarea_prioridad']);
		$request = $this->db->select("
				 id_tarea_prioridad AS id_prioridad
				,prioridad
				,IF(id_tarea_prioridad='$selected', 1, 0) AS selected /*PARA EL SELECT2*/
			", FALSE)
			->where('activo', 1)
			->get($tbl['tarea_prioridad']);

		return $all ? $request->result_array() : $request->row_array();
	}
}

/* End of file Catalogos_model.php */
/* Location: ./application/modules/technojet/models/Catalogos_model.php */