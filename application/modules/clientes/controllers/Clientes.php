<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends SB_Controller{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('clientes/cliente', 'db_cliente');
    }

    public function index()
    {
        $this->load_view('clientes/index');
    }

    public function cliente()
    {
        $includes = get_includes_vendor(['dataTables', 'jQValidate']);
        $pathJS = get_var('path_js');
        $includes['modulo']['js'][] = ['name' => 'index', 'dirname' => "$pathJS/clientes/", 'fulldir' => TRUE];

        $dataView['tpl-tools'] = $this->parser_view('clientes/tpl/tpl-tools');

        $this->load_view('clientes/index', $dataView, $includes);
    }
   

    public function get_clientes()
    {
        $resultados = $this->db_cliente->getClientes();

        $tplAcciones = $this->parser_view('clientes/tpl/tpl-acciones');
        foreach ($resultados as &$di) {
            $di['acciones'] = $tplAcciones;
        }

        echo json_encode($resultados, JSON_NUMERIC_CHECK);
    }


    public function get_modal_new_cliente()
    {
        $this->parser_view('clientes/tpl/modal-new-cliente', [], FALSE);
    }

    public function process_save_cliente()
    {
        try {
            $sqlWhere = ['nombre' => $this->input->post('nombre') ?? null];
            $exist = $this->db_cliente->guardar_clientes($sqlWhere, FALSE);

            if (!$exist) {
                $sqlData = $this->input->post([ 'nombre', 'razon_social', 'rfc']);
                $insert = $this->db_cliente->insert_cliente($sqlData);
                $insert or set_exception();
                $actividad         = "ha creado un nuevo cliente en almacén: " . $_POST['nombre'];
                $data_change     = ['insert' => ['newData' => $sqlData]];
                registro_bitacora_actividades($insert, 'tbl_almacen_requisiciones', $actividad, $data_change);
            } else set_alert(lang('cliente_exist'));

            $response = [
                'success'    => TRUE,
                'msg'         => lang('cliente_save_success'),
                'icon'         => 'success'
            ];
        } catch (SB_Exception $e) {
            $response = get_exception($e);
        }

        echo json_encode($response);
    }


    public function get_modal_update_cliente()
    {
        $sqlWhere = $this->input->post(['id_cliente']);
        $dataView = $this->db_cliente->update_clientes($sqlWhere, FALSE);

        $this->parser_view('clientes/tpl/modal-update-cliente', $dataView, FALSE);
    }

    public function process_update_cliente()
    {
        try {

            $sqlWhere = ['id_cliente' => $this->input->post('id_cliente')];

            $exist = $this->db_cliente->update_clientes($sqlWhere, FALSE);
            if ($exist) {
                $sqlWhere = $this->input->post(['id_cliente']);
                $sqlData = $this->input->post(['nombre', 'razon_social', 'rfc']);
                $update = $this->db_cliente->update_cliente_process($sqlData, $sqlWhere);
                $update or set_exception();
                $actividad         = "ha editado un cliente en almacén: " . $_POST['id_cliente'];
                $data_change     = ['update' => ['newData' => $sqlData]];
                registro_bitacora_actividades($sqlWhere['id_cliente'], 'clientes', $actividad, $data_change);
				
            } else set_alert(lang('cliente_not_exist'));

            $response = [
                'success'    => TRUE,
                'msg'         => lang('cliente_update_success'),
                'icon'         => 'success'
            ];
        } catch (SB_Exception $e) {
            $response = get_exception($e);
        }

        echo json_encode($response);
    }

    public function process_remove_cliente()
    {
        try {
            $sqlWhere = $this->input->post(['id_cliente']);
            $remove = $this->db_cliente->update_cliente_process(['activo' => 0], $sqlWhere);
            $remove or set_exception();

            $actividad         = "ha eliminado un cliente en almacén: " . $_POST['id_cliente'];
            $data_change     = ['delete' => ['oldData' => $_POST]];
            registro_bitacora_actividades($sqlWhere['id_cliente'], 'clientes', $actividad, $data_change);

            $response = [
                'success'    => TRUE,
                'msg'         => lang('cliente_rm_success'),
                'icon'         => 'success'
            ];
        } catch (SB_Exception $e) {
            $response = get_exception($e);
        }

        echo json_encode($response);
    }

}