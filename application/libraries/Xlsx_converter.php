<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Xlsx_converter {

	public function __construct() {
	}

	public function to_pdf() {
		$fileTemplate 	= LOCALPATH.get_var('path_docs').'/vales/vale_entrada.xlsx';
		$dirTMP 		= LOCALPATH.get_var('path_tmp');

		$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		#$render->setReadDataOnly(true);
		$spreadsheet = $render->load($fileTemplate);
		$worksheet = $spreadsheet->getActiveSheet();

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
		$writer->writeAllSheets();
		$writer->save("$dirTMP/vale_entrada.pdf");
	}
}

/* End of file Xlsx_converter.php */
/* Location: ./application/libraries/Xlsx_converter.php */
