<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Cliente extends SB_Model {

    public function getClientes(){
        $table = $this->tbl['clientes'];
        $request = $this->db->query("SELECT * FROM ${table} WHERE activo = 1");

        return $request->result_array();
    }

    public function update_clientes(array $where = [], $all = TRUE){
        $table = $this->tbl['clientes'];

        !isset($where['notIN']) or $this->db->where_not_in('id_cliente', $where['notIN']);
        !isset($where['id_cliente']) or $this->db->where('id_cliente', $where['id_cliente']);
        !isset($where['cliente']) or $this->db->where('cliente', $where['cliente']);

        $id_cliente = $where['id_cliente'];

        $request = $this->db->query("SELECT * FROM ${table} WHERE id_cliente = $id_cliente");

        return $all ? $request->result_array() : $request->row_array();
    }

    public function guardar_clientes(array $where = [], $all = TRUE)
    {
        $table = $this->tbl['clientes'];

        !isset($where['notIN']) or $this->db->where_not_in('id_cliente', $where['notIN']);
        !isset($where['id_cliente']) or $this->db->where('id_cliente', $where['id_cliente']);
        !isset($where['cliente']) or $this->db->where('cliente', $where['cliente']);

        $nombre = $where['nombre'];

        $request = $this->db->query("SELECT * FROM ${table} WHERE nombre = '${nombre}'");

        

        return $all ? $request->result_array() : $request->row_array();
    }

    public function insert_cliente(array $data, $batch = FALSE)
    {
        $tbl = $this->tbl['clientes'];

        if (!$batch) {
            
            $data['id_usuario_insert']     = $this->session->userdata('id_usuario');
            $data['timestamp_update']     = timestamp();

            $data['id_usuario_update']     = $this->session->userdata('id_usuario');
            $data['timestamp_insert']     = timestamp();
            $data['activo'] = 1;
            $this->db->insert($tbl, $data);
        } else $this->db->insert_batch($tbl, $data);

        $error = $this->db->error();
        if ($error['message']) {
            log_message('error', $error['message']);
            return FALSE;
        }

        return ($batch ? TRUE : $this->db->insert_id());
    }

    public function update_cliente_process(array $data, array $where, $affectedRows = TRUE)
    {
        $tbl = $this-> tbl['clientes'];
        $data['id_usuario_insert']     = $this->session->userdata('id_cliente');
        $data['timestamp_update']     = timestamp();

        $data['id_usuario_update']     = $this->session->userdata('id_cliente');
        $data['timestamp_update']     = timestamp();
        $this->db->update($tbl, $data, $where);
        // debug($this->db->last_query());

        $error = $this->db->error();
        if ($error['message']) {
            log_message('error', $error['message']);
            return FALSE;
        }

        return ($affectedRows ? $this->db->affected_rows() : TRUE);
    }

}
