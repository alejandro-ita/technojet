<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends SB_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('technojet/Almacenes_vales_model', 'db_av');
		$this->load->model('technojet/Ventas_productos_model', 'db_vp');
		$this->load->model('technojet/Productos_model', 'db_productos');
		$this->load->model('ventas/Cotizaciones_model', 'db_cotizaciones');
		$this->load->model('technojet/Almacen_requisiciones_model', 'db_ar');
		$this->load->model('technojet/Ventas_cotizaciones_model', 'db_vc');
		$this->load->model('technojet/Vendedores_model', 'db_vendedor');
		$this->load->model('technojet/Catalogos_model', 'db_catalog');
		$this->load->model('technojet/Clientes_model', 'db_cliente');
		$this->load->model('ventas/Pedidos_internos', 'db_pi');
	}

	public function cotizaciones() {
		$dataView['tpl-tbl-cotizaciones']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-cotizaciones');
		$dataView['tpl-tbl-cotizaciones-consecutivo']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-cotizaciones-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'cotizaciones', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    	//$includes['modulo']['js'][] = ['name'=>'reporte-mensual', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
    	//$includes['modulo']['js'][] = ['name'=>'reporte-detallado', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/cotizaciones/cotizaciones_view',$dataView, $includes);
	}
	
	public function get_modal_add_cotizacion(){
		//cargar contenido de vistas / CATALOGOS
		//Tiempo de entrega
		$sqlWhere['id_categoria'] = 23;
		$sqlWhere['grupo'] = 6;
		$dataView['tiempo-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		
		//Estatus vigencia
		$sqlWhere['id_categoria'] = 24;
		$sqlWhere['grupo'] = 6;
		$dataView['estatus-vigencia'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Estatus entrega
		$sqlWhere['id_categoria'] = 25;
		$sqlWhere['grupo'] = 6;
		$dataView['estatus-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Condiciones de pago
		$sqlWhere['id_categoria'] = 26;
		$sqlWhere['grupo'] = 6;
		$dataView['condiciones-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Vigencia
		$sqlWhere['id_categoria'] = 27;
		$sqlWhere['grupo'] = 6;
		$dataView['vigencia'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Precios
		$sqlWhere['id_categoria'] = 28;
		$sqlWhere['grupo'] = 6;
		$dataView['precios'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Lugar de entrega
		$sqlWhere['id_categoria'] = 29;
		$sqlWhere['grupo'] = 6;
		$dataView['lentrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Tipos de producto
		$sqlWhere['id_categoria'] = 30;
		$sqlWhere['grupo'] = 6;
		$dataView['tproducto'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

		//Vendedores
		$dataView['vendedores'] = $this->db_vendedor->get_vendedores_main();

		//Monedas
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();

		//Clientes
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//cargar JS's / INTERACCIÓN
		
		#OBTENEMOS EL CONSECUTIVO DEL COTIZACION
		$folio = $this->db_cotizaciones->get_ultima_cotizacion();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/cotizaciones/tpl/modal-nueva-cotizacion', $dataView, FALSE);
		//modal-add-producto-entrada
	}

	public function get_modal_add_product_option(){
		$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-opcional', FALSE, FALSE);
	}

	public function get_cotizaciones(){
		$response = $this->db_cotizaciones->get_cotizaciones_main();

		$tplAcciones = $this->parser_view('ventas/cotizaciones/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_producto_cotizacion() {
		$dataView['tipo-producto'] = $this->db_catalogos->get_tipos_productos_min();
		$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-cotizacion', $dataView, FALSE);
	}

	public function get_unidades_medida_productos() {
		$sqlWhere = $this->input->post(['id_tipo_producto']);
		$response = $this->db_productos->get_unidad_medida_tipo_producto($sqlWhere);

		echo json_encode($response);
	}

	public function get_productos() {
		$sqlWhere = $this->input->post('id_tipo_producto') ? $this->input->post(['id_tipo_producto']) : [];
		$sqlWhere['grupo']=3;
		$sqlWhere['id_sitio']=2;
		$productos = $this->db_productos->get_productos($sqlWhere);
		$productos = $productos ? $productos : [];

		/*$tplAcciones = $this->parser_view('database/ventas/productos/tpl/tpl-acciones');
		foreach ($productos as &$di) {
			$di['acciones'] = $tplAcciones;
		}*/

		echo json_encode($productos, JSON_NUMERIC_CHECK);
	}

	public function get_productos_por_tipo() {
		$sqlWhere = $this->input->post(['id_tipo_producto', 'id_unidad_medida']);
		$response = $this->db_productos->get_productos_por_tipo($sqlWhere);

		echo json_encode($response);
	}
	
	public function process_save_cotizacion() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS COTIZACIÓN
			$sqlData = $this->input->post([
				'id_cliente',
				'id_estatus_vigencia',
				'id_estatus_entrega',
				'fecha_elaboracion',
				'atencion',
				'departamento',
				'id_moneda',
				'id_precio',
				'id_condiciones_pago',
				'id_tiempo_entrega',
				'id_lugar_entrega',
				'id_vigencia',
				'id_tipo_producto',
				'fecha_recepcion',
				'id_vendedor',
				'creador_cotizacion'
			]);
			
			$sqlData['atencion'] 	= strtoupper($this->input->post('atencion'));
			$sqlData['departamento'] 		= strtoupper($this->input->post('departamento'));
			$sqlData['creador_cotizacion'] = strtoupper($this->input->post('creador_cotizacion'));
			$insert = $this->db_cotizaciones->insert_cotizacion($sqlData);
			$insert OR set_exception();

			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_cotizacion' 		=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento' 			=> $producto['descuento'],
					'total' 				=> $producto['total'],
					'incluye' 				=> $producto['incluye'],
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				$sqlDataBatch[] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_cotizaciones->insert_cotizacion_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $sqlData;
			$actividad 		= "ha creado una cotización";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_cotizaciones', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_modal_edit_cotizacion() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_cotizacion']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Tiempo de entrega
		$sqlWhere['id_categoria'] = 23;
		$sqlWhere['selected'] = $this->input->post('id_tiempo_entrega');
		$dataView['tiempo-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Vigencia
		$sqlWhere['id_categoria'] = 24;
		$sqlWhere['selected'] = $this->input->post('id_estatus_vigencia');
		$dataView['estatus-vigencia'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Entrega
		$sqlWhere['id_categoria'] = 25;
		$sqlWhere['selected'] = $this->input->post('id_estatus_entrega');
		$dataView['estatus-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Condiciones de pago
		$sqlWhere['id_categoria'] = 26;
		$sqlWhere['selected'] = $this->input->post('id_condiciones_pago');
		$dataView['condiciones-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Vigencia
		$sqlWhere['id_categoria'] = 27;
		$sqlWhere['selected'] = $this->input->post('id_vigencia');
		$dataView['vigencia'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Precios
		$sqlWhere['id_categoria'] = 28;
		$sqlWhere['selected'] = $this->input->post('id_precio');
		$dataView['precios'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Lugar de entrega
		$sqlWhere['id_categoria'] = 29;
		$sqlWhere['selected'] = $this->input->post('id_lugar_entrega');
		$dataView['lentrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Tipos de producto
		$sqlWhere['id_categoria'] = 30;
		$sqlWhere['selected'] = $this->input->post('id_tipo_producto');
		$dataView['tproducto'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Monedas
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//Vendedores
		$sqlWhere['selected'] = $this->input->post('id_vendedor');
		$dataView['vendedores'] = $this->db_vendedor->get_vendedor_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_cotizacion']);
		$productos 	= $this->db_cotizaciones->get_cotizacion_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_tiempo_entrega'], $dataView['id_tiempo_entrega']);
		unset($dataView['id_estatus_vigencia'], $dataView['id_estatus_vigencia']);
		unset($dataView['id_estatus_entrega'], $dataView['id_estatus_entrega']);
		unset($dataView['id_condiciones_pago'], $dataView['id_condiciones_pago']);
		unset($dataView['id_vigencia'], $dataView['id_vigencia']);
		unset($dataView['id_precio'], $dataView['id_precio']);
		unset($dataView['id_tipo_producto'], $dataView['id_tipo_producto']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);

		$this->parser_view('ventas/cotizaciones/tpl/modal-editar-cotizacion', $dataView, FALSE);
	}

	public function process_update_cotizacion() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_cliente',
				'id_estatus_vigencia',
				'id_estatus_entrega',
				'fecha_elaboracion',
				'atencion',
				'departamento',
				'id_moneda',
				'id_precio',
				'id_condiciones_pago',
				'id_tiempo_entrega',
				'id_lugar_entrega',
				'id_vigencia',
				'id_tipo_producto',
				'fecha_recepcion',
				'id_vendedor',
				'creador_cotizacion'
			]);

			$sqlData['atencion'] 	= strtoupper($this->input->post('atencion'));
			$sqlData['departamento'] 		= strtoupper($this->input->post('departamento'));
			$sqlData['creador_cotizacion'] = strtoupper($this->input->post('creador_cotizacion'));
			$sqlWhere = $this->input->post(['id_cotizacion']);
			$update = $this->db_cotizaciones->update_cotizacion($sqlData, $sqlWhere);
			$update OR set_exception();

			/*DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');*/

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_cotizacion_producto'));
			$sqlWhere = $this->input->post(['id_cotizacion']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_cotizaciones->update_cotizacion_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_cotizacion' 		=> $this->input->post('id_cotizacion'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento' 			=> $producto['descuento'],
					'total' 				=> $producto['total'],
					'incluye' 				=> $producto['incluye'],
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_cotizacion_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				/*DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];*/
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				//$insertBatch = $this->db_ar->insert_requisiciones_productos($sqlDataBatch);
				$insertBatch = $this->db_cotizaciones->insert_cotizacion_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			/*$this->load->library('Create_pdf');
			$sqlWhere 	= $this->input->post(['id_requisicion']);
			$estatusRQ 	= $this->db_ar->get_estatus_requisicion($sqlWhere);
			$dataView 	= array_merge($dataView, $estatusRQ);
			$settings = array(
				 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
				,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);*/

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado una cotización";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_cotizacion'], 'tbl_cotizaciones', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				//'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_cotizacion() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_cotizacion']);
			#ELIMIANCIÓN DE COTIZACION
			$update = $this->db_cotizaciones->update_cotizacion(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA
			$update = $this->db_cotizaciones->update_cotizacion_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una cotización";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_cotizacion'], 'tbl_cotizaciones', $actividad, $data_change);
			
			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	#==============Facturación================
	public function facturacion() {
		// $dataView['tpl-tbl-facturacion']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-registros']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-registros');
		
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'facturacion', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/facturacion/facturacion_view', $dataView, $includes);
	}

	public function get_modal_add_registro_facturacion(){
		$this->parser_view('ventas/facturacion/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}
	#==============FIN Facturación================
	
	#==============Mostrador y factura | pedidos internos================	
	public function mostrador() {
		$dataView['tpl-tbl-mostrador']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-mostrador');
		$dataView['tpl-tbl-mostrador-consecutivo']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-mostrador-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'mostrador', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/pedidos-internos/mostrador/mostrador_view', $dataView, $includes);
	}

	public function get_pi_mostrador(){	
		$response = $this->db_pi->get_pi_mostrador_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}
	
	public function get_modal_add_mostrador(){
		//Estatus PI
		$sqlWhere['id_categoria'] = 31;
		$sqlWhere['grupo'] = 7;
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 32;
		$sqlWhere['grupo'] = 7;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 33;
		$sqlWhere['grupo'] = 7;
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//OC
		$sqlWhere['id_categoria'] = 34;
		$sqlWhere['grupo'] = 7;
		$dataView['oc'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 35;
		$sqlWhere['grupo'] = 7;
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 36;
		$sqlWhere['grupo'] = 7;
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//VENDEDORES
		$dataView['vendedores'] = $this->db_vendedor->get_vendedores_main();
		//MONEDAS
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//COTIZACIONES
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_all_id_cotizaciones();

		#OBTENEMOS EL CONSECUTIVO DEL COTIZACION
		$folio = $this->db_pi->get_ultimo_pi_mostrador();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/pedidos-internos/mostrador/tpl/modal-nuevo-pi-mostrador', $dataView, FALSE);
	}

	public function get_modal_add_mostrador_product(){
		$dataView['tipo-producto'] = $this->db_catalogos->get_tipos_productos_min();
		$this->parser_view('ventas/pedidos-internos/mostrador/tpl/modal-add-producto-entrada', $dataView, FALSE);
	}

	public function process_save_pi_mostrador() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS PI
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'id_cotizacion',
				'id_cliente',
				'contacto',
				'id_departamento',
				'fecha_pi',
				'id_medio',
				'id_vendedor',
				'id_oc',
				'id_forma_envio',
				'incluir_iva',
				'id_moneda',
				'notas_internas',
				'notas_remision',
				'tipo_cambio',
				'id_condiciones'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$insert = $this->db_pi->insert_pi_mostrador($sqlData);
			$insert OR set_exception();

			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_pi_mostrador' 		=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				$sqlDataBatch[] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_pi->insert_pi_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $sqlData;
			$actividad 		= "ha creado un pedido interno mostrador";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_pi_mostrador', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_modal_edit_pi() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_pi_mostrador']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus PI
		$sqlWhere['id_categoria'] = 31;
		$sqlWhere['selected'] = $this->input->post('id_estatus_pi');
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//COTIZACIONES
		$sqlWhere['selected'] = $this->input->post('id_cotizacion');
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 32;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 33;
		$sqlWhere['selected'] = $this->input->post('id_medio');
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//VENDEDOR
		$sqlWhere['selected'] = $this->input->post('id_vendedor');
		$dataView['vendedores'] = $this->db_vendedor->get_vendedor_select($sqlWhere);
		//OC
		$sqlWhere['id_categoria'] = 34;
		$sqlWhere['selected'] = $this->input->post('id_oc');
		$dataView['oc'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 35;
		$sqlWhere['selected'] = $this->input->post('id_forma_envio');
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MONEDA
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 36;
		$sqlWhere['selected'] = $this->input->post('id_condiciones');
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_pi_mostrador']);
		$productos 	= $this->db_pi->get_pi_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cotizacion'], $dataView['id_cotizacion']);
		unset($dataView['folio'], $dataView['folio']);
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_estatus_pi'], $dataView['id_estatus_pi']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_oc'], $dataView['id_oc']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['id_condiciones'], $dataView['id_condiciones']);
		

		$this->parser_view('ventas/pedidos-internos/mostrador/tpl/modal-editar-pi', $dataView, FALSE);
	}

	public function process_update_pi() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'id_cotizacion',
				'id_cliente',
				'contacto',
				'id_departamento',
				'fecha_pi',
				'id_medio',
				'id_vendedor',
				'id_oc',
				'id_forma_envio',
				'incluir_iva',
				'id_moneda',
				'notas_internas',
				'notas_remision',
				'tipo_cambio',
				'id_condiciones'
			]);

			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$sqlWhere = $this->input->post(['id_pi_mostrador']);
			$update = $this->db_pi->update_pi($sqlData, $sqlWhere);
			$update OR set_exception();

			/*DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');*/

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_pi_mostrador_producto'));
			$sqlWhere = $this->input->post(['id_pi_mostrador']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_pi->update_pin_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_pi_mostrador' 		=> $this->input->post('id_pi_mostrador'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_pi_mostrador_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				/*DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];*/
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				//$insertBatch = $this->db_ar->insert_requisiciones_productos($sqlDataBatch);
				$insertBatch = $this->db_pi->insert_pi_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			/*$this->load->library('Create_pdf');
			$sqlWhere 	= $this->input->post(['id_requisicion']);
			$estatusRQ 	= $this->db_ar->get_estatus_requisicion($sqlWhere);
			$dataView 	= array_merge($dataView, $estatusRQ);
			$settings = array(
				 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
				,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);*/

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado un PI Mostrador";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_pi_mostrador'], 'tbl_pi_mostrador', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				//'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_remove_pi() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_pi_mostrador']);
			#ELIMIANCIÓN DE COTIZACION
			$update = $this->db_pi->update_pi(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA
			$update = $this->db_pi->update_pin_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado un PI";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_pi_mostrador'], 'tbl_pi_mostrador', $actividad, $data_change);
			
			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}
	
	public function factura() {
		$dataView['tpl-tbl-factura']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-factura');
		$dataView['tpl-tbl-factura-consecutivo']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-factura-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'factura', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/pedidos-internos/factura/factura_view', $dataView, $includes);
	}

	public function get_pi_facturas(){	
		$response = $this->db_pi->get_pi_factura_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}
	
	public function get_modal_add_factura(){
		//Estatus PI
		$sqlWhere['id_categoria'] = 37;
		$sqlWhere['grupo'] = 8;
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 38;
		$sqlWhere['grupo'] = 8;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 39;
		$sqlWhere['grupo'] = 8;
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 40;
		$sqlWhere['grupo'] = 8;
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 41;
		$sqlWhere['grupo'] = 8;
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//USO CFDI
		$sqlWhere['id_categoria'] = 42;
		$sqlWhere['grupo'] = 8;
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 43;
		$sqlWhere['grupo'] = 8;
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//METODO PAGO
		$sqlWhere['id_categoria'] = 44;
		$sqlWhere['grupo'] = 8;
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//VENDEDORES
		$dataView['vendedores'] = $this->db_vendedor->get_vendedores_main();
		//MONEDAS
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//COTIZACIONES
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_all_id_cotizaciones();

		#OBTENEMOS EL CONSECUTIVO DE FACTURAS
		$folio = $this->db_pi->get_ultimo_pi_factura();
		$dataView = array_merge($dataView, $folio);
		$this->parser_view('ventas/pedidos-internos/factura/tpl/modal-nuevo-pi-factura', $dataView, FALSE);
	}

	public function get_modal_add_factura_product(){
		$this->parser_view('ventas/pedidos-internos/factura/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	public function process_save_pi_factura() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS PI
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'id_cotizacion',
				'id_cliente',
				'contacto',
				'id_departamento',
				'fecha_pi',
				'id_medio',
				'id_vendedor',
				'oc',
				'id_forma_envio',
				'id_moneda',
				'notas_internas',
				'notas_facturacion',
				'tipo_cambio',
				'id_condiciones',
				'id_uso_cfdi',
				'id_forma_pago',
				'id_metodo_pago',
				'email_factura'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$insert = $this->db_pi->insert_pi_factura($sqlData);
			$insert OR set_exception();

			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_pi_factura' 		=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				$sqlDataBatch[] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_pi->insert_pi_producto_factura($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $sqlData;
			$actividad 		= "ha creado un pedido interno factura";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_pi_factura', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function get_modal_edit_pi_factura() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_pi_factura']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus PI
		$sqlWhere['id_categoria'] = 37;
		$sqlWhere['selected'] = $this->input->post('id_estatus_pi');
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//COTIZACIONES
		$sqlWhere['selected'] = $this->input->post('id_cotizacion');
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 38;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 39;
		$sqlWhere['selected'] = $this->input->post('id_medio');
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//VENDEDOR
		$sqlWhere['selected'] = $this->input->post('id_vendedor');
		$dataView['vendedores'] = $this->db_vendedor->get_vendedor_select($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 40;
		$sqlWhere['selected'] = $this->input->post('id_forma_envio');
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MONEDA
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 41;
		$sqlWhere['selected'] = $this->input->post('id_condiciones');
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//USO CFDI
		$sqlWhere['id_categoria'] = 42;
		$sqlWhere['selected'] = $this->input->post('id_uso_cfdi');
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 43;
		$sqlWhere['selected'] = $this->input->post('id_forma_pago');
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//METODO PAGO
		$sqlWhere['id_categoria'] = 44;
		$sqlWhere['selected'] = $this->input->post('id_metodo_pago');
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_pi_factura']);
		$productos 	= $this->db_pi->get_pi_factura_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		//unset($dataView['id_pi_factura'], $dataView['id_pi_factura']);
		unset($dataView['id_cotizacion'], $dataView['id_cotizacion']);
		unset($dataView['folio'], $dataView['folio']);
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_estatus_pi'], $dataView['id_estatus_pi']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_oc'], $dataView['id_oc']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['id_condiciones'], $dataView['id_condiciones']);
		

		$this->parser_view('ventas/pedidos-internos/factura/tpl/modal-editar-pi', $dataView, FALSE);
	}

	public function process_update_pi_factura() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'id_cotizacion',
				'id_cliente',
				'contacto',
				'id_departamento',
				'fecha_pi',
				'id_medio',
				'id_vendedor',
				'oc',
				'id_forma_envio',
				'id_moneda',
				'notas_internas',
				'notas_facturacion',
				'tipo_cambio',
				'id_condiciones',
				'id_uso_cfdi',
				'id_forma_pago',
				'id_metodo_pago',
				'email_factura'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$sqlWhere = $this->input->post(['id_pi_factura']);
			$update = $this->db_pi->update_pi_factura($sqlData, $sqlWhere);
			$update OR set_exception();

			/*DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');*/

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_pi_factura_producto'));
			$sqlWhere = $this->input->post(['id_pi_factura']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_pi->update_pi_factura_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_pi_factura' 		=> $this->input->post('id_pi_factura'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_pi_factura_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				/*DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];*/
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				//$insertBatch = $this->db_ar->insert_requisiciones_productos($sqlDataBatch);
				$insertBatch = $this->db_pi->insert_pi_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			/*$this->load->library('Create_pdf');
			$sqlWhere 	= $this->input->post(['id_requisicion']);
			$estatusRQ 	= $this->db_ar->get_estatus_requisicion($sqlWhere);
			$dataView 	= array_merge($dataView, $estatusRQ);
			$settings = array(
				 'file_name' 	=> 'Vale_requisicion_'.date('YmdHis')
				,'content_file' => $this->parser_view('almacen/requisicion-material/tpl/tpl-pdf-vale-requisicion-material', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);*/

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado un PI Factura";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_pi_factura'], 'tbl_pi_factura', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				//'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	#==============fin Mostrador y factura | pedidos internos================
	
	#==============Mostrador y factura | notas de crédito================
	public function mostrador_notas() {
		$dataView['tpl-tbl-mostrador']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-mostrador');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-reporte-detallado');
		
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'mostrador_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/notas-credito/mostrador/mostrador_view', $dataView, $includes);
	}

	public function get_modal_add_mostrador_notas(){
		$this->parser_view('ventas/notas-credito/mostrador/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function get_modal_add_mostrador_notas_product(){
		$this->parser_view('ventas/notas-credito/mostrador/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	public function factura_notas() {
		$dataView['tpl-tbl-factura']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-factura');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-reporte-detallado');

		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'factura_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/notas-credito/factura/factura_view', $dataView, $includes);
	}

	public function get_modal_add_factura_notas(){
		$this->parser_view('ventas/notas-credito/factura/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function get_modal_add_factura_notas_product(){
		$this->parser_view('ventas/notas-credito/factura/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}
	#==============FIN Mostrador y factura | notas de crédito================

	#==============solicitud de entrega================
	public function solicitud_entrega() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-solicitud']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-solicitud');
		
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'solicitud_entrega', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/solicitud-entrega/solicitud_view', $dataView, $includes);
	}

	public function get_modal_add_solicitud(){
		$this->parser_view('ventas/solicitud-entrega/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function get_modal_add_solicitud_product(){
		$this->parser_view('ventas/solicitud-entrega/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}
	#==============FIN solicitud de entrega================

		
	#==============solicitud de recoleccion================
	public function solicitud_recoleccion() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-solicitud']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-solicitud');
		
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'solicitud_recoleccion', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/solicitud-recoleccion/solicitud_recoleccion_view', $dataView, $includes);
	}

	public function get_modal_add_solicitud_recoleccion(){
		$this->parser_view('ventas/solicitud-recoleccion/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function get_modal_add_recoleccion_product(){
		$this->parser_view('ventas/solicitud-recoleccion/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}
	#==============FIN recoleccion================


	#==============Complementos de pago================
	public function complementos_pago() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-solicitud']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-complemento');
		
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'complementos_pago', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/complementos-pago/complementos_pago_view', $dataView, $includes);
	}

	public function get_modal_add_complementos_pago(){
		$this->parser_view('ventas/complementos-pago/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function get_modal_add_complemento_product(){
		$this->parser_view('ventas/complementos-pago/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}
	#==============FIN complementos de pago================


		
	#==============lista de precios================
	public function lista_precios() {
		$dataView['tpl-tbl-consumibles']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-consumibles');
		$dataView['tpl-tbl-refacciones']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-refacciones');
		$dataView['tpl-tbl-servicios']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-servicios');

		$dataView['tpl-tbl-equipos']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-equipos');
		$dataView['tpl-tbl-accesorios']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-accesorios');
		$dataView['tpl-tbl-otros']= $this->parser_view('ventas/lista-precios/tpl/tpl-tbl-otros');
		
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'lista_precios', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/lista-precios/precios_view', $dataView, $includes);
	}
	#==============FIN lista de precios================
		
	public function get_productos_almacenes() {
		if ($_POST['id_categoria'] || ($_POST['id_uso']==5)) {
			$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);
			if ($sqlWhere['id_uso']!=5) {
				$sqlWhere['id_vale_estatus_entrada'] = 1;
				$sqlWhere['id_vale_estatus_salida'] = 3;
				$response = $this->db_productos->get_productos_info($sqlWhere);
			
			#PRODUCTOS DE ALMACENES ACTIVOS
			} else {
				$response = $this->db_productos->get_productos_activos_info($sqlWhere);
			}

		} else $response = [];

		echo json_encode($response);
	}

	public function get_productos_vales_entrada() {
		$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);

		if ($sqlWhere['id_uso'] || $sqlWhere['id_categoria']) {
			$sqlWhere['tipo'] = 'ENTRADA';
			$productos = ($sqlWhere['id_uso']==5)
				? $this->db_vp->get_productos_vales_activos($sqlWhere)
				: $this->db_vp->get_productos_vales_entrada($sqlWhere);
			
		} else $productos = [];

		echo json_encode($productos);
	}

	public function get_modal_add_vale_entrada() {
		$sqlWhere['tipo'] = 'ENTRADA';
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada();
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		#OBTENEMOS LAS REQUISICIONES EXISTENTES
		$requisiciones = $this->db_ar->get_requisiciones_select2();
		$dataView['requisiciones'] = $requisiciones;

		#OBTENEMOS EL CONSECUTIVO DEL VALE DE ENTRADA
		$folio = $this->db_vales_pro->get_ultimo_vale_entrada();
		$dataView = array_merge($dataView, $folio);

		($this->input->post('id_uso')==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-entrada-activos', $dataView, true)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-entrada', $dataView, true);
	}

	public function get_modal_vale_salida() {
		$sqlWhere['tipo'] = 'SALIDA';
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada();
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		#OBTENEMOS EL CONSECUTIVO DEL VALE DE SALIDA
		$folio = $this->db_vales_pro->get_ultimo_vale_salida();
		$dataView = array_merge($dataView, $folio);

		($this->input->post('id_uso')==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-salida-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-nuevo-salida', $dataView, FALSE);
	}

	public function get_productos_vales_salida() {
		$sqlWhere = $this->input->post(['id_uso', 'id_categoria']);

		if ($sqlWhere['id_uso'] || $sqlWhere['id_categoria']) {
			$sqlWhere['tipo'] = 'SALIDA';
			$productos = ($sqlWhere['id_uso']==5)
				? $this->db_vp->get_productos_vales_activos($sqlWhere)
				: $this->db_vp->get_productos_vales_salida($sqlWhere);

		} else $productos = [];

		echo json_encode($productos);
	}

	public function get_modal_add_producto_salida() {
		$dataView['tipos-productos'] = $this->db_catalogos->get_tipos_productos_min();

		if($this->input->post('id_uso')==5) {
			$unidades_medida = $this->db_catalogos->get_unidades_medida_min();
			$dataView['unidades-medida'] = $unidades_medida;
			$dataView['monedas'] 		= $this->db_catalogos->get_monedas_min();

			$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-salida-activos', $dataView, FALSE);

		} else $this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-salida', $dataView, FALSE);
	}

	public function process_save_productos_entrada() {
		$response = ($this->input->post('id_uso') == 5)
			? self::save_productos_activos('ENTRADA')
			: self::save_productos_entrada();

		echo json_encode($response);
	}

	public function save_productos_entrada() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_categoria'
				,'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'id_requisicion'
				,'concepto_entrada'
				,'vale_salida_correspondiente'
				,'id_ve_tipo_entrada'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$insert = $this->db_vales_pro->insert_vales_entrada($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_entrada']= $insert;
			$dataView['tipo_entrada']= $this->input->post('tipo_entrada');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_entrada' 			=> $insert
					,'id_producto' 				=> $producto['id_producto']
					,'cantidad' 				=> $producto['cantidad']
					,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
					,'referencia_entrada' 		=> $producto['referencia_entrada']
					,'id_usuario_insert' 		=> $this->session->userdata('id_usuario')
					,'timestamp_insert' 		=> timestamp()
				];
				$sqlDataBatch[] = $sqlDataPro;

				#DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insert = $this->db_vales_pro->insert_vales_entrada_productos($sqlDataBatch);
				$insert OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= "ha creado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría:".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function save_productos_activos($tipo) {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA|SALIDA
			$sqlData = $this->input->post([
				 'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'concepto'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['tipo'] = strtoupper($tipo);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['autorizo'] = strtoupper($this->input->post('autorizo'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);

			switch ($sqlData['tipo']) {
				case 'ENTRADA': 
					$sqlData['id_requisicion'] = $this->input->post('id_requisicion');
					$sqlData['id_ve_tipo_entrada'] = $this->input->post('id_ve_tipo_entrada');
				break;
				case 'SALIDA': $sqlData['pedido_interno'] = $this->input->post('pedido_interno'); break;
			}

			$insert = $this->db_vales_pro->insert_vales_activos($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_activo']= $insert;
			$dataView['tipo_entrada']= $this->input->post('tipo_entrada');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_activo' 	=> $insert
					,'id_tipo_producto' => $producto['id_tipo_producto']
					,'id_unidad_medida' => $producto['id_unidad_medida']
					,'no_parte' 		=> $producto['no_parte']
					,'descripcion' 		=> $producto['descripcion']
					,'cantidad' 		=> $producto['cantidad']
					,'no_serie' 		=> $producto['no_serie']
					,'estado_producto' 	=> $producto['estado_producto']
					,'id_moneda' 		=> $producto['id_moneda']
					,'costo' 			=> $producto['costo']
				];
				$sqlDataBatch[] = $sqlData;

				#DATA PARA EL PDF
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$sqlData['tipo_producto'] = $producto['tipo_producto'];
				$sqlData['moneda'] 			= $producto['moneda'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_activos_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$tipo = strtolower($tipo);
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
				,'content_file' => $this->parser_view("ventas/cotizaciones/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= ($tipo=='entrada')
				? "ha creado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
				: "ha creado un vale de salida almacén/almacenes con uso: $_POST[uso]";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_activos', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_save_productos_salida() {
		$response = ($this->input->post('id_uso')==5)
			? self::save_productos_activos('SALIDA')
			: self::save_productos_salida();

		echo json_encode($response);
	}

	public function save_productos_salida() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_categoria'
				,'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'pedido_interno'
				,'concepto_salida'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$insert = $this->db_vales_pro->insert_vales_salida($sqlData);
			$insert OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_salida']= $insert;
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');

			$sqlDataBatch 	= [];
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_salida' 	=> $insert
					,'id_producto' 		=> $producto['id_producto']
					,'cantidad' 		=> $producto['cantidad']
					,'referencia_salida'=> $producto['referencia_salida']
					,'id_usuario_insert'=> $this->session->userdata('id_usuario')
					,'timestamp_insert' => timestamp()
				];
				$sqlDataBatch[] = $sqlData;

				#DATA PARA EL PDF
				$sqlData['no_parte'] 	= $producto['no_parte'];
				$sqlData['descripcion'] = $producto['descripcion'];
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_salida_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $sqlDataBatch;
			$actividad 		= "ha creado un vale de salida almacén/almacenes con uso: $_POST[uso] y categoría:".$_POST['categoria'];
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_remove_vale_entrada() {
		try {
			$this->db->trans_begin();

			$id_uso = $this->input->post('id_uso');
			if ($id_uso!=5) {
				$sqlWhere 	= $this->input->post(['id_vale_entrada']);
				#ELIMIANCIÓN DEL VALE DE ENTRADA
				$update = $this->db_vales_pro->update_vales_entrada(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA
				$update = $this->db_vales_pro->update_vales_entrada_productos(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				$actividad 		= "ha eliminado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría: ".$_POST['categoria'];
				$data_change 	= ['delete'=>['oldData'=>$_POST]];
				registro_bitacora_actividades($sqlWhere['id_vale_entrada'], 'tbl_vales_entrada', $actividad, $data_change);

			} else self::remove_vale_activos('ENTRADA');

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	private function remove_vale_activos($tipo) {
		$sqlWhere 	= $this->input->post(['id_vale_activo']);
		#ELIMIANCIÓN DEL VALE DE ENTRADA ACTIVOS
		$update = $this->db_vales_pro->update_vales_activos(['activo'=>0], $sqlWhere);
		$update OR set_exception();

		#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE ENTRADA ACTIVOS
		$update = $this->db_vales_pro->update_vales_activos_productos(['activo'=>0], $sqlWhere);
		$update OR set_exception();

		$actividad = ($tipo=='ENTRADA')
			? "ha eliminado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
			: "ha eliminado un vale de salida en almacén/almacenes con uso: $_POST[uso]";
		$data_change 	= ['delete'=>['oldData'=>$_POST]];
		registro_bitacora_actividades($sqlWhere['id_vale_activo'], 'tbl_vales_activos', $actividad, $data_change);
	}

	public function process_build_pdf_vale_entrada() {
		$response = ($this->input->post('id_uso') == 5)
			? self::build_pdf_vale_activos('ENTRADA')
			: self::build_pdf_vale_entrada();

		echo json_encode($response);
	}

	public function build_pdf_vale_entrada() {
		$sqlWhere 	= $this->input->post(['id_vale_entrada']);
		$productos 	= $this->db_vp->get_productos_vales_entrada($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'cantidad' 				=> $producto['cantidad']
				,'no_parte' 				=> $producto['no_parte']
				,'unidad_medida' 			=> $producto['unidad_medida']
				,'descripcion' 				=> $producto['descripcion']
				,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
				,'referencia_entrada' 		=> $producto['referencia_entrada']
			];
		}

		$dataView = [
			 'id_vale_entrada' 				=> $producto['id_vale_entrada']
			,'cliente' 						=> $producto['cliente']
			,'id_requisicion' 				=> $producto['id_requisicion']
			,'requisicion' 					=> $producto['requisicion']
			,'custom_fecha' 				=> $producto['custom_fecha']
			,'concepto_entrada' 			=> $producto['concepto_entrada']
			,'vale_salida_correspondiente' 	=> $producto['vale_salida_correspondiente']
			,'observaciones' 				=> $producto['observaciones']
			,'vale_almacen' 				=> $producto['vale_almacen']
			,'recibio' 						=> $producto['recibio']
			,'entrego' 						=> $producto['entrego']
			,'vo_bo' 						=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
			,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function build_pdf_vale_activos($tipo) {
		$sqlWhere 	= $this->input->post(['id_vale_activo']);
		$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'tipo_producto' 	=> $producto['tipo_producto']
				,'unidad_medida' 	=> $producto['unidad_medida']
				,'no_parte' 		=> $producto['no_parte']
				,'descripcion' 		=> $producto['descripcion']
				,'cantidad' 		=> $producto['cantidad']
				,'no_serie' 		=> $producto['no_serie']
				,'estado_producto' 	=> $producto['estado_producto']
				,'costo' 			=> $producto['costo']
				,'moneda' 			=> $producto['moneda']
			];
		}

		$dataView = [
			 'id_vale_activo' 	=> $producto['id_vale_activo']
			,'cliente' 			=> $producto['cliente']
			,'id_requisicion' 	=> $producto['id_requisicion']
			,'requisicion' 		=> $producto['requisicion']
			,'custom_fecha' 	=> $producto['custom_fecha']
			,'concepto' 		=> $producto['concepto']
			,'observaciones' 	=> $producto['observaciones']
			,'vale_almacen' 	=> $producto['vale_almacen']
			,'recibio' 			=> $producto['recibio']
			,'autorizo' 		=> $producto['autorizo']
			,'entrego' 			=> $producto['entrego']
			,'vo_bo' 			=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$tipo = strtolower($tipo);
		$settings = array(
			 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
			,'content_file' => $this->parser_view("ventas/almacenes/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function get_modal_edit_vale_entrada() {
		$id_uso = $this->input->post('id_uso');
		$dataView = $this->input->post();

		$dataEncription = ($id_uso==5) #VALES ACTIVOS
			? json_encode($this->input->post(['id_vale_activo']))
			: json_encode($this->input->post(['id_vale_entrada', 'id_categoria', 'id_uso']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		$sqlWhere['tipo'] = 'ENTRADA';
		$sqlWhere['selected'] = $this->input->post('id_vale_almacen');
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$sqlWhere['selected'] = $this->input->post('id_vale_estatus');
		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		$sqlWhere['selected'] = $this->input->post('id_ve_tipo_entrada');
		$tipos_entrada = $this->db_catalogos->get_ve_tipos_entrada($sqlWhere);
		$dataView['ve-tipos-entrada'] = $tipos_entrada;

		$sqlWhere['selected'] = $this->input->post('id_requisicion');
		$requisiciones = $this->db_ar->get_requisiciones_select2($sqlWhere);
		$dataView['requisiciones'] = $requisiciones;

		if ($id_uso==5) { #VALES ACTIVOS
			$sqlWhere 	= $this->input->post(['id_vale_activo']);
			$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);

		} else {
			$sqlWhere 	= $this->input->post(['id_vale_entrada']);
			$productos 	= $this->db_vp->get_productos_vales_entrada($sqlWhere);
		}
		$dataView['list-productos'] = json_encode($productos);
		// debug($dataView);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_vale_estatus'], $dataView['estatus']);
		unset($dataView['id_vale_almacen'], $dataView['vale_almacen']);
		unset($dataView['id_ve_tipo_entrada']);
		unset($dataView['id_requisicion'], $dataView['folio_requisicion']);

		($id_uso ==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-editar-entrada-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-editar-entrada', $dataView, FALSE);
	}

	public function process_update_productos_entrada() {
		$response = ($this->input->post('id_uso')==5)
			? self::update_productos_activos('ENTRADA')
			: self::update_productos_entrada();

		echo json_encode($response);
	}

	public function update_productos_entrada() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE ENTRADA
			$sqlData = $this->input->post([
				 'id_vale_estatus'
				,'cliente'
				,'id_requisicion'
				,'concepto_entrada'
				,'vale_salida_correspondiente'
				,'id_ve_tipo_entrada'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$sqlWhere = $this->input->post(['id_vale_entrada', 'id_categoria', 'id_uso']);
			$update = $this->db_vales_pro->update_vales_entrada($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_entrada']= $this->input->post('id_vale_entrada');
			$dataView['tipo_entrada'] 	= $this->input->post('tipo_entrada');
			$dataView['vale_almacen'] 	= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_vale_entrada_producto'));
			$sqlWhere = $this->input->post(['id_vale_entrada']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_entrada_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_entrada' 			=> $this->input->post('id_vale_entrada')
					,'id_producto' 				=> $producto['id_producto']
					,'cantidad' 				=> $producto['cantidad']
					,'referencia_alfanumerica' 	=> $producto['referencia_alfanumerica']
					,'referencia_entrada' 		=> $producto['referencia_entrada']
					,'id_usuario_insert' 		=> $this->session->userdata('id_usuario')
					,'timestamp_insert' 		=> timestamp()
				];

				if (!isset($producto['id_vale_entrada_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				#DATA PARA EL PDF
				$sqlDataPro['no_parte'] 	= $producto['no_parte'];
				$sqlDataPro['descripcion'] = $producto['descripcion'];
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_entrada_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_entrada_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-entrada', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad 		= "ha editado un vale de entrada en almacén/almacenes con uso: $_POST[uso] y categoría: ".$_POST['categoria'];
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_entrada'], 'tbl_vales_entrada', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}

	public function update_productos_activos($tipo) {
		try {
			$this->db->trans_begin();
			$tipo = strtoupper($tipo);
			#GUARDAMOS VALE DE ACTIVOS
			$sqlData = $this->input->post([
				 'id_uso'
				,'id_vale_estatus'
				,'cliente'
				,'concepto'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['autorizo'] = strtoupper($this->input->post('autorizo'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);

			switch ($tipo) {
				case 'ENTRADA': 
					$sqlData['id_requisicion'] = $this->input->post('id_requisicion');
					$sqlData['id_ve_tipo_entrada'] = $this->input->post('id_ve_tipo_entrada');
				break;
				case 'SALIDA': $sqlData['pedido_interno'] = $this->input->post('pedido_interno'); break;
			}

			$sqlWhere = $this->input->post(['id_vale_activo']);
			$update = $this->db_vales_pro->update_vales_activos($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_activo'] = $this->input->post('id_vale_activo');
			$dataView['tipo_entrada'] 	= $this->input->post('tipo_entrada');
			$dataView['vale_almacen'] 	= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');
			$dataView['requisicion'] 	= $this->input->post('requisicion');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LIST
			$productosActivos = array_filter(array_column($productos, 'id_vale_activo_producto'));
			$sqlWhere = $this->input->post(['id_vale_activo']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_activos_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					 'id_vale_activo' 	=> $this->input->post('id_vale_activo')
					,'id_tipo_producto' => $producto['id_tipo_producto']
					,'id_unidad_medida' => $producto['id_unidad_medida']
					,'no_parte' 		=> $producto['no_parte']
					,'descripcion' 		=> $producto['descripcion']
					,'cantidad' 		=> $producto['cantidad']
					,'no_serie' 		=> $producto['no_serie']
					,'estado_producto' 	=> $producto['estado_producto']
					,'id_moneda' 		=> $producto['id_moneda']
					,'costo' 			=> $producto['costo']
				];

				if (!isset($producto['id_vale_activo_producto'])) {
					$sqlDataBatch[] = $sqlDataPro;
				}

				#DATA PARA EL PDF
				$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
				$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];
				$sqlDataPro['moneda'] 			= $producto['moneda'];
				$dataView['list-productos'][] = $sqlDataPro;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_vales_pro->insert_vales_activos_productos($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			#GENERANDO EL PDF
			$tipo = strtolower($tipo);
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> "Vale_{$tipo}_".date('YmdHis')
				,'content_file' => $this->parser_view("ventas/cotizaciones/tpl/tpl-pdf-vale-{$tipo}-activos", $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$sqlData['productos'] = $dataView['list-productos'];
			$actividad = ($tipo == 'entrada')
				? "ha editado un vale de entrada en almacén/almacenes con uso: $_POST[uso]"
				: "ha editado un vale de salida en almacén/almacenes con uso: $_POST[uso]";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_vale_activo'], 'tbl_vales_activos', $actividad, $data_change);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}

	public function process_remove_vale_salida() {
		try {
			$this->db->trans_begin();

			$id_uso = $this->input->post('id_uso');
			if ($id_uso!=5) {
				$sqlWhere 	= $this->input->post(['id_vale_salida']);
				#ELIMIANCIÓN DEL VALE DE SALIDA
				$update = $this->db_vales_pro->update_vales_salida(['activo'=>0], $sqlWhere);
				$update OR set_exception();

				#ELIMIANCIÓN DE LOS PRODUCTOS DEL VALE DE SALIDA
				$update = $this->db_vales_pro->update_vales_salida_productos(['activo'=>0], $sqlWhere);
				$update OR set_exception();
			} else self::remove_vale_activos('SALIDA');

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_rm_success'),
				'icon' 		=> 'success'
			];

			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$this->db->trans_rollback();
			$response = get_exception($e);
		}

		echo json_encode($response);
	}

	public function process_build_pdf_vale_salida() {
		$response = ($this->input->post('id_uso') == 5)
			? self::build_pdf_vale_activos('SALIDA')
			: self::build_pdf_vale_salida();

		echo json_encode($response);
	}

	public function build_pdf_vale_salida() {
		$sqlWhere 	= $this->input->post(['id_vale_salida']);
		$productos 	= $this->db_vp->get_productos_vales_salida($sqlWhere);

		$listProductos = [];
		foreach ($productos as $producto) {
			$listProductos[] = [
				 'cantidad' 				=> $producto['cantidad']
				,'no_parte' 				=> $producto['no_parte']
				,'unidad_medida' 			=> $producto['unidad_medida']
				,'descripcion' 				=> $producto['descripcion']
				,'referencia_salida' 		=> $producto['referencia_salida']
			];
		}

		$dataView = [
			 'id_vale_salida' 				=> $producto['id_vale_salida']
			,'cliente' 						=> $producto['cliente']
			,'pedido_interno' 				=> $producto['pedido_interno']
			,'custom_fecha' 				=> $producto['custom_fecha']
			,'concepto_salida' 				=> $producto['concepto_salida']
			,'observaciones' 				=> $producto['observaciones']
			,'vale_almacen' 				=> $producto['vale_almacen']
			,'recibio' 						=> $producto['recibio']
			,'entrego' 						=> $producto['entrego']
			,'vo_bo' 						=> $producto['vo_bo']
		];
		$dataView['list-productos'] = $listProductos;

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
			,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
			,'load_file' 	=> FALSE
			,'orientation' 	=> 'landscape'
		);

		return [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];
	}

	public function get_modal_edit_vale_salida() {
		$id_uso = $this->input->post('id_uso');
		$dataView = $this->input->post();

		$dataEncription = ($id_uso==5) #VALES ACTIVOS
			? json_encode($this->input->post(['id_vale_activo']))
			: json_encode($this->input->post(['id_vale_salida', 'id_categoria', 'id_uso']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		$sqlWhere['tipo'] = 'SALIDA';
		$sqlWhere['selected'] = $this->input->post('id_vale_almacen');
		$vales_almacen = $this->db_av->get_vales_almacenes($sqlWhere);
		$dataView['vales-almacen'] = $vales_almacen;

		$sqlWhere['selected'] = $this->input->post('id_vale_estatus');
		$vales_estatus = $this->db_av->get_vales_estatus($sqlWhere);
		$dataView['vales-estatus'] = $vales_estatus;

		if ($id_uso==5) { #VALES ACTIVOS
			$sqlWhere 	= $this->input->post(['id_vale_activo']);
			$productos 	= $this->db_vp->get_productos_vales_activos($sqlWhere);
		} else {
			$sqlWhere 	= $this->input->post(['id_vale_salida']);
			$productos 	= $this->db_vp->get_productos_vales_salida($sqlWhere);
		}
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_vale_estatus'], $dataView['estatus']);
		unset($dataView['id_vale_almacen'], $dataView['vale_almacen']);

		($id_uso ==5)
			? $this->parser_view('ventas/cotizaciones/tpl/modal-editar-salida-activos', $dataView, FALSE)
			: $this->parser_view('ventas/cotizaciones/tpl/modal-editar-salida', $dataView, FALSE);
	}

	public function process_update_productos_salida() {
		$response = ($this->input->post('id_uso')==5)
			? self::update_productos_activos('SALIDA')
			: self::update_productos_salida();

		echo json_encode($response);
	}

	public function update_productos_salida() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS VALE DE SALIDA
			$sqlData = $this->input->post([
				 'id_vale_estatus'
				,'cliente'
				,'pedido_interno'
				,'concepto_salida'
				,'observaciones'
				,'id_vale_almacen'
			]);
			$sqlData['recibio'] = strtoupper($this->input->post('recibio'));
			$sqlData['entrego'] = strtoupper($this->input->post('entrego'));
			$sqlData['vo_bo'] = strtoupper($this->input->post('vo_bo'));
			$sqlData['fecha_hora'] = implode(' ', [
				$this->input->post('fecha')
				,date('H:i:s')
			]);
			$sqlWhere = $this->input->post(['id_vale_salida', 'id_categoria', 'id_uso']);
			$update = $this->db_vales_pro->update_vales_salida($sqlData, $sqlWhere);
			$update OR set_exception();

			#DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_vale_salida']= $this->input->post('id_vale_salida');
			$dataView['vale_almacen']= $this->input->post('vale_almacen');
			$dataView['custom_fecha'] 	= $this->input->post('custom_fecha');

			$productos = $this->input->post('productos');
			#ELIMINACION DE PRODUCTOS QUE NO LLEGAN EN LA LISTA
			$productosActivos = array_filter(array_column($productos, 'id_vale_salida_producto'));
			$sqlWhere = $this->input->post(['id_vale_salida']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_vales_pro->update_vales_salida_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlData = [
					 'id_vale_salida' 	=> $this->input->post('id_vale_salida')
					,'id_producto' 		=> $producto['id_producto']
					,'cantidad' 		=> $producto['cantidad']
					,'referencia_salida'=> $producto['referencia_salida']
					,'id_usuario_insert'=> $this->session->userdata('id_usuario')
					,'timestamp_insert' => timestamp()
				];

				if (!isset($producto['id_vale_salida_producto'])) {
					$sqlDataBatch[] = $sqlData;
				}

				#DATA PARA EL PDF
				$sqlData['no_parte'] 	= $producto['no_parte'];
				$sqlData['descripcion'] = $producto['descripcion'];
				$sqlData['unidad_medida'] = $producto['unidad_medida'];
				$dataView['list-productos'][] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insert = $this->db_vales_pro->insert_vales_salida_productos($sqlDataBatch);
				$insert OR set_exception();
			}

			#GENERANDO EL PDF
			$this->load->library('Create_pdf');
			$settings = array(
				 'file_name' 	=> 'Vale_salida_'.date('YmdHis')
				,'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-vale-salida', $dataView)
				,'load_file' 	=> FALSE
				,'orientation' 	=> 'landscape'
			);

			$response = [
				'success'	=> TRUE,
				'msg' 		=> lang('vales_entrada_save_success'),
				'icon' 		=> 'success',
				'file_path' => $this->create_pdf->create_file($settings)
			];
			$this->db->trans_commit();
		} catch (SB_Exception $e) {
			$response = get_exception($e);
		}

		return $response;
	}
}

/* End of file Almacen.php */
/* Location: ./application/modules/almacen/controllers/Almacenes.php */