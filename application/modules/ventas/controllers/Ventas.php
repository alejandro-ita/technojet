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
		$this->load->model('ventas/Facturacion_model', 'db_fac');
		$this->load->model('ventas/Notas_credito', 'db_nc');
		$this->load->model('ventas/Solicitudes_entrada', 'db_se');
		$this->load->model('ventas/Solicitudes_recoleccion', 'db_sr');
		$this->load->model('ventas/Complementos_model', 'db_com');
	}

	public function cotizaciones() {
		$dataView['tpl-tbl-cotizaciones']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-cotizaciones');
		$dataView['tpl-tbl-cotizaciones-consecutivo']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-cotizaciones-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/cotizaciones/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'cotizaciones', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		$includes['modulo']['js'][] = ['name'=>'charts', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
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

		//Departamento
		$sqlWhere['id_categoria'] = 45;
		$sqlWhere['grupo'] = 6;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);

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

	public function get_consecutivo(){
		$response = $this->db_cotizaciones->get_cotizaciones_consecutivo();

		/*$tplAcciones = $this->parser_view('ventas/cotizaciones/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}*/

		echo json_encode($response);
	}

	public function get_modal_add_producto_cotizacion() {
		$dataView['tipo-producto'] = $this->db_catalogos->get_tipos_productos_min();
		$this->parser_view('ventas/cotizaciones/tpl/modal-add-producto-cotizacion', $dataView, FALSE);
	}

	public function get_modal_add_producto_nota(){
		$this->parser_view('ventas/cotizaciones/tpl/modal-add-nota', FALSE, FALSE);
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
			$sqlData['creador_cotizacion'] = strtoupper($this->input->post('creador_cotizacion'));
			$insert = $this->db_cotizaciones->insert_cotizacion($sqlData);
			$insert OR set_exception();
			//SAVE PRODUCTOS
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_cotizacion' 		=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento' 			=> $producto['descuento'],
					'total' 				=> $producto['total'],
					'incluye' 				=> isset($producto['incluye']) ? json_encode($producto['incluye']) : '',
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'opcional' 				=> $producto['opcional'],
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

			//SAVE NOTAS
			$notas 	= $this->input->post('notas');
			if($notas != null){
				
				foreach ($notas as $nota) {
					$sqlData = [
						'id_cotizacion' 		=> $insert,
						'nota' 					=> $nota['nota'],
						'descripcion' 			=> $nota['descripcion'],
						'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
						'timestamp_insert' 	=> timestamp()
					];
	
					$sqlNotasBatch[] = $sqlData;
				}
	
				if ($sqlNotasBatch) {
					$insertBatch = $this->db_cotizaciones->insert_cotizacion_nota($sqlNotasBatch);
					$insertBatch OR set_exception();
				}

				$sqlData['notas'] = $sqlData;
			}

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

		$sqlWhere 	= $this->input->post(['id_cotizacion']);
		$notas 	= $this->db_cotizaciones->get_cotizacion_notas($sqlWhere);
		$dataView['list-notas'] = json_encode($notas);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		//unset($dataView['list-productos'], $dataView['list-productos']);
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
		unset($dataView['cliente'], $dataView['cliente']);

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
					'incluye' 				=> isset($producto['incluye']) ? json_encode($producto['incluye']) : '',
					'comision_vendedor' 	=> $producto['comision_vendedor'],
					'opcional' 				=> $producto['opcional'],
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

			$sqlData['productos'] = $dataView['list-productos'];

			#ELIMINACION DE NOTAS QUE NO LLEGAN EN LA LIST	
			$notas = $this->input->post('notas');
			
			if($notas != null){
				$notasActivas = array_filter(array_column($notas, 'id_nota'));
				$sqlWhere = $this->input->post(['id_cotizacion']);
				$sqlWhere['activo'] = 1;
				if($notasActivas) $sqlWhere['notIn'] = $notasActivas;
				$update = $this->db_cotizaciones->update_cotizacion_notas(['activo'=>0], $sqlWhere);

				#REGISTRO DE NUEVAS NOTAS
				$sqlBatchNotas = [];
				foreach ($notas as $nota) {
					$sqlDataPro = [
						'id_cotizacion' 		=> $this->input->post('id_cotizacion'),
						'nota' 					=> $nota['nota'],
						'descripcion' 			=> $nota['descripcion'],
						'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
						'timestamp_insert' 	=> timestamp()
					];

					if (!isset($nota['id_nota'])) {
						$sqlBatchNotas[] = $sqlDataPro;
					}

					/*DATA PARA EL PDF
					$sqlDataPro['no_parte'] 	= $producto['no_parte'];
					$sqlDataPro['descripcion'] = $producto['descripcion'];
					$sqlDataPro['unidad_medida'] = $producto['unidad_medida'];
					$sqlDataPro['tipo_producto'] = $producto['tipo_producto'];*/
					$dataView['list-notas'][] = $sqlDataPro;
				}

				if ($sqlBatchNotas) {
					$insertBatch = $this->db_cotizaciones->insert_cotizacion_nota($sqlBatchNotas);
					$insertBatch OR set_exception();
				}

				$sqlData['notas'] = $dataView['list-notas'];
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

	public function createPdfCotizacion(){
		
		$sqlWhere 	= $this->input->post(['id_cotizacion']);
		$productos 	= $this->db_cotizaciones->get_cotizacion_productos($sqlWhere);
		$notas 	= $this->db_cotizaciones->get_cotizacion_notas($sqlWhere);
		$cotizacion = $this->db_cotizaciones->get_cotizaciones_main($sqlWhere, FALSE);
		$total = 0;
		$listProductos = [];
		$listOpcionales = [];
		$listNotas = [];
		foreach ($productos as $producto) {

			$array = json_decode($producto['incluye'], true);
			$lista = '';
			if($array){
				foreach ($array as $value) {
					$lista .=  "* <b>" . strtoupper($value['incluye']) . "</b> &nbsp;&nbsp;&nbsp;";
				}
			}

			if($producto['opcional'] == 1){
				//opcional
				$listOpcionales[] = [
					'cantidad' 				=> $producto['cantidad'],
					'no_parte' 				=> $producto['no_parte'],
					'unidad_medida' 		=> $producto['unidad_medida'],
					'descripcion' 			=> $producto['descripcion'],
					'precio'				=> $producto['precio_unitario'],
					'total'					=> $producto['total'],
					'list-incluye'			=> $lista
				];
			}else{
				//Grand total
				$total = $total + $producto['total'];
				$listProductos[] = [
					'cantidad' 				=> $producto['cantidad'],
					'no_parte' 				=> $producto['no_parte'],
					'unidad_medida' 		=> $producto['unidad_medida'],
					'descripcion' 			=> $producto['descripcion'],
					'precio'				=> $producto['precio_unitario'],
					'total'					=> $producto['total'],
					'list-incluye'			=> $lista
				];
			}
		}

		foreach($notas as $nota){
			$listNotas[] = [
				'nota' 		=> $nota['nota'],
				'descripcion' 	=> $nota['descripcion'],
			];
		}

		setlocale(LC_ALL, 'es_mx');
		$date = strftime("%A, %d de %B de %Y", strtotime($cotizacion['fecha_elaboracion']));
		$dataView['razon_social'] = $cotizacion['razon_social'];
		$dataView['rfc'] = $cotizacion['rfc'];
		$dataView['moneda'] = $cotizacion['moneda'];
		$dataView['grand_total'] = number_format($total, 2);
		$dataView['fecha_elaboracion'] = $date;
		$dataView['departamento'] = $cotizacion['depto'];
		$dataView['folio'] = $cotizacion['folio'];
		$dataView['list-productos'] = $listProductos;
		$dataView['list-opcionales'] = $listOpcionales;
		$dataView['cantidadLetra'] = $this->numtoletras($total);
		$dataView['direccion'] = $cotizacion['direccion'];
		$dataView['municipio'] = $cotizacion['municipio'];
		$dataView['estado'] = $cotizacion['estado'];
		$dataView['telefono'] = $cotizacion['telefono'];
		$dataView['cp'] = $cotizacion['cp'];
		$dataView['contacto'] = $cotizacion['contacto'];
		$dataView['departamento'] = $cotizacion['depto_cliente'];
		$dataView['list-notas'] = $listNotas;
		$dataView['total-opcionales'] = count($listOpcionales);
		$dataView['total-notas'] = count($listNotas);


		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'cotizacion_'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/cotizaciones/tpl/tpl-pdf-cotizacion', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'portrait'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	function numtoletras($xcifra){

		$xarray = array(0 => "Cero",
			1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
			"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
			"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
			100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
		);
	
		$xcifra = trim($xcifra);
		$xlength = strlen($xcifra);
		$xpos_punto = strpos($xcifra, ".");
		$xaux_int = $xcifra;
		$xdecimales = "00";
		if (!($xpos_punto === false)) {
			if ($xpos_punto == 0) {
				$xcifra = "0" . $xcifra;
				$xpos_punto = strpos($xcifra, ".");
			}
			$xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
			$xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
		}
	
		$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
		$xcadena = "";
		for ($xz = 0; $xz < 3; $xz++) {
			$xaux = substr($XAUX, $xz * 6, 6);
			$xi = 0;
			$xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
			$xexit = true; // bandera para controlar el ciclo del While
			while ($xexit) {
				if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
					break; // termina el ciclo
				}
	
				$x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
				$xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
				for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
					switch ($xy) {
						case 1: // checa las centenas
							if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
								
							} else {
								$key = (int) substr($xaux, 0, 3);
								if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
									$xseek = $xarray[$key];
									$xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
									if (substr($xaux, 0, 3) == 100)
										$xcadena = " " . $xcadena . " CIEN " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
								}
								else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
									$key = (int) substr($xaux, 0, 1) * 100;
									$xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
									$xcadena = " " . $xcadena . " " . $xseek;
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 0, 3) < 100)
							break;
						case 2: // checa las decenas (con la misma lógica que las centenas)
							if (substr($xaux, 1, 2) < 10) {
								
							} else {
								$key = (int) substr($xaux, 1, 2);
								if (TRUE === array_key_exists($key, $xarray)) {
									$xseek = $xarray[$key];
									$xsub = $this->subfijo($xaux);
									if (substr($xaux, 1, 2) == 20)
										$xcadena = " " . $xcadena . " VEINTE " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3;
								}
								else {
									$key = (int) substr($xaux, 1, 1) * 10;
									$xseek = $xarray[$key];
									if (20 == substr($xaux, 1, 1) * 10)
										$xcadena = " " . $xcadena . " " . $xseek;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " Y ";
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 1, 2) < 10)
							break;
						case 3: // checa las unidades
							if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
								
							} else {
								$key = (int) substr($xaux, 2, 1);
								$xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
								$xsub = $this->subfijo($xaux);
								$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
							} // ENDIF (substr($xaux, 2, 1) < 1)
							break;
					} // END SWITCH
				} // END FOR
				$xi = $xi + 3;
			} // ENDDO
	
			if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
				$xcadena.= " DE";
	
			if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
				$xcadena.= " DE";
	
			// ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
			if (trim($xaux) != "") {
				switch ($xz) {
					case 0:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN BILLON ";
						else
							$xcadena.= " BILLONES ";
						break;
					case 1:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN MILLON ";
						else
							$xcadena.= " MILLONES ";
						break;
					case 2:
						if ($xcifra < 1) {
							//$xcadena = "CERO PESOS $xdecimales/100 M.N.";
							$xcadena = "CERO DOLARES $xdecimales CENTAVOS";
						}
						if ($xcifra >= 1 && $xcifra < 2) {
							//$xcadena = "UN PESO $xdecimales/100 M.N. ";
							$xcadena = "UN DOLAR $xdecimales  CENTAVOS ";
						}
						if ($xcifra >= 2) {
							$xcadena.= " DOLARES $xdecimales CENTAVOS "; //
						}
						break;
				} // endswitch ($xz)
			} // ENDIF (trim($xaux) != "")
			// ------------------      en este caso, para México se usa esta leyenda     ----------------
			$xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
		} // ENDFOR ($xz)
		return trim($xcadena);
	}
	
	function subfijo($xx){ 
		// esta función regresa un subfijo para la cifra
		$xx = trim($xx);
		$xstrlen = strlen($xx);
		if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
			$xsub = "";
		//
		if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
			$xsub = "MIL";
		//
		return $xsub;
	}	

	#==============Facturación================
	public function facturacion() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-registros']= $this->parser_view('ventas/facturacion/tpl/tpl-tbl-registros');
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'facturacion', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/facturacion/facturacion_view', $dataView, $includes);
	}

	public function get_facturas(){
		$response = $this->db_fac->get_facturas_main();

		$tplAcciones = $this->parser_view('ventas/cotizaciones/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_registro_facturacion(){
		//ESTATUS FACTURACION
		$sqlWhere['id_categoria'] = 46;
		$sqlWhere['grupo'] = 9;
		$dataView['estatus-factura'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//USO CFDI
		$sqlWhere['id_categoria'] = 47;
		$sqlWhere['grupo'] = 9;
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//METODO DE PAGO
		$sqlWhere['id_categoria'] = 48;
		$sqlWhere['grupo'] = 9;
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//ESTATUS ENTREGA
		$sqlWhere['id_categoria'] = 49;
		$sqlWhere['grupo'] = 9;
		$dataView['estatus-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA PAGO
		$sqlWhere['id_categoria'] = 80;
		$sqlWhere['grupo'] = 9;
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Monedas
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		//Clientes
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		#OBTENEMOS EL CONSECUTIVO DEL COTIZACION
		$folio = $this->db_fac->get_ultima_factura();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/facturacion/tpl/modal-nueva-factura', $dataView, FALSE);
	}

	public function process_save_factura() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS FACTURA
			$sqlData = $this->input->post([
				'id_estatus_factura',
				'fecha_elaboracion',
				'id_uso_cfdi',
				'no_pi',
				'id_cliente',
				'subtotal',
				'descuento',
				'iva',
				'id_moneda',
				'concepto',
				'id_metodo_pago',
				'id_estatus_entrega',
				'id_forma_pago',
				'observaciones',
				'total'
			]);
			
			$sqlData['concepto'] 	= strtoupper($this->input->post('concepto'));

			$insert = $this->db_fac->insert_factura($sqlData);
			$insert OR set_exception();

			$actividad 		= "ha creado una factura";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_facturacion', $actividad, $data_change);

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

	public function get_modal_edit_factura() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_factura']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus factura
		$sqlWhere['id_categoria'] = 46;
		$sqlWhere['selected'] = $this->input->post('id_estatus_factura');
		$dataView['estatus-factura'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Uso CFDI
		$sqlWhere['id_categoria'] = 47;
		$sqlWhere['selected'] = $this->input->post('id_uso_cfdi');
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Metodo de pago
		$sqlWhere['id_categoria'] = 48;
		$sqlWhere['selected'] = $this->input->post('id_metodo_pago');
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Estatus entrega
		$sqlWhere['id_categoria'] = 49;
		$sqlWhere['selected'] = $this->input->post('id_estatus_entrega');
		$dataView['estatus-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Forma de pago
		$sqlWhere['id_categoria'] = 80;
		$sqlWhere['selected'] = $this->input->post('id_forma_pago');
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Monedas
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_estatus_entrega'], $dataView['id_estatus_entrega']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['cliente'], $dataView['cliente']);

		$this->parser_view('ventas/facturacion/tpl/modal-editar-factura', $dataView, FALSE);
	}

	public function process_update_factura() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_estatus_factura',
				'fecha_elaboracion',
				'id_uso_cfdi',
				'no_pi',
				'id_cliente',
				'subtotal',
				'descuento',
				'iva',
				'id_moneda',
				'concepto',
				'id_metodo_pago',
				'id_estatus_entrega',
				'id_forma_pago',
				'observaciones',
				'total'
			]);

			$sqlData['concepto'] 	= strtoupper($this->input->post('concepto'));

			$sqlWhere = $this->input->post(['id_factura']);
			$update = $this->db_fac->update_factura($sqlData, $sqlWhere);
			$update OR set_exception();

			/*DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');*/

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

			$actividad 		= "ha editado una factura";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_factura'], 'tbl_facturacion', $actividad, $data_change);

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

	public function process_remove_factura() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_factura']);
			#ELIMIANCIÓN DE FACTURA
			$update = $this->db_fac->update_factura(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una factura";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_factura'], 'tbl_facturas', $actividad, $data_change);
			
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
		//TIPO PEDIDO
		$sqlWhere['id_categoria'] = 87;
		$sqlWhere['grupo'] = 7;
		$dataView['tipo-pedido'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 88;
		$sqlWhere['grupo'] = 7;
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
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
				'id_condiciones',
				'observaciones',
				'id_tipo_pedido',
				'id_tipo_producto'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
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
					//'comision_vendedor' 	=> $producto['comision_vendedor'],
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
		//TIPO PEDIDO
		$sqlWhere['id_categoria'] = 87;
		$sqlWhere['selected'] = $this->input->post('id_tipo_pedido');
		$dataView['tipo-pedido'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 88;
		$sqlWhere['selected'] = $this->input->post('id_tipo_producto');
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_pi_mostrador']);
		$productos 	= $this->db_pi->get_pi_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cotizacion'], $dataView['id_cotizacion']);
		unset($dataView['folio'], $dataView['folio']);
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_estatus_pi'], $dataView['id_estatus_pi']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_oc'], $dataView['id_oc']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['cliente'], $dataView['cliente']);
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
				'id_condiciones',
				'observaciones',
				'id_tipo_pedido',
				'id_tipo_producto'
			]);

			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
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

	public function process_build_pdf_pi_mostrador(){
		$sqlWhere 	= $this->input->post(['id_pi_mostrador']);
		$productos 	= $this->db_pi->get_pi_productos($sqlWhere);
		$pi_mostrador = $this->db_pi->get_pi_mostrador_main($sqlWhere, FALSE);
		$total = 0;
		$cont = 0;

		$listProductos = [];
		foreach ($productos as $producto) {
			//Grand total
			$total = $total + $producto['total'];
			$cont = $cont + 1;
			$listProductos[] = [
				'p'						=> $cont,
				'cantidad' 				=> $producto['cantidad'],
				'no_parte' 				=> $producto['no_parte'],
				'unidad_medida' 		=> $producto['unidad_medida'],
				'descripcion' 			=> $producto['descripcion'],
				'precio_unitario'		=> $producto['precio_unitario'],
				'total'					=> $producto['total'],
				'descuento_pieza'		=> $producto['descuento_pieza'],
				'descuento_total'		=> $producto['descuento_total']
			];
		}

		$dataView['list-productos'] = $listProductos;
		$dataView['razon_social'] = $pi_mostrador['razon_social'];
		$dataView['fecha'] = $pi_mostrador['fecha_pi'];
		$dataView['folio'] = $pi_mostrador['folio'];
		$dataView['tipo_cambio'] = $pi_mostrador['tipo_cambio'];
		$dataView['medio'] = $pi_mostrador['medio'];
		$dataView['contacto'] = $pi_mostrador['contacto'];
		$dataView['departamento'] = $pi_mostrador['depto'];
		$dataView['notas_internas'] = $pi_mostrador['notas_internas'];
		$dataView['notas_remision'] = $pi_mostrador['notas_remision'];

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'pi_mostrador'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-pdf-pi-mostrador', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

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
		//TIPO DE PEDIDO
		$sqlWhere['id_categoria'] = 89;
		$sqlWhere['grupo'] = 8;
		$dataView['tipo-pedido'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO DE PRODUCTO
		$sqlWhere['id_categoria'] = 90;
		$sqlWhere['grupo'] = 8;
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
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
				'email_factura',
				'observaciones',
				'id_tipo_pedido',
				'id_tipo_producto'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
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
		//TIPO PEDIDO
		$sqlWhere['id_categoria'] = 89;
		$sqlWhere['selected'] = $this->input->post('id_tipo_pedido');
		$dataView['tipo-pedido'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 90;
		$sqlWhere['selected'] = $this->input->post('id_tipo_producto');
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

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
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_oc'], $dataView['id_oc']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['cliente'], $dataView['cliente']);
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
				'email_factura',
				'observaciones',
				'id_tipo_pedido',
				'id_tipo_producto'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
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

	public function process_remove_pi_factura() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_pi_factura']);
			#ELIMIANCIÓN DE PI FACT
			$update = $this->db_pi->update_pi_factura(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS
			$update = $this->db_pi->update_pi_factura_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado un PI factura";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_pi_factura'], 'tbl_pi_factura', $actividad, $data_change);
			
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

	public function process_build_pdf_pi_factura(){
		$sqlWhere 	= $this->input->post(['id_pi_factura']);
		$productos 	= $this->db_pi->get_pi_factura_productos($sqlWhere);
		$pi_factura = $this->db_pi->get_pi_factura_main($sqlWhere, FALSE);
		$total = 0;
		$cont = 0;

		$listProductos = [];
		foreach ($productos as $producto) {
			//Grand total
			$total = $total + $producto['total'];
			$cont = $cont + 1;
			$listProductos[] = [
				'p'						=> $cont,
				'cantidad' 				=> $producto['cantidad'],
				'no_parte' 				=> $producto['no_parte'],
				'unidad_medida' 		=> $producto['unidad_medida'],
				'descripcion' 			=> $producto['descripcion'],
				'precio_unitario'		=> $producto['precio_unitario'],
				'total'					=> $producto['total'],
				'descuento_pieza'		=> $producto['descuento_pieza'],
				'descuento_total'		=> $producto['descuento_total']
			];
		}

		$dataView['list-productos'] = $listProductos;
		$dataView['razon_social'] = $pi_factura['razon_social'];
		$dataView['fecha'] = $pi_factura['fecha_pi'];
		$dataView['folio'] = $pi_factura['folio'];
		$dataView['tipo_cambio'] = $pi_factura['tipo_cambio'];
		$dataView['medio'] = $pi_factura['medio'];
		$dataView['contacto'] = $pi_factura['contacto'];
		$dataView['departamento'] = $pi_factura['departamento'];
		$dataView['notas_internas'] = $pi_factura['notas_internas'];
		$dataView['notas_facturacion'] = $pi_factura['notas_facturacion'];

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'pi_factura'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-pdf-pi-factura', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	#==============fin Mostrador y factura | pedidos internos================
	
	#==============Mostrador y factura | notas de crédito================
	public function mostrador_notas() {
		/*$dataView['tpl-tbl-mostrador']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-mostrador');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-reporte-detallado');
		
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'mostrador_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/notas-credito/mostrador/mostrador_view', $dataView, $includes);*/
		$dataView['tpl-tbl-mostrador']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-mostrador');
		$dataView['tpl-tbl-mostrador-consecutivo']= $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-tbl-mostrador-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'mostrador_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/notas-credito/mostrador/mostrador_view', $dataView, $includes);
	}

	public function get_nc_mostrador(){	
		$response = $this->db_nc->get_pi_mostrador_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/mostrador/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_mostrador_notas(){
		//Estatus PI
		$sqlWhere['id_categoria'] = 50;
		$sqlWhere['grupo'] = 10;
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 51;
		$sqlWhere['grupo'] = 10;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 52;
		$sqlWhere['grupo'] = 10;
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 53;
		$sqlWhere['grupo'] = 10;
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 54;
		$sqlWhere['grupo'] = 10;
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 81;
		$sqlWhere['grupo'] = 10;
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO ENTREGA
		$sqlWhere['id_categoria'] = 82;
		$sqlWhere['grupo'] = 10;
		$dataView['tipo-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//VENDEDORES
		$dataView['vendedores'] = $this->db_vendedor->get_vendedores_main();
		//MONEDAS
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//COTIZACIONES
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_all_id_cotizaciones();

		#OBTENEMOS EL CONSECUTIVO DEL COTIZACION
		$folio = $this->db_nc->get_ultimo_pi_mostrador();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/notas-credito/mostrador/tpl/modal-nuevo-nc-mostrador', $dataView, FALSE);
	}

	public function get_modal_add_mostrador_notas_product(){
		$this->parser_view('ventas/notas-credito/mostrador/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	public function factura_notas() {
		$dataView['tpl-tbl-factura']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-factura');
		$dataView['tpl-tbl-factura-consecutivo']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-factura-consecutivo');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-tbl-reporte-detallado');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'factura_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/notas-credito/factura/factura_view', $dataView, $includes);
		/*$dataView['tpl-tbl-factura']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-factura');
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/notas-credito/factura/tpl/tpl-tbl-reporte-detallado');

		$pathJS = get_var('path_js');
    	$includes['modulo']['js'][] = ['name'=>'factura_notas', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];

		$this->load_view('ventas/notas-credito/factura/factura_view', $dataView, $includes);*/
	}

	public function get_nc_facturas(){	
		$response = $this->db_nc->get_pi_factura_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_nota_factura(){
		//Estatus PI
		$sqlWhere['id_categoria'] = 55;
		$sqlWhere['grupo'] = 11;
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 56;
		$sqlWhere['grupo'] = 11;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 57;
		$sqlWhere['grupo'] = 11;
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 58;
		$sqlWhere['grupo'] = 11;
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 59;
		$sqlWhere['grupo'] = 11;
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//USO CFDI
		$sqlWhere['id_categoria'] = 60;
		$sqlWhere['grupo'] = 11;
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 61;
		$sqlWhere['grupo'] = 11;
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//METODO PAGO
		$sqlWhere['id_categoria'] = 62;
		$sqlWhere['grupo'] = 11;
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 83;
		$sqlWhere['grupo'] = 11;
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//TIPO ENTREGA
		$sqlWhere['id_categoria'] = 84;
		$sqlWhere['grupo'] = 11;
		$dataView['tipo-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//VENDEDORES
		$dataView['vendedores'] = $this->db_vendedor->get_vendedores_main();
		//MONEDAS
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//COTIZACIONES
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_all_id_cotizaciones();

		#OBTENEMOS EL CONSECUTIVO DE FACTURAS
		$folio = $this->db_nc->get_ultimo_pi_factura();
		$dataView = array_merge($dataView, $folio);
		$this->parser_view('ventas/notas-credito/factura/tpl/modal-nuevo-pi-factura', $dataView, FALSE);
		//$this->parser_view('ventas/notas-credito/factura/tpl/modal-nuevo-entrada', FALSE, FALSE);
	}

	public function process_save_nc_factura() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS NC
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'fecha_pi',
				'fact_remision',
				'id_cliente',
				'contacto',
				'id_departamento',
				'id_medio',
				'id_uso_cfdi',
				'oc',
				'id_forma_envio',
				'notas_internas',
				'notas_facturacion',
				'tipo_cambio',
				'id_condiciones',
				'id_tipo_producto',
				'id_tipo_entrega',
				'motivo_credito',
				'observaciones',
				'id_forma_pago',
				'id_metodo_pago',
				'email_factura'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$sqlData['motivo_credito'] = strtoupper($this->input->post('motivo_credito'));
			$insert = $this->db_nc->insert_pi_factura($sqlData);
			$insert OR set_exception();

			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_nc_factura' 		=> $insert,
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
				$insertBatch = $this->db_nc->insert_pi_producto_factura($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $sqlData;
			$actividad 		= "ha creado una cota de crédito factura";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_nc_factura', $actividad, $data_change);

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

	public function get_modal_edit_nc_factura() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_nc_factura']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus PI
		$sqlWhere['id_categoria'] = 55;
		$sqlWhere['selected'] = $this->input->post('id_estatus_pi');
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//COTIZACIONES
		$sqlWhere['selected'] = $this->input->post('id_cotizacion');
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 56;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 57;
		$sqlWhere['selected'] = $this->input->post('id_medio');
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//VENDEDOR
		$sqlWhere['selected'] = $this->input->post('id_vendedor');
		$dataView['vendedores'] = $this->db_vendedor->get_vendedor_select($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 58;
		$sqlWhere['selected'] = $this->input->post('id_forma_envio');
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MONEDA
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 59;
		$sqlWhere['selected'] = $this->input->post('id_condiciones');
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//USO CFDI
		$sqlWhere['id_categoria'] = 60;
		$sqlWhere['selected'] = $this->input->post('id_uso_cfdi');
		$dataView['uso-cfdi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 61;
		$sqlWhere['selected'] = $this->input->post('id_forma_pago');
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//METODO PAGO
		$sqlWhere['id_categoria'] = 62;
		$sqlWhere['selected'] = $this->input->post('id_metodo_pago');
		$dataView['metodo-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 83;
		$sqlWhere['selected'] = $this->input->post('id_tipo_producto');
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO ENTREGA
		$sqlWhere['id_categoria'] = 84;
		$sqlWhere['selected'] = $this->input->post('id_tipo_entrega');
		$dataView['tipo-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_nc_factura']);
		$productos 	= $this->db_nc->get_pi_factura_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		//unset($dataView['id_pi_factura'], $dataView['id_pi_factura']);
		unset($dataView['id_cotizacion'], $dataView['id_cotizacion']);
		unset($dataView['folio'], $dataView['folio']);
		unset($dataView['id_estatus_pi'], $dataView['id_estatus_pi']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['cliente'], $dataView['cliente']);
		unset($dataView['id_condiciones'], $dataView['id_condiciones']);
		

		$this->parser_view('ventas/notas-credito/factura/tpl/modal-editar-pi', $dataView, FALSE);
	}

	public function process_update_nc_factura() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'fecha_pi',
				'fact_remision',
				'id_cliente',
				'contacto',
				'id_departamento',
				'id_medio',
				'id_uso_cfdi',
				'oc',
				'id_forma_envio',
				'notas_internas',
				'notas_facturacion',
				'tipo_cambio',
				'id_condiciones',
				'id_tipo_producto',
				'id_tipo_entrega',
				'motivo_credito',
				'observaciones',
				'id_forma_pago',
				'id_metodo_pago',
				'email_factura'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['oc'] = strtoupper($this->input->post('oc'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_facturacion'] = strtoupper($this->input->post('notas_facturacion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$sqlData['motivo_credito'] = strtoupper($this->input->post('motivo_credito'));
			$sqlWhere = $this->input->post(['id_nc_factura']);
			$update = $this->db_nc->update_pi_factura($sqlData, $sqlWhere);
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
			$productosActivos = array_filter(array_column($productos, 'id_nc_factura_producto'));
			$sqlWhere = $this->input->post(['id_nc_factura']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_nc->update_pi_factura_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_nc_factura' 		=> $this->input->post('id_nc_factura'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_nc_factura_producto'])) {
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
				$insertBatch = $this->db_nc->insert_pi_producto($sqlDataBatch);
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
			$actividad 		= "ha editado un Nota de crédito Factura";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_nc_factura'], 'tbl_nc_factura', $actividad, $data_change);

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

	public function get_modal_add_factura_notas_product(){
		$this->parser_view('ventas/notas-credito/factura/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	public function process_save_nc_mostrador() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS PI
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'fecha_pi',
				'fact_remision',
				'id_cliente',
				'contacto',
				'id_departamento',
				'id_medio',
				'id_oc',
				'id_forma_envio',
				'notas_internas',
				'notas_remision',
				'tipo_cambio',
				'id_condiciones',
				'id_tipo_producto',
				'id_tipo_entrega',
				'motivo_credito',
				'observaciones'
			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$sqlData['motivo_credito'] = strtoupper($this->input->post('motivo_credito'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$insert = $this->db_nc->insert_pi_mostrador($sqlData);
			$insert OR set_exception();

			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_nc_mostrador' 		=> $insert,
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
				$insertBatch = $this->db_nc->insert_pi_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $sqlData;
			$actividad 		= "ha creado una nota de crédito mostrador";
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

	public function get_modal_edit_nc() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_nc_mostrador']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus PI
		$sqlWhere['id_categoria'] = 50;
		$sqlWhere['selected'] = $this->input->post('id_estatus_pi');
		$dataView['estatus-pi'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//COTIZACIONES
		$sqlWhere['selected'] = $this->input->post('id_cotizacion');
		$dataView['cotizaciones'] = $this->db_cotizaciones->get_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 51;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MEDIO
		$sqlWhere['id_categoria'] = 52;
		$sqlWhere['selected'] = $this->input->post('id_medio');
		$dataView['medio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//VENDEDOR
		$sqlWhere['selected'] = $this->input->post('id_vendedor');
		$dataView['vendedores'] = $this->db_vendedor->get_vendedor_select($sqlWhere);
		//FORMA ENVIO
		$sqlWhere['id_categoria'] = 53;
		$sqlWhere['selected'] = $this->input->post('id_forma_envio');
		$dataView['forma-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO PRODUCTO
		$sqlWhere['id_categoria'] = 81;
		$sqlWhere['selected'] = $this->input->post('id_tipo_producto');
		$dataView['tipo-producto'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO ENTREGA
		$sqlWhere['id_categoria'] = 82;
		$sqlWhere['selected'] = $this->input->post('id_tipo_entrega');
		$dataView['tipo-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//MONEDA
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//CONDICIONES
		$sqlWhere['id_categoria'] = 54;
		$sqlWhere['selected'] = $this->input->post('id_condiciones');
		$dataView['condiciones'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_nc_mostrador']);
		$productos 	= $this->db_nc->get_pi_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cotizacion'], $dataView['id_cotizacion']);
		unset($dataView['folio'], $dataView['folio']);
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_estatus_pi'], $dataView['id_estatus_pi']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_medio'], $dataView['id_medio']);
		unset($dataView['id_oc'], $dataView['id_oc']);
		unset($dataView['id_forma_envio'], $dataView['id_forma_envio']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);
		unset($dataView['id_vendedor'], $dataView['id_vendedor']);
		unset($dataView['vendedor'], $dataView['vendedor']);
		unset($dataView['cliente'], $dataView['cliente']);
		unset($dataView['id_condiciones'], $dataView['id_condiciones']);
		

		$this->parser_view('ventas/notas-credito/mostrador/tpl/modal-editar-pi', $dataView, FALSE);
	}

	public function process_update_nc() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_estatus_pi',
				'fecha_pi',
				'fact_remision',
				'id_cliente',
				'contacto',
				'id_departamento',
				'id_medio',
				'id_oc',
				'id_forma_envio',
				'notas_internas',
				'notas_remision',
				'tipo_cambio',
				'id_condiciones',
				'id_tipo_producto',
				'id_tipo_entrega',
				'motivo_credito',
				'observaciones'

			]);
			
			$sqlData['contacto'] 	= strtoupper($this->input->post('contacto'));
			$sqlData['notas_internas'] 		= strtoupper($this->input->post('notas_internas'));
			$sqlData['notas_remision'] = strtoupper($this->input->post('notas_remision'));
			$sqlData['motivo_credito'] = strtoupper($this->input->post('motivo_credito'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$sqlWhere = $this->input->post(['id_nc_mostrador']);
			$update = $this->db_nc->update_pi($sqlData, $sqlWhere);
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
			$productosActivos = array_filter(array_column($productos, 'id_nc_mostrador_producto'));
			$sqlWhere = $this->input->post(['id_nc_mostrador']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_nc->update_pin_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_nc_mostrador' 		=> $this->input->post('id_nc_mostrador'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'precio_unitario' 		=> $producto['precio_unitario'],
					'descuento_pieza' 		=> $producto['descuento_pieza'],
					'descuento_total' 		=> $producto['descuento_total'],
					'total' 				=> $producto['total'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_nc_mostrador_producto'])) {
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
				$insertBatch = $this->db_nc->insert_pi_producto($sqlDataBatch);
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
			$actividad 		= "ha editado una Nota de credito Mostrador";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_nc_mostrador'], 'tbl_nc_mostrador', $actividad, $data_change);

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

	public function process_remove_nc() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_nc_mostrador']);
			#ELIMIANCIÓN DE NC
			$update = $this->db_nc->update_pi(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS DEL NC
			$update = $this->db_nc->update_pin_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una nota de crédito";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_nc_mostrador'], 'tbl_nc_mostrador', $actividad, $data_change);
			
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

	public function process_remove_nc_factura() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_nc_factura']);
			#ELIMIANCIÓN DE NCF
			$update = $this->db_nc->update_pi_factura(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS 
			$update = $this->db_nc->update_pi_factura_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una nota de crédito Factura";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_nc_factura'], 'tbl_nc_mostrador', $actividad, $data_change);
			
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

	public function process_build_pdf_nc_mostrador(){
		$sqlWhere 	= $this->input->post(['id_nc_mostrador']);
		$productos 	= $this->db_nc->get_pi_productos($sqlWhere);
		$pi_factura = $this->db_nc->get_pi_mostrador_main($sqlWhere, FALSE);
		$total = 0;
		$cont = 0;

		$listProductos = [];
		foreach ($productos as $producto) {
			//Grand total
			$total = $total + $producto['total'];
			$cont = $cont + 1;
			$listProductos[] = [
				'p'						=> $cont,
				'cantidad' 				=> $producto['cantidad'],
				'no_parte' 				=> $producto['no_parte'],
				'unidad_medida' 		=> $producto['unidad_medida'],
				'descripcion' 			=> $producto['descripcion'],
				'precio_unitario'		=> $producto['precio_unitario'],
				'total'					=> $producto['total'],
				'descuento_pieza'		=> $producto['descuento_pieza'],
				'descuento_total'		=> $producto['descuento_total']
			];
		}

		$dataView['list-productos'] = $listProductos;
		/*$dataView['razon_social'] = $pi_factura['razon_social'];
		$dataView['fecha'] = $pi_factura['fecha_pi'];
		$dataView['folio'] = $pi_factura['folio'];
		$dataView['tipo_cambio'] = $pi_factura['tipo_cambio'];
		$dataView['medio'] = $pi_factura['medio'];
		$dataView['contacto'] = $pi_factura['contacto'];
		$dataView['departamento'] = $pi_factura['departamento'];
		$dataView['notas_internas'] = $pi_factura['notas_internas'];
		$dataView['notas_facturacion'] = $pi_factura['notas_facturacion'];*/

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'pi_factura'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/notas-credito/mostrador/tpl/tpl-pdf-nc-mostrador', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	public function process_build_pdf_nc_factura(){
		$sqlWhere 	= $this->input->post(['id_nc_factura']);
		$productos 	= $this->db_nc->get_pi_factura_productos($sqlWhere);
		$pi_factura = $this->db_nc->get_pi_factura_main($sqlWhere, FALSE);
		$total = 0;
		$cont = 0;

		$listProductos = [];
		foreach ($productos as $producto) {
			//Grand total
			$total = $total + $producto['total'];
			$cont = $cont + 1;
			$listProductos[] = [
				'p'						=> $cont,
				'cantidad' 				=> $producto['cantidad'],
				'no_parte' 				=> $producto['no_parte'],
				'unidad_medida' 		=> $producto['unidad_medida'],
				'descripcion' 			=> $producto['descripcion'],
				'precio_unitario'		=> $producto['precio_unitario'],
				'total'					=> $producto['total'],
				'descuento_pieza'		=> $producto['descuento_pieza'],
				'descuento_total'		=> $producto['descuento_total']
			];
		}

		$dataView['list-productos'] = $listProductos;
		/*$dataView['razon_social'] = $pi_factura['razon_social'];
		$dataView['fecha'] = $pi_factura['fecha_pi'];
		$dataView['folio'] = $pi_factura['folio'];
		$dataView['tipo_cambio'] = $pi_factura['tipo_cambio'];
		$dataView['medio'] = $pi_factura['medio'];
		$dataView['contacto'] = $pi_factura['contacto'];
		$dataView['departamento'] = $pi_factura['departamento'];
		$dataView['notas_internas'] = $pi_factura['notas_internas'];
		$dataView['notas_facturacion'] = $pi_factura['notas_facturacion'];*/

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'nc_factura'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/notas-credito/factura/tpl/tpl-pdf-nc-factura', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	#==============FIN Mostrador y factura | notas de crédito================

	#==============solicitud de entrega================
	public function solicitud_entrega() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-solicitud-consecutivo']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-solicitud-consecutivo');
		$dataView['tpl-tbl-solicitud']= $this->parser_view('ventas/solicitud-entrega/tpl/tpl-tbl-solicitud');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'solicitud_entrega', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/solicitud-entrega/solicitud_view', $dataView, $includes);
	}

	public function get_modal_add_solicitud(){
		//Estatus
		$sqlWhere['id_categoria'] = 63;
		$sqlWhere['grupo'] = 12;
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Departamento
		$sqlWhere['id_categoria'] = 64;
		$sqlWhere['grupo'] = 12;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Tipo envio
		$sqlWhere['id_categoria'] = 65;
		$sqlWhere['grupo'] = 12;
		$dataView['tipo-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Paquetería
		$sqlWhere['id_categoria'] = 66;
		$sqlWhere['grupo'] = 12;
		$dataView['paqueteria'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Condiciones de entrega
		$sqlWhere['id_categoria'] = 67;
		$sqlWhere['grupo'] = 12;
		$dataView['condicion-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Departamento solicitante
		$sqlWhere['id_categoria'] = 68;
		$sqlWhere['grupo'] = 12;
		$dataView['departamento-solicitante'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Almacen saliente
		$sqlWhere['id_categoria'] = 69;
		$sqlWhere['grupo'] = 12;
		$dataView['almacen-saliente'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Forma pago
		$sqlWhere['id_categoria'] = 85;
		$sqlWhere['grupo'] = 12;
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		#OBTENEMOS EL CONSECUTIVO
		$folio = $this->db_se->get_ultima_solicitud();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/solicitud-entrega/tpl/modal-nueva-solicitud', $dataView, FALSE);
	}

	public function get_modal_add_solicitud_product(){
		$dataView['tipo-producto'] = $this->db_catalogos->get_tipos_productos_min();
		$this->parser_view('ventas/solicitud-entrega/tpl/modal-add-producto-entrada', $dataView, FALSE);
	}

	public function get_solicitudes_entrada(){
		$response = $this->db_se->get_solicitudes_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function process_save_solicitud_entrega() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS
			$sqlData = $this->input->post([
				'fecha_solicitud',
				'id_estatus',
				'id_departamento',
				'id_tipo_envio',
				'consignado',
				'pi_nc_oc',
				'id_paqueteria',
				'id_cliente',
				'contacto',
				'id_condicion_entrega',
				'direccion',
				'id_dep_solicitante',
				'id_almacen_saliente',
				'id_forma_pago',
				'observaciones'
			]);
			
			$sqlData['consignado'] 	= strtoupper($this->input->post('consignado'));
			$sqlData['pi_nc_oc'] = strtoupper($this->input->post('pi_nc_oc'));
			$sqlData['contacto'] = strtoupper($this->input->post('contacto'));
			$sqlData['direccion'] = strtoupper($this->input->post('direccion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$insert = $this->db_se->insert_solicitud($sqlData);
			$insert OR set_exception();
			//SAVE PRODUCTOS
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_solicitud' 			=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				$sqlDataBatch[] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_se->insert_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$actividad 		= "ha creado una solicitud de entrega";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_solicitud_entrega', $actividad, $data_change);

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

	public function get_modal_edit_solicitud_entrega() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_solicitud']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);
		
		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//ESTATUS
		$sqlWhere['id_categoria'] = 63;
		$sqlWhere['selected'] = $this->input->post('id_estatus');
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 64;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO ENVIO
		$sqlWhere['id_categoria'] = 65;
		$sqlWhere['selected'] = $this->input->post('id_tipo_envio');
		$dataView['tipo-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//PAQUETERIA
		$sqlWhere['id_categoria'] = 66;
		$sqlWhere['selected'] = $this->input->post('id_paqueteria');
		$dataView['paqueteria'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//CONDICION ENTREGA
		$sqlWhere['id_categoria'] = 67;
		$sqlWhere['selected'] = $this->input->post('id_condicion_entrega');
		$dataView['condicion-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//DEPARTAMENTO SOLICITANTE
		$sqlWhere['id_categoria'] = 68;
		$sqlWhere['selected'] = $this->input->post('id_dep_solicitante');
		$dataView['departamento-solicitante'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//ALMACEN SALIENTE
		$sqlWhere['id_categoria'] = 69;
		$sqlWhere['selected'] = $this->input->post('id_almacen_saliente');
		$dataView['almacen-saliente'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 85;
		$sqlWhere['selected'] = $this->input->post('id_forma_pago');
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_solicitud']);
		$productos 	= $this->db_se->get_solicitud_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);

		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['cliente'], $dataView['cliente']);
		unset($dataView['id_estatus'], $dataView['id_estatus']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_tipo_envio'], $dataView['id_tipo_envio']);
		unset($dataView['id_paqueteria'], $dataView['id_paqueteria']);
		unset($dataView['id_condicion_entrega'], $dataView['id_condicion_entrega']);
		unset($dataView['id_dep_solicitante'], $dataView['id_dep_solicitante']);
		unset($dataView['id_almacen_saliente'], $dataView['id_almacen_saliente']);
		unset($dataView['id_forma_pago'], $dataView['id_forma_pago']);

		$this->parser_view('ventas/solicitud-entrega/tpl/modal-editar-solicitud', $dataView, FALSE);
	}

	public function process_update_solicitud_entrega() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_solicitud',
				'fecha_solicitud',
				'id_estatus',
				'id_departamento',
				'id_tipo_envio',
				'consignado',
				'pi_nc_oc',
				'id_paqueteria',
				'id_cliente',
				'contacto',
				'id_condicion_entrega',
				'direccion',
				'id_dep_solicitante',
				'id_almacen_saliente',
				'id_forma_pago',
				'observaciones'
			]);
			
			$sqlData['consignado'] 	= strtoupper($this->input->post('consignado'));
			$sqlData['pi_nc_oc'] = strtoupper($this->input->post('pi_nc_oc'));
			$sqlData['contacto'] = strtoupper($this->input->post('contacto'));
			$sqlData['direccion'] = strtoupper($this->input->post('direccion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$sqlWhere = $this->input->post(['id_solicitud']);
			$update = $this->db_se->update_solicitud($sqlData, $sqlWhere);
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
			$productosActivos = array_filter(array_column($productos, 'id_solicitud_entrega_productos'));
			$sqlWhere = $this->input->post(['id_solicitud']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_se->update_solicitud_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_solicitud' 			=> $this->input->post('id_solicitud'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_solicitud_entrega_productos'])) {
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
				$insertBatch = $this->db_se->insert_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $dataView['list-productos'];

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

			$actividad 		= "ha editado una solicitud de entrega";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_solicitud'], 'tbl_solicitud_entrega', $actividad, $data_change);

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

	public function process_remove_solicitud_entrega() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_solicitud']);
			#ELIMIANCIÓN
			$update = $this->db_se->update_solicitud(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS
			$update = $this->db_se->update_solicitud_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una solicitud de entrega";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_solicitud'], 'tbl_solicitud_entrega', $actividad, $data_change);
			
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

	public function process_build_pdf_solicitud_entrega(){
		$sqlWhere 	= $this->input->post(['id_solicitud']);
		$productos 	= $this->db_se->get_solicitud_productos($sqlWhere);
		$pi_factura = $this->db_se->get_solicitudes_main($sqlWhere, FALSE);
		$total = 0;
		$cont = 0;

		$listProductos = [];
		foreach ($productos as $producto) {
			//Grand total
			$listProductos[] = [
				'cantidad' 				=> $producto['cantidad'],
				'no_parte' 				=> $producto['no_parte'],
				'unidad_medida' 		=> $producto['unidad_medida'],
				'descripcion' 			=> $producto['descripcion'],
			];
		}

		$dataView['list-productos'] = $listProductos;
		/*$dataView['razon_social'] = $pi_factura['razon_social'];
		$dataView['fecha'] = $pi_factura['fecha_pi'];
		$dataView['folio'] = $pi_factura['folio'];
		$dataView['tipo_cambio'] = $pi_factura['tipo_cambio'];
		$dataView['medio'] = $pi_factura['medio'];
		$dataView['contacto'] = $pi_factura['contacto'];
		$dataView['departamento'] = $pi_factura['departamento'];
		$dataView['notas_internas'] = $pi_factura['notas_internas'];
		$dataView['notas_facturacion'] = $pi_factura['notas_facturacion'];*/

		#GENERANDO EL PDF
		$this->load->library('Create_pdf');
		$settings = array(
			'file_name' 	=> 'solicitud_entrega'.date('YmdHis'),
			'content_file' => $this->parser_view('ventas/solicitud-entrega/tpl/tpl-pdf-solicitud-entrega', $dataView),
			'load_file' 	=> FALSE,
			'orientation' 	=> 'landscape'
		);

		$response = [
			'success'	=> TRUE,
			'file_path' => $this->create_pdf->create_file($settings)
		];

		echo json_encode($response);
	}

	#==============FIN solicitud de entrega================

		
	#==============solicitud de recoleccion================
	public function solicitud_recoleccion() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-reporte-detallado');
		$dataView['tpl-tbl-solicitud-consecutivo']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-solicitud-consecutivo');
		$dataView['tpl-tbl-solicitud']= $this->parser_view('ventas/solicitud-recoleccion/tpl/tpl-tbl-solicitud');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'solicitud_recoleccion', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/solicitud-recoleccion/solicitud_recoleccion_view', $dataView, $includes);
	}

	public function get_solicitudes_recoleccion(){
		$response = $this->db_sr->get_solicitudes_main();

		$tplAcciones = $this->parser_view('ventas/pedidos-internos/factura/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_solicitud_recoleccion(){
		//Estatus
		$sqlWhere['id_categoria'] = 70;
		$sqlWhere['grupo'] = 13;
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Departamento
		$sqlWhere['id_categoria'] = 71;
		$sqlWhere['grupo'] = 13;
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Tipo envio
		$sqlWhere['id_categoria'] = 72;
		$sqlWhere['grupo'] = 13;
		$dataView['tipo-envio'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Paquetería
		$sqlWhere['id_categoria'] = 73;
		$sqlWhere['grupo'] = 13;
		$dataView['paqueteria'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Condiciones de recoleccion
		$sqlWhere['id_categoria'] = 74;
		$sqlWhere['grupo'] = 13;
		$dataView['condicion-entrega'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Departamento solicitante
		$sqlWhere['id_categoria'] = 75;
		$sqlWhere['grupo'] = 13;
		$dataView['departamento-solicitante'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Almacen saliente
		$sqlWhere['id_categoria'] = 76;
		$sqlWhere['grupo'] = 13;
		$dataView['almacen-saliente'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//Forma pago
		$sqlWhere['id_categoria'] = 86;
		$sqlWhere['grupo'] = 13;
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		#OBTENEMOS EL CONSECUTIVO
		$folio = $this->db_sr->get_ultima_solicitud();
		$dataView = array_merge($dataView, $folio);
		$this->parser_view('ventas/solicitud-recoleccion/tpl/modal-nueva-solicitud', $dataView, FALSE);
	}

	public function process_save_solicitud_recoleccion() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS
			$sqlData = $this->input->post([
				'fecha_solicitud',
				'id_estatus',
				'id_departamento',
				'id_tipo_envio',
				'consignado',
				'pi_nc_oc',
				'id_paqueteria',
				'id_cliente',
				'contacto',
				'id_condicion_entrega',
				'direccion',
				'id_dep_solicitante',
				'id_almacen_entrante',
				'id_forma_pago',
				'observaciones'
			]);
			
			$sqlData['consignado'] 	= strtoupper($this->input->post('consignado'));
			$sqlData['pi_nc_oc'] = strtoupper($this->input->post('pi_nc_oc'));
			$sqlData['contacto'] = strtoupper($this->input->post('contacto'));
			$sqlData['direccion'] = strtoupper($this->input->post('direccion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$insert = $this->db_sr->insert_solicitud($sqlData);
			$insert OR set_exception();
			//SAVE PRODUCTOS
			$productos 		= $this->input->post('productos');
			foreach ($productos as $producto) {
				$sqlData = [
					'id_solicitud' 			=> $insert,
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				$sqlDataBatch[] = $sqlData;
			}

			if ($sqlDataBatch) {
				$insertBatch = $this->db_sr->insert_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$actividad 		= "ha creado una solicitud de recoleccion";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_solicitud_recoleccion', $actividad, $data_change);

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

	public function get_modal_edit_solicitud_recoleccion() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_solicitud']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);
		
		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//ESTATUS
		$sqlWhere['id_categoria'] = 70;
		$sqlWhere['selected'] = $this->input->post('id_estatus');
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//DEPARTAMENTO
		$sqlWhere['id_categoria'] = 71;
		$sqlWhere['selected'] = $this->input->post('id_departamento');
		$dataView['departamento'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//TIPO ENVIO
		$sqlWhere['id_categoria'] = 72;
		$sqlWhere['selected'] = $this->input->post('id_tipo_envio');
		$dataView['tipo-envio'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//PAQUETERIA
		$sqlWhere['id_categoria'] = 73;
		$sqlWhere['selected'] = $this->input->post('id_paqueteria');
		$dataView['paqueteria'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//CONDICION ENTREGA
		$sqlWhere['id_categoria'] = 74;
		$sqlWhere['selected'] = $this->input->post('id_condicion_entrega');
		$dataView['condicion-entrega'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//DEPARTAMENTO SOLICITANTE
		$sqlWhere['id_categoria'] = 75;
		$sqlWhere['selected'] = $this->input->post('id_dep_solicitante');
		$dataView['departamento-solicitante'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//ALMACEN SALIENTE
		$sqlWhere['id_categoria'] = 76;
		$sqlWhere['selected'] = $this->input->post('id_almacen_entrante');
		$dataView['almacen-saliente'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//FORMA DE PAGO
		$sqlWhere['id_categoria'] = 86;
		$sqlWhere['selected'] = $this->input->post('id_forma_pago');
		$dataView['forma-pago'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);

		$sqlWhere 	= $this->input->post(['id_solicitud']);
		$productos 	= $this->db_sr->get_solicitud_productos($sqlWhere);
		$dataView['list-productos'] = json_encode($productos);

		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['cliente'], $dataView['cliente']);
		unset($dataView['id_estatus'], $dataView['id_estatus']);
		unset($dataView['id_departamento'], $dataView['id_departamento']);
		unset($dataView['id_tipo_envio'], $dataView['id_tipo_envio']);
		unset($dataView['id_paqueteria'], $dataView['id_paqueteria']);
		unset($dataView['id_condicion_entrega'], $dataView['id_condicion_entrega']);
		unset($dataView['id_dep_solicitante'], $dataView['id_dep_solicitante']);
		unset($dataView['id_almacen_entrante'], $dataView['id_almacen_entrante']);
		unset($dataView['id_forma_pago'], $dataView['id_forma_pago']);

		$this->parser_view('ventas/solicitud-recoleccion/tpl/modal-editar-solicitud', $dataView, FALSE);
	}

	public function process_update_solicitud_recoleccion() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_solicitud',
				'fecha_solicitud',
				'id_estatus',
				'id_departamento',
				'id_tipo_envio',
				'consignado',
				'pi_nc_oc',
				'id_paqueteria',
				'id_cliente',
				'contacto',
				'id_condicion_entrega',
				'direccion',
				'id_dep_solicitante',
				'id_almacen_entrante',
				'id_forma_pago',
				'observaciones'
			]);
			
			$sqlData['consignado'] 	= strtoupper($this->input->post('consignado'));
			$sqlData['pi_nc_oc'] = strtoupper($this->input->post('pi_nc_oc'));
			$sqlData['contacto'] = strtoupper($this->input->post('contacto'));
			$sqlData['direccion'] = strtoupper($this->input->post('direccion'));
			$sqlData['observaciones'] = strtoupper($this->input->post('observaciones'));
			$sqlWhere = $this->input->post(['id_solicitud']);
			$update = $this->db_sr->update_solicitud($sqlData, $sqlWhere);
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
			$productosActivos = array_filter(array_column($productos, 'id_solicitud_recoleccion_productos'));
			$sqlWhere = $this->input->post(['id_solicitud']);
			$sqlWhere['activo'] = 1;
			if($productosActivos) $sqlWhere['notIn'] = $productosActivos;
			$update = $this->db_sr->update_solicitud_productos(['activo'=>0], $sqlWhere);
			#$update OR set_exception();

			#REGISTRO DE NUEVOS PRODUCTOS
			$sqlDataBatch = [];
			foreach ($productos as $producto) {
				$sqlDataPro = [
					'id_solicitud' 			=> $this->input->post('id_solicitud'),
					'id_producto' 			=> $producto['id_producto'],
					'cantidad' 				=> $producto['cantidad'],
					'id_usuario_insert' 	=> $this->session->userdata('id_usuario'),
					'timestamp_insert' 	=> timestamp()
				];

				if (!isset($producto['id_solicitud_recoleccion_productos'])) {
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
				$insertBatch = $this->db_sr->insert_producto($sqlDataBatch);
				$insertBatch OR set_exception();
			}

			$sqlData['productos'] = $dataView['list-productos'];

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

			$actividad 		= "ha editado una solicitud de recoleccion";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_solicitud'], 'tbl_solicitud_recoleccion', $actividad, $data_change);

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

	public function get_modal_add_recoleccion_product(){
		$this->parser_view('ventas/solicitud-recoleccion/tpl/modal-add-producto-entrada', FALSE, FALSE);
	}

	public function process_remove_solicitud_recoleccion() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_solicitud']);
			#ELIMIANCIÓN
			$update = $this->db_sr->update_solicitud(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			#ELIMIANCIÓN DE LOS PRODUCTOS
			$update = $this->db_sr->update_solicitud_productos(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado una solicitud de recoleccion";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_solicitud'], 'tbl_solicitud_recoleccion', $actividad, $data_change);
			
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

	#==============FIN recoleccion================


	#==============Complementos de pago================
	public function complementos_pago() {
		$dataView['tpl-tbl-reporte-mensual']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-reporte-mensual');
		$dataView['tpl-tbl-reporte-detallado']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-reporte-detallado');
		//$dataView['tpl-tbl-complementos-consecutivo']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-complemento-consecutivo');
		$dataView['tpl-tbl-complementos']= $this->parser_view('ventas/complementos-pago/tpl/tpl-tbl-complementos');
		
		$includes 	= get_includes_vendor(['dataTables', 'jQValidate']);
		$pathJS = get_var('path_js');
		$includes['modulo']['js'][] = ['name'=>'complementos_pago', 'dirname'=>"$pathJS/ventas", 'fulldir'=>TRUE];
		
		$this->load_view('ventas/complementos-pago/complementos_pago_view', $dataView, $includes);
	}

	public function get_complementos(){
		$response = $this->db_com->get_complementos_main();

		$tplAcciones = $this->parser_view('ventas/cotizaciones/tpl/tpl-acciones');
		foreach ($response as &$cotizacion) {
			$cotizacion['acciones'] = $tplAcciones;
		}

		echo json_encode($response);
	}

	public function get_modal_add_complementos_pago(){
		//ESTATUS COMPLEMENTO
		$sqlWhere['id_categoria'] = 77;
		$sqlWhere['grupo'] = 14;
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//NUMERO PARCIALIDAD
		$sqlWhere['id_categoria'] = 79;
		$sqlWhere['grupo'] = 14;
		$dataView['parcialidades'] = $this->db_vc->get_ventas_cotizacion_min($sqlWhere);
		//CLIENTES
		$dataView['clientes'] = $this->db_cliente->get_clientes_main();
		//FACTURAS
		$dataView['facturas'] = $this->db_fac->get_facturas_main();
		//MONEDAS
		$dataView['monedas'] = $this->db_catalog->get_monedas_min();
		#OBTENEMOS EL CONSECUTIVO
		$folio = $this->db_com->get_ultimo_complemento();
		$dataView = array_merge($dataView, $folio);

		$this->parser_view('ventas/complementos-pago/tpl/modal-nuevo-complemento', $dataView, FALSE);
	}

	public function process_save_complemento() {
		try {
			$this->db->trans_begin();
			#GUARDAMOS complemento
			$sqlData = $this->input->post([
				'id_estatus_complemento',
				'fecha_complemento',
				'id_cliente',
				'id_factura',
				'importe_pago',
				'fecha_pago',
				'importe_restante',
				'id_moneda',
				'id_numero_parcialidad',
				'observaciones'
			]);
			
			$sqlData['observaciones'] 	= strtoupper($this->input->post('observaciones'));
			$insert = $this->db_com->insert_complemento($sqlData);
			$insert OR set_exception();

			$actividad 		= "ha creado un complemento de pago";
			$data_change 	= ['insert'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($insert, 'tbl_complementos_pago', $actividad, $data_change);

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

	public function get_modal_edit_complemento() {
		$dataView = $this->input->post();
		$dataEncription = json_encode($this->input->post(['id_complemento_pago']));
		$dataView['dataEncription'] = $this->encryption->encrypt($dataEncription);

		#CATALOGOS & SELECTS
		$sqlWhere['selected'] = $this->input->post('id_cliente');
		$dataView['clientes'] = $this->db_cliente->get_cliente_selected($sqlWhere);
		//Estatus complemento
		$sqlWhere['id_categoria'] = 77;
		$sqlWhere['selected'] = $this->input->post('id_estatus_complemento');
		$dataView['estatus'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Numero parcialidad
		$sqlWhere['id_categoria'] = 79;
		$sqlWhere['selected'] = $this->input->post('id_numero_parcialidad');
		$dataView['parcialidades'] = $this->db_vc->get_ventas_cotizacion_select($sqlWhere);
		//Monedas
		$sqlWhere['selected'] = $this->input->post('id_moneda');
		$dataView['monedas'] = $this->db_catalog->get_monedas_min($sqlWhere);
		//Facturación
		$sqlWhere['selected'] = $this->input->post('id_factura');
		$dataView['facturas'] = $this->db_fac->get_factura_select($sqlWhere);
		
		
		#ELIMINACIÓN DE CONFLICTOS EN EL PARSER VIEW
		unset($dataView['id_cliente'], $dataView['id_cliente']);
		unset($dataView['id_factura'], $dataView['id_factura']);
		unset($dataView['cliente'], $dataView['cliente']);
		unset($dataView['id_estatus_complemento'], $dataView['id_estatus_complemento']);
		unset($dataView['id_moneda'], $dataView['id_moneda']);
		unset($dataView['moneda'], $dataView['moneda']);

		$this->parser_view('ventas/complementos-pago/tpl/modal-editar-complemento', $dataView, FALSE);
	}

	public function process_update_complemento() {
		try {
			$this->db->trans_begin();
			$sqlData = $this->input->post([
				'id_complemento_pago',
				'id_estatus_complemento',
				'fecha_complemento',
				'id_cliente',
				'id_factura',
				'importe_pago',
				'fecha_pago',
				'importe_restante',
				'id_moneda',
				'id_numero_parcialidad',
				'observaciones'
			]);
			
			$sqlData['observaciones'] 	= strtoupper($this->input->post('observaciones'));

			$sqlWhere = $this->input->post(['id_complemento_pago']);
			$update = $this->db_com->update_complemento($sqlData, $sqlWhere);
			$update OR set_exception();

			/*DATA PARA EL PDF
			$dataView = $sqlData;
			$dataView['id_requisicion'] 				= $this->input->post('id_requisicion');
			$dataView['tipo_requisicion'] 				= $this->input->post('tipo_requisicion');
			$dataView['vale_entrada'] 					= $this->input->post('vale_entrada');
			$dataView['departamento_solicitante'] 		= $this->input->post('departamento_solicitante');
			$dataView['almacen_solicitante'] 			= $this->input->post('almacen_solicitante');
			$dataView['departamento_encargado_surtir'] 	= $this->input->post('departamento_encargado_surtir');*/

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

			$actividad 		= "ha editado un complemento de pago";
			$data_change 	= ['update'=>['newData'=>$sqlData]];
			registro_bitacora_actividades($sqlWhere['id_complemento_pago'], 'tbl_complementos_pago', $actividad, $data_change);

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

	public function process_remove_complemento() {
		try {
			$this->db->trans_begin();

			$sqlWhere 	= $this->input->post(['id_complemento_pago']);
			#ELIMIANCIÓN DE COMPLEMENTO
			$update = $this->db_com->update_complemento(['activo'=>0], $sqlWhere);
			$update OR set_exception();

			$actividad 		= "ha eliminado un complemento de pago";
			$data_change 	= ['delete'=>['oldData'=>$_POST]];
			registro_bitacora_actividades($sqlWhere['id_complemento_pago'], 'tbl_complementos_pago', $actividad, $data_change);
			
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