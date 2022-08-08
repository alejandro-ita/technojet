<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Create_pdf {

	public function __construct() {}

	public function create_file($settings=array()) {
		ini_set('max_execution_time', 0);
		$options 	= new Dompdf\Options();
		// $canvas 	= new Dompdf\Canvas();
		$options->set('isRemoteEnabled', TRUE);
		$options->set('isJavascriptEnabled', TRUE);
		$options->set('isHtml5ParserEnabled', TRUE);
		$dompdf 		= new Dompdf\Dompdf($options);

		$FILE_NAME 		= isset($settings['file_name']) 	? "$settings[file_name].pdf": 'test_Smart_Bussines.pdf';
		$SIZE 			= isset($settings['size']) 			? $settings['size'] 		: 'letter'; 		//letter|legal|A4...
		$ORIENTATION 	= isset($settings['orientation']) 	? $settings['orientation'] 	: 'portrait'; 		//portrait|landscape
		$FILE_PATH 		= isset($settings['file_path'])  	? $settings['file_path'] 	: get_var('path_tmp');
		$LOAD_FILE 		= isset($settings['load_file']) 	? $settings['load_file'] 	: FALSE;
		$CONTENT_FILE 	= isset($settings['content_file']) 	? $settings['content_file'] : '';
		$ADD_PAGINATED 	= isset($settings['add_paginated']) ? $settings['add_paginated']: FALSE;

		$dompdf->set_paper($SIZE, $ORIENTATION);
		$dompdf->load_html($CONTENT_FILE, 'UTF-8');
		$dompdf->render();

		if ($ADD_PAGINATED) {
			$dompdf->get_canvas()->page_text(560, 50, "{PAGE_NUM}/{PAGE_COUNT}", '', 12, array(0,0,0));
		}

		//RETORNAMOS LA RUTA DEL ARCHIVO CREADO
		if (!$LOAD_FILE) {
			$FILE 		= $dompdf->output();
			$FILE_PATH 	.= "/$FILE_NAME";
			file_put_contents($FILE_PATH, $FILE);

			return $FILE_PATH;
		} 
		
		//CARGAMOS EL ARCHIVO AL NAVEGADOR
		$dompdf->stream($FILE_NAME, array('Attachment'=>0));
	}
}

/* End of file Dompdf.php */
/* Location: ./application/libraries/Dompdf.php */