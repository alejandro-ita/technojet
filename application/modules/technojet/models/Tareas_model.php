<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tareas_model extends SB_Model {

	public function get_tareas(array $where, $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['notIN']) 	OR $this->db->where_not_in('id_tarea', $where['notIN']);
		!isset($where['id_tarea']) 	OR $this->db->where('id_tarea', $where['id_tarea']);
		$request = $this->db->select("*", FALSE)
			->where('activo', 1)
			->get($tbl['tareas']);
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_tarea(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['tareas'], $data);
		} else $this->db->insert_batch($tbl['tareas'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_tarea(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['tareas'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_tareas_info(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_tarea']) 		OR $this->db->where('TT.id_tarea', $where['id_tarea']);
		!isset($where['mc']) 			OR $this->db->where('TT.mc', $where['mc']);
		!isset($where['id_estatus']) 	OR $this->db->where_in('TT.id_estatus', $where['id_estatus']);
		!isset($where['id_prioridad']) 	OR $this->db->where_in('TT.id_prioridad', $where['id_prioridad']);
		if (isset($where['fecha_inicio']) && isset($where['fecha_fin'])) {
			$this->db->where("TT.fecha_inicio<='$where[fecha_fin]' AND TT.fecha_fin>= '$where[fecha_inicio]'", NULL, FALSE);
		}

		if (isset($where['id_usuario'])) {
			$this->db->group_start()
				->where('TT.id_usuario_insert', $where['id_usuario']) #AUTOR
				->or_where("FIND_IN_SET($where[id_usuario], TT.id_responsables)") #RESPONSABLES DE LA TAREA
				->or_where("FIND_IN_SET($where[id_usuario], TTP.id_participantes)")
    			->group_end();
		}

		$request = $this->db->select("
				 TT.id_tarea
				,TT.titulo
				,TT.descripcion
				,TT.fecha_inicio
				,DATE_FORMAT(TT.fecha_inicio, '".get_var('MySQLtimeStamp')."') AS custom_fecha_inicio
				,IF(TIME_TO_SEC(TT.hora_inicio)>0, TT.hora_inicio, '') AS hora_inicio
				,TT.fecha_fin
				,DATE_FORMAT(TT.fecha_fin, '".get_var('MySQLtimeStamp')."') AS custom_fecha_fin
				,IF(TIME_TO_SEC(TT.hora_fin)>0, TT.hora_fin, '') AS hora_fin
				,TT.id_responsables
				,TTP.id_participantes
				,TT.id_estatus
				,TT.id_prioridad
				,CONCAT_WS(' ', IFNULL(SU.nombre, ''), IFNULL(SU.paterno, ''), IFNULL(SU.materno, '')) AS nombre_autor
				,DATE_FORMAT(TT.timestamp_insert, '".get_var('MySQLtimeStamp')."') AS fecha_creacion
				,TT.id_usuario_insert
			", FALSE)
			->from("$tbl[tareas] AS TT")
			->join("$tbl[usuarios] AS SU", 'SU.id_usuario=TT.id_usuario_insert', 'LEFT')
			->join("(
				SELECT id_tarea, GROUP_CONCAT(id_participante) AS id_participantes, estatus
				FROM $tbl[tareas_participantes]
				WHERE activo=1
				GROUP BY id_tarea
			) AS TTP", 'TTP.id_tarea=TT.id_tarea', 'LEFT')
			->where('TT.activo', 1)
			->group_by('TT.id_tarea')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}

	public function insert_participante(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['tareas_participantes'], $data);
		} else $this->db->insert_batch($tbl['tareas_participantes'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_participante(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['tareas_participantes'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function insert_comentario(array $data, $batch=FALSE) {
		$tbl = $this->tbl;

		if (!$batch) {
			$data['id_usuario_insert'] 	= $this->session->userdata('id_usuario');
			$data['timestamp_insert'] 	= timestamp();
			$this->db->insert($tbl['tareas_comentarios'], $data);
		} else $this->db->insert_batch($tbl['tareas_comentarios'], $data);

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($batch ? TRUE : $this->db->insert_id());
	}

	public function update_comentario(array $data, array $where, $affectedRows=TRUE) {
		$tbl = $this->tbl;

		$data['id_usuario_update'] 	= $this->session->userdata('id_usuario');
		$data['timestamp_update'] 	= timestamp();
		$this->db->update($tbl['tareas_comentarios'], $data, $where);
		// debug($this->db->last_query());

		$error = $this->db->error();
		if ($error['message']) {
			log_message('error', $error['message']);
			return FALSE;
		}

		return ($affectedRows ? $this->db->affected_rows() : TRUE);
	}

	public function get_comentarios_info(array $where=[], $all=TRUE) {
		$tbl = $this->tbl;

		!isset($where['id_tarea']) OR $this->db->where('TTC.id_tarea', $where['id_tarea']);
		$request = $this->db->select("
				 TTC.id_tarea_comentario
				,TTC.id_tarea
				,TTC.comentario
				,TTC.id_usuario_insert
				,CONCAT_WS(' ', IFNULL(SU.nombre, ''), IFNULL(SU.paterno, ''), IFNULL(SU.materno, '')) AS usuario
				,DATE_FORMAT(TTC.timestamp_insert, '".get_var('MySQLtimeStamp')."') AS custom_timestamp_insert
			", FALSE)
			->from("$tbl[tareas_comentarios] AS TTC")
			->join("$tbl[usuarios] AS SU", 'SU.id_usuario=TTC.id_usuario_insert', 'LEFT')
			->where('TTC.activo', 1)
			->order_by('TTC.timestamp_insert', 'ASC')
			->get();
		// debug($this->db->last_query());

		return $all ? $request->result_array() : $request->row_array();
	}
}

/* End of file Tareas_model.php */
/* Location: ./application/modules/technojet/models/Tareas_model.php */