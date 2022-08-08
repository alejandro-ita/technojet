<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro_actividades_model extends SB_Model {

	public function get_registro_actividades(array $where, $all=TRUE) {
		$tbl 	= $this->tbl;

		!isset($where['notIn']) OR $this->db->where_not_in('SU.id_usuario', $where['notIn']);
		!isset($where['id_usuario']) OR $this->db->where('SU.id_usuario', $where['id_usuario']);
		!isset($where['email']) OR $this->db->where('SU.email', $where['email']);
		!isset($where['token']) OR $this->db->where('SU.token', $where['token']);

		if (isset($where['startDate'])) {
			$this->db->where("DATE_FORMAT(TBA.timestamp, '%Y-%m-%d') BETWEEN '$where[startDate]' AND '$where[endDate]'");
		}
		$request = $this->db->select("
				 TBA.id_bitacora_actividad
				,TBA.id_registro
				,TBA.tabla
				,TBA.actividad
				,TBA.data_change
				,TBA.browser
				,TBA.ip
				,TBA.id_usuario
				,TBA.timestamp
				,TBA.activo
				,concat_ws(' ', ifnull( SU.nombre, '' ), ifnull( SU.paterno, '' ), ifnull( SU.materno, '' )) AS nombre_completo,
				,SU.email AS email
				,DATE_FORMAT(TBA.timestamp, '".get_var('MySQLdateFormat')."') AS date_custom
				,DATE_FORMAT(TBA.timestamp, '".get_var('MySQLtimeFormat')."') AS time_custom
			", FALSE)
			->from("$tbl[bitacora_actividades] AS TBA")
			->join("$tbl[usuarios] AS SU", 'SU.id_usuario=TBA.id_usuario', 'LEFT')
			->where('TBA.activo', 1)
			->order_by('TBA.timestamp', 'DESC')
			->get();

		// debug($this->db->last_query());
		return $all ? $request->result_array() : $request->row_array();
	}

}

/* End of file Registro_actividades_model.php */
/* Location: ./application/modules/technojet/models/Registro_actividades_model.php */