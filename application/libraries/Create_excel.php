<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Create_excel {

	private function setupFile($setting) {
		$title 			= isset($setting['title']) 			? $setting['title'] 		: 'Archivo';
		$subject 		= isset($setting['subject']) 		? $setting['subject'] 		: '';
		$description	= isset($setting['description'])	? $setting['description'] 	: '';
		$TEMPLATE 	 	= isset($setting['template']) 		? $setting['template'] 		: FALSE;
		$spreadsheet 	=  $TEMPLATE ? \PhpOffice\PhpSpreadsheet\IOFactory::load($TEMPLATE) : new Spreadsheet();
		
		#Set document properties
		$spreadsheet->getProperties()->setCreator('Creado por '. get_var('site_name'))
									 ->setLastModifiedBy("")
									 ->setTitle($title)
									 ->setSubject($subject)
									 ->setDescription($description);
		return $spreadsheet;
	}

	private function setContent(&$spreadsheet, $setting) {
		$HEADERS 			= isset($setting['headers']) 			? $setting['headers'] 			 : FALSE;
		$REPORT_INFORMATION = isset($setting['report_information']) ? $setting['report_information'] : array();
		$DATA   			= isset($setting['data']) 				? $setting['data'] 				 : array();
		$COMBINAR   		= isset($setting['combinar']) 			? $setting['combinar'] 			 : array();
		$STYLECELLS   		= isset($setting['styleCells']) 		? $setting['styleCells'] 		 : array();
		$ADDFORMAT   		= isset($setting['addFormat']) 			? $setting['addFormat'] 		 : array(); 
		$ADDVALUESEXPLICIT  = isset($setting['addValuesExplicit']) 	? $setting['addValuesExplicit']  : array(); 
		$spreadsheet->getActiveSheet()->setTitle($setting['sheet_name']);
		//SET HEADER
		$HEADERS AND $spreadsheet->getActiveSheet()->fromArray($HEADERS['data'], null, $HEADERS['cell']);

		if ($COMBINAR) {
			foreach ($COMBINAR as $celdas) {
				$spreadsheet->getActiveSheet()->mergeCells($celdas);
			}
		}

		if ($STYLECELLS) {
			foreach ($STYLECELLS as $celdas=>$style) {
				$spreadsheet->getActiveSheet()->getStyle($celdas)->applyFromArray($style);
			}
		}
		
		//SET INFORMATION REPORT
  		foreach ($REPORT_INFORMATION as $info) {
      		$spreadsheet->getActiveSheet()->setCellValue($info['cell'], $info['text']);
  		}

  		//INSERTAMOS LOS DATOS EN LAS CELDAS
  		if (isset($DATA['data']))
  			$spreadsheet->getActiveSheet()->fromArray($DATA['data'], null, $DATA['cell'], TRUE);
  		
	    if (isset($setting['autoSize'])) {
			foreach ($setting['autoSize'] as $cell) {
				$spreadsheet->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
			}
		}

		//AGREGAMOS ESTILO Y DISEÑO A LA HOJA
		if (isset($setting['font'])) {
			$spreadsheet->getActiveSheet()->getStyle($setting['font']['cells'])->applyFromArray($setting['font']);
		}

		#AGREGAMOS FORMATO AL CONTENIDO DE LA HOJA
		#VER FORMATOS VALIDOS http://www.cmth.ph.ic.ac.uk/people/a.mackinnon/php/PHPExcel/Documentation/API/PHPExcel_Style/PHPExcel_Style_NumberFormat.html
		foreach ($ADDFORMAT as $format) {
			$spreadsheet->getActiveSheet()->getStyle($format['cells'])->getNumberFormat()->setFormatCode($format['code']);
		}

		#PARSEAMOS LOS VALORES EXPLICITOS
		#VER FORMATOS VALIDOS http://www.osakac.ac.jp/labs/koeda/tmp/phpexcel/Documentation/API/PHPExcel_Cell/PHPExcel_Cell_DataType.html
		foreach ($ADDVALUESEXPLICIT as $dataValueExplicit) {
			self::setValuesExplicit($spreadsheet, $dataValueExplicit);
		}

		return $spreadsheet;
	}

	/**
	 * Función para la creación de archivos de Excel 
	 * con opcion para guardarlo en la carpeta temporal
	 */
	public function build_file(array $setting=[], $return_path=TRUE) {
		ini_set('max_execution_time', 0);
		$content_type 		= isset($setting['content_type']) 		? $setting['content_type'] 	: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		$filename 			= isset($setting['filename']) 			? $setting['filename'] 		: 'Excel';
		$extension 			= isset($setting['extension']) 			? $setting['extension'] 	: 'xlsx';
		$version 			= $extension == 'xls' 					? 'Excel5'					: 'Excel2007';
		$sheet_name 		= isset($setting['sheet_name'])			? $setting['sheet_name'] 	: 'hoja1';
		$spreadsheet 		= self::setupFile($setting);

		//ACTIVAMOS LA HOJA1
		$spreadsheet->setActiveSheetIndex(0);
		$setting['sheet_name'] = $sheet_name;
		self::setContent($spreadsheet, $setting);
		//GUARDAMOS EL ARCHIVO EN LA CARPETA TEMPORAL Y RETORNAMOS LA RUTA DEL ARCHIVO
		if ($return_path) {
			$dir_tmp 	= isset($setting['directory']) ? $setting['directory'] : get_var('path_tmp');
			$pathfile  	= "{$dir_tmp}/{$filename}.{$extension}";

			switch ($extension) {
				case 'xlsx': $objWriter = new Xlsx($spreadsheet, $version); break;
				case 'csv': $objWriter = new Csv($spreadsheet); break;
			}
			$objWriter->save(LOCALPATH.$pathfile);

			return $pathfile;

			/*$pathfile = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, base64_decode(get_var('download_key')), $pathfile, MCRYPT_MODE_ECB)));
			return 'download-file?file='.$pathfile;*/
		}

		//DESCARGA DEL ARCHIVO DESDE EL NAVEGADOR
		header("Content-Type: $content_type");
		header("Content-Disposition: attachment;filename=$filename.$extension");
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		$writer = new Xlsx($spreadsheet, $version);
		$writer->save('php://output');
		exit;
	}

	public function download_file_multiple_sheets(array $settings=[], $return_path=TRUE) {
		ini_set('max_execution_time', 0);
		$content_type 		= isset($settings['content_type']) 		? $settings['content_type'] 	: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		$filename 			= isset($settings['filename']) 			? $settings['filename'] 		: 'Reporte';
		$extension 			= isset($settings['extension']) 		? $settings['extension'] 		: 'xlsx';
		$version 			= $extension == 'xls' 					? 'Excel5'						: 'Excel2007';
		$objPHPExcel 		= self::setupFile($settings);

		$sheet = 0;
		foreach ($settings['sheets'] as $setting) {
			$setting['sheet_name'] = isset($setting['sheet_name'])	? $setting['sheet_name'] 	: 'hoja'.($sheet+1);
			self::activeSheet($objPHPExcel, ($sheet++));
			self::setContent($objPHPExcel, $setting);
		}
		$objPHPExcel->setActiveSheetIndex(0);

		//GUARDAMOS EL ARCHIVO EN LA CARPETA TEMPORAL Y RETORNAMOS LA RUTA DEL ARCHIVO
		if ($return_path) {
			$dir_tmp 	= isset($setting['directory']) ? $setting['directory'] : get_var('path_tmp');
			$pathfile  	= "{$dir_tmp}/{$filename}.{$extension}";
			$objWriter 	= PHPExcel_IOFactory::createWriter($objPHPExcel, $version);
			$objWriter->save(LOCALPATH.$pathfile);

			return $pathfile;
		}

		//DESCARGA DEL ARCHIVO DESDE EL BROWSER
		header("Content-Type: $content_type");
		header("Content-Disposition: attachment;filename=$filename.$extension");
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $version);
		$objWriter->save('php://output');
		exit;
	}

	public function activeSheet(&$objPHPExcel, $hoja) {
		try {
			$objPHPExcel->getSheet($hoja);
			$objPHPExcel->setActiveSheetIndex($hoja);
		} catch(Exception $e){
		    $objPHPExcel->createSheet($hoja);
			$objPHPExcel->setActiveSheetIndex($hoja);
		}

		return $objPHPExcel;
	}

	/**
	 * Establecer explícitamente el tipo de datos de un rango de celdas como texto para valores numéricos, etc
	 * @param Object &$objPHPExcel      PHPExcel
	 * @param array  $dataValueExplicit datos para realizar el parseo
	 *
	 * @example llamada de la funcion:
	 * $dataValueExplicit=[
	 * 	['cells'=> 'A2:C5', 'dataType'=>PHPExcel_Cell_DataType::TYPE_STRING],
	 * 	['cells'=> 'D2:D5', 'dataType'=>PHPExcel_Cell_DataType::TYPE_NUMERIC]
	 * ]
	 * 
	 * self::setValuesExplicit($objPHPExcel, $dataValueExplicit);
	 */
	public function setValuesExplicit(&$objPHPExcel, array $dataValueExplicit) {
		$sheetData = $objPHPExcel->getActiveSheet()->rangeToArray($dataValueExplicit['cells'], NULL, TRUE, TRUE, TRUE);
		foreach ($sheetData as $row=>$data) {
			foreach ($data as $col=>$value) {
				$cell = $col.$row;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $value, $dataValueExplicit['dataType']);
			}
		}

		return $objPHPExcel;
	}

}

/* End of file Excel.php */
/* Location: ./application/libraries/Excel.php */