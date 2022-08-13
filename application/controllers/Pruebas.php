<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pruebas extends SB_Controller {

	public function __construct() {
		parent::__construct();
		//Do your magic here
	}

	public function index() {
		echo "<ul>
			<li><a href='".base_url('pruebas/email')."' target='_blank'>Enviar Correo</a></li>
			<li><a href='".base_url('pruebas/excel')."' target='_blank'>Generar Archivo Excel</a></li>
			<li><a href='".base_url('pruebas/pdf')."' target='_blank'>Generar Archivo PDF</a></li>
		</ul>";
	}

	/**
	 * Prueba de envío de correo
	 * @return [type] [description]
	 */
	public function email() {
		$this->load->library('Mail');
		$data = array(
			 'email-body' 	=> $this->parser_view("email/prueba")
			,'asunto' 	=> 'Prueba Salida de correo'
			,'para' 	=> 'telpokatzin@outlook.com'
		);
	
		// Send email
		$resultado = modules::run('technojet/correos/send_email', $data);
		if($resultado['success']){
			$msj = "Correo enviado OK: ".date("Y-m-d H:i:s") . '<br>'.$resultado['msg']."<br>Envíado a: $data[para]";
		}else{
			$msj = "ERROR: No se pudo enviar el correo: <br>".$resultado['msg'];
		}

		echo $msj;
	}

	/**
	 * Prueba de generador de archivo excel
	 * @return [type] [description]
	 */
	public function excel() {
		$this->load->library('Create_excel');
		$setting = array(
			 'filename' 			=> 'Smart_Bussines_'.date('YmdHis')
			,'report_information' 	=> array(
				 array('cell'=> 'A1',	'text' => date('Y-m-d H:i:s'))
				,array('cell'=> 'B1', 	'text' => 1)
				,array('cell'=> 'C1', 	'text' => 'UNO')
				,array('cell'=> 'D1', 	'text' => 2)
				,array('cell'=> 'E1', 	'text' => 'DOS')
			)
			,'data'=>['data'=>[[1,2,3]], 'cell'=> 'A2']
			// ,'return_file_path' => TRUE
		);

		// echo "<pre>";
		// print_r($setting);
		$this->create_excel->build_file($setting, FALSE);
		echo "Archivo Excel generado: ".date("Y-m-d H:i:s");
	}

	public function pdf() {
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Smart_Bussines_'.date('YmdHis')
			,'content_file' => 'Archivo generado desde el sistema de <b>'.get_var('site_name').'</b>: '. date('Y-m-d H:i:s')
			,'load_file' 	=> TRUE
		);
		$file_path  = $this->create_pdf->create_file($settings);
		echo "Archivo PDF generado: ".date("Y-m-d H:i:s");
	}

	public function excel_to_pdf() {
		$this->load->library('Xlsx_converter');
		$this->xlsx_converter->to_pdf();
	}

	public function vale_salida() {
		$this->load->library('Create_pdf');
		$settings = array(
			 'file_name' 	=> 'Smart_Bussines_'.date('YmdHis')
			,'content_file' => $this->parser_view('almacen/almacenes/tpl/tpl-pdf-vale-salida', [])
			,'load_file' 	=> TRUE
			,'orientation' 	=> 'landscape'
		);
		$file_path  = $this->create_pdf->create_file($settings);
	}
}

/* End of file Pruebas.php */
/* Location: ./application/controllers/Pruebas.php */