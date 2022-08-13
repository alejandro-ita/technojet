<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Read_excel extends PHPExcel { 
	public $str_compare;
	private $roles_validation;

	public function __construct() {
		parent::__construct();
		$this->roles_validation = array( 
			 'CONTAINS_TITLE' 	=> TRUE
			,'REMOVE_TITLE' 	=> TRUE
			,'TITLES' => array(
				'CLAVE CORPORATIVO', 			'ID_EMPRESA', 
				'ACRONIMO_EMPRESA', 			'EMPRESA',
				'ACRONIMO_SEDE', 				'SEDE', 
				'PERSON_ID', 					'NÚMERO DE EMPLEADO',
				'APELLIDO PATERNO',				'APELLIDO MATERNO'
			)
			,'REQUIRED' 		=> array('A', 'C', 'D', 'H')
			,'REQUIRED_NUMBERS' => array('V', 'Z', 'AK')
			,'ONLY_DIGITS' 		=> array('V', 'Z')
			,'FORMAT' 			=> array(
				'FORMAT_GENERAL' 			=> array('A', 'B', 'C', 'E', 'F') 
				,'FORMAT_NUMBER' 			=> array('D', 'I', 'J', 'P', 'R') //NUMEROS ENTEROS MAYORES A 0(CERO)
				,'FORMAT_DATE_YYYYMMDD2' 	=> array('N', 'AB', 'AL') 
				,'FORMAT_NUMBER_00' 		=> array('O', 'V', 'W', 'X', 'Y') //NUMEROS DECIMALE MAYORES A 0(CERO)
			)
			// ,'RANGE_COLS' => 'A1:Z' // ** RANGO DE LAS COLUMNA A CONSIDERAR PARA LA EXTRACCIÓN DE DATOS [EL NUMERO DEL FILAS ES CALCULABLE]**
		);
	}

	public function get_sheetsData(array $sheetsData, $rules=[]) {
		try {
			$response = [];
			foreach ($sheetsData['sheets'] as $sheetNumber=>$sheetData) {
				$sheetName = isset($sheetData['name']) ? $sheetData['name'] : "hoja{$sheetNumber}";
				$sheetRules= isset($sheetData['sheetRules']) ? $sheetData['sheetRules'] : [];
				$sheetData['path_file'] = $sheetsData['path_file'];
				$sheetData['sheetNumber'] = $sheetNumber;

				$response[$sheetName] = self::get_Data($sheetData, array_merge($rules, $sheetRules));
				!isset($response[$sheetName]['error']) OR set_exception(['message'=>$response[$sheetName]['msg'], 'typeMsg'=>'warning', 'saveTrace'=>FALSE]);
			}
		} catch (Exception  $e) {
			$response['error'] 		= TRUE;
			$response['msg'] 		= $e->getMessage();
		}

		return $response;
	}

	/**
	 * se crea un metodo para la validacion y la lectura de la carga del layout
	 * @param $data array datos del layout para la lectura
	 * @param $roles_validation array estructura para la validación del layout, 
	 *			si el parametro es FALSE, toma la validacion definida por default.
	 * @return $sheetData Array datos del layout en un arreglo bidimencional
	 */
	public function get_Data($data = array(), $roles_validation = FALSE) {
		try {
			$roles_validation = ($roles_validation !== FALSE ? $roles_validation : $this->roles_validation);
			$this->roles_validation = $roles_validation;
			$fileXLSX = (isset($data['path_file']) ? $data['path_file'] : '');
			$inputFileType 	= PHPExcel_IOFactory::identify($fileXLSX);
			$this->fileName = (isset($data['fileName'])?$data['fileName']:$inputFileType);

			if (isset($roles_validation['FORMAT'])) {
				$roles = $roles_validation['FORMAT'];
			}

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objectPhpExcel = $objReader->load($fileXLSX);
			$sheetNumber = (isset($data['sheetNumber']) ? $data['sheetNumber'] : 0);
			$objectPhpExcel->setActiveSheetIndex($sheetNumber); 
			//OBTENEMOS LOS FORMATOS PARA LA ASIGNACION A LAS COLUMNAS
			$FORMAT_GENERAL 		= PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
			$FORMAT_NUMBER  		= PHPExcel_Style_NumberFormat::FORMAT_NUMBER;
			$FORMAT_DATE_YYYYMMDD2 	= PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2;
			$FORMAT_NUMBER_00 		= PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00;
			$FORMAT_TIME24HRS 		= 'hh:mm:ss';
			$FORMAT_TIMESTAMP24HRS 	= 'yyyy-mm-dd hh:mm:ss';

			//VARIABLES
			$maxRow 				= $objectPhpExcel->getActiveSheet()->getHighestRow();
			$CONTAINS_TITLE 		= isset($roles_validation['CONTAINS_TITLE']) ? $roles_validation['CONTAINS_TITLE'] : FALSE;
			$REMOVE_TITLE 			= isset($roles_validation['REMOVE_TITLE']) ? $roles_validation['REMOVE_TITLE'] : FALSE;
			$row_format_start 		= $CONTAINS_TITLE ? 2 : 1;

			//SE AGREGA EL FORMATO PARA LAS COLUMAS OBTENIDAS
			if (isset($roles)) {
				foreach ($roles as $format => $rows) {
					foreach ($rows as $col) {
						$objectPhpExcel->getActiveSheet()
							->getStyle("{$col}{$row_format_start}:{$col}{$maxRow}")
						    ->getNumberFormat()
						    ->setFormatCode($$format);
					}
				}
			}

			if (isset($roles_validation['RANGE_COLS'])) {
				$sheetData = $objectPhpExcel->getActiveSheet()->rangeToArray($roles_validation['RANGE_COLS'] . $maxRow, NULL, TRUE, TRUE, TRUE);
			} else {
				$sheetData = $objectPhpExcel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);
			}

			//VALIDACIÓN DE LOS NOMBRES DE LAS COLUMNAS
			if (isset($roles_validation['TITLES'])) {
				self::validation_title($roles_validation['TITLES'], $sheetData[1]);
			}

			//DESCARTAMOS LOS TITULOS PARA LAS SIGUIENTES VALIDACIONES
			$TMP_DATA = $sheetData;
			if ($CONTAINS_TITLE) unset($TMP_DATA[1]);

			//VALIDACIÓN DE CAMPOS QUE PERTENESCAN AL MISMO GRUPO CORPORATIVO|EMPRESA
			if (isset($data['COL_MACH'])) {
				$this->str_compare = $data['MATCH_DATA'];
				self::validation_match_data($TMP_DATA, $data['COL_MACH']);
				//Si la vigencia es por grupo corporativo,
				//se valida que las empresas del layout pertnezcan a ese grupo
				if($data['COL_MACH'] == 'C') {
					self::validation_empresas_grupo($TMP_DATA, $data);
				}
			}

			//VALIDACIÓN DE LOS CAMPOS REQUERIDOS
			if (isset($roles_validation['REQUIRED'])) {
				self::validation_data_required($TMP_DATA, $roles_validation['REQUIRED']);
			}

			//VALIDACION DE CAMPOS NUMERICOS MAYORES A 0 
			if(isset($roles_validation['ONLY_DIGITS'])) {
				self::validation_data_numeric($TMP_DATA, $roles_validation['ONLY_DIGITS'], TRUE);
			}
			
			//VALIDACION DE CAMPOS NUMERICOS REQUERIDOS
			if (isset($roles_validation['REQUIRED_NUMBERS'])) {
				self::validation_data_numeric($TMP_DATA, $roles_validation['REQUIRED_NUMBERS']);
			}

			//VALIDACION DE CAMPOS TIPO FECHA
			if (isset($roles['FORMAT_DATE_YYYYMMDD2'])) {
				// self::convert_dateformat($TMP_DATA, $roles['FORMAT_DATE_YYYYMMDD2']);

				self::validation_data_date($TMP_DATA, $roles['FORMAT_DATE_YYYYMMDD2']);
			}

			//VALIDACION DE CAMPOS TIPO FECHA HORA
			if (isset($roles['FORMAT_TIMESTAMP24HRS'])) {
				self::convert_datetimeformat($TMP_DATA, $roles['FORMAT_TIMESTAMP24HRS']);
			}

			//QUITAMOS LA COLUMNA DE TITULOS
			// if ($REMOVE_TITLE) {
				// unset($sheetData[1]);
			// }
			// $response = $sheetData;	

			$response = array_values($TMP_DATA);	

		} catch (Exception  $e) {
			$response['error'] 		= TRUE;
			$response['msg'] 		= $e->getMessage();
		}

		return $response;
	}

	/**
	 * Función para realizar la validación de los títulos del layout
	 * @param $titles array nombre de los títulos del layout
	 * @param $titles_comparation array nombre de los títulos a comparar
	 * @return boolean TRUE en caso de ser igual, si no, FALSE
	 */
	protected function validation_title($titles, $titles_comparation) {
		$diff_title = array_diff($titles, $titles_comparation);
		if (count($diff_title) OR count($titles) != count($titles_comparation)) {
			$message_exception = str_replace('{file_name}', $this->fileName, lang('excel_diff_titles'))."<br>";
			$message_exception .= str_replace('{titles}', implode(', ', $diff_title), lang('excel_titles_no_accep'));
			throw new Exception($message_exception);
		}
		
		unset($diff_title);
		return TRUE;
	}

	/**
	 * Función para realizar la validación de datos que pertenescan a un solo grupo corporativo o a una sola empresa
	 * @param $data array datos a validar
	 * @param $col_name string nombre de la columa a realizar la busqueda de la comparación
	 * @return boolean TRUE en caso de ser igual, si no, FALSE
	 */
	protected function validation_match_data($data, $col_name) {
		$datos = array_column($data, $col_name);
		$datos = array_unique($datos);

		$no_match = array_filter($datos, "self::data_no_match");
		if (count($no_match)) {
			$message_exception = lang('excel_cell_error_match_'.$col_name);
			throw new Exception($message_exception);
		}

		unset($datos, $no_match);
		return TRUE;
	}

	/**
	 * Función para validar que las empresas pertenecen al grupo corporativo seleccionado
	 * @param  [array] $data                 datos a validar
	 * @param  int $id_grupo_corporativo id del grupo corporativo
	 * @return boolean                       
	 */
	protected function validation_empresas_grupo($data, $data_vigencia) {
		$data = array_column($data, 'D');
		$data = array_unique($data);

		$empresas_grupo = $data_vigencia['empresas_grupo']? array_unique(array_column($data_vigencia['empresas_grupo'], 'id_aon_flex')) : array();
		$no_match = array_diff($data, $empresas_grupo);
		
		if(count($no_match)) {
			throw new Exception(lang('excel_empresas_grupos_diff'));
		}

		unset($data, $no_match);
		return TRUE;
	}
	
	/**
	 * Función para realizar la validación de los campos requeridos del layout
	 * @param $data array datos a validar
	 * @param $cols_name_required array nombre de las columans a validar
	 * @return boolean TRUE en caso de ser igual, si no, FALSE
	 */
	protected function validation_data_required($data, $cols_name_required) {
		foreach ($cols_name_required as $col_name) {
			$datos = array_column($data, $col_name);
			$datos = array_unique($datos, SORT_REGULAR);
			$campos_vacios = array_filter($datos, "self::data_empty");
			if (count($campos_vacios)) {
				$message_exception = str_replace(['{col_name}', '{file_name}'], [$col_name, $this->fileName], lang('excel_cell_required'));
				throw new Exception($message_exception);
			}
			unset($datos, $campos_vacios);
		}
		
		return TRUE;
	}

	/**
	 * Función para realizar la validación de los campos numericos del layout
	 * @param $data array datos a validar
	 * @param $cols_number array nombre de las columans a validar
	 * @param $only_positive_numbers boolean TRUE or FALSE para la validación de solo numeros mayores o igual a 0 (Cero)
	 * @return boolean TRUE en caso de ser igual, si no, FALSE
	 */
	protected function validation_data_numeric($data, $cols_number, $only_positive_numbers = FALSE) {
		foreach ($cols_number as $col_name) {
			$datos = array_column($data, $col_name);
			$datos = array_unique($datos);

			$no_numericos = array_filter($datos, "self::data_no_numeric");
			if (count($no_numericos)) {
				$message_exception = str_replace(['{col_name}', '{file_name}'], [$col_name, $this->fileName], lang('excel_cell_numeric'));
				throw new Exception($message_exception);
			}

			//VALIDACIÓN SOLO NUMEROS ENTEROS POSITIVOS
			if($only_positive_numbers) {
				$numeros_negativos = array_filter($datos, "self::data_negative_number");
				if (count($numeros_negativos)) {
					$message_exception = str_replace(['{col_name}', '{file_name}'], [$col_name, $this->fileName], lang('excel_cell_numeric_negative'));
					throw new Exception($message_exception);
				}
				unset($numeros_negativos);
			}
			unset($datos, $no_numericos);
		}

		return TRUE;
	}

	/**
	 * Función para realizar la validación de los campos tipo fecha del layout
	 * @param $data array datos a validar
	 * @param $cols_date array nombre de las columans a validar
	 * @return boolean TRUE en caso de ser igual, si no, FALSE
	 */
	protected function validation_data_date($data, $cols_date) {
		foreach ($cols_date as $col_name) {
			$datos = array_column($data, $col_name);
			$datos = array_unique($datos);

			$no_fechas = array_filter($datos, "self::data_no_date");
			if (count($no_fechas)) {
				$message_exception = str_replace(['{col_name}', '{file_name}'], [$col_name, $this->fileName], lang('excel_cell_date'));
				throw new Exception($message_exception);
			}
			unset($datos, $no_fechas);
		}

		return TRUE;
	}

	/**
	 * Función para verificar si el valor(parametro) está vacío
	 * @param $data dato a validar
	 * @return boolean TRUE si está vacío, si no, FALSE
	 */
	protected function data_empty($data) {
		return (trim($data) == '');
	}

	/**
	 * Functión para verificar si los valores son diferentes
	 * @param $data dato a comparar
	 * @return boolean TRUE si es diferente, si no, FALSE
	 */
	protected function data_no_match($data) {
		$data = trim($data);
		return ($data!=$this->str_compare);
	}

	/**
	 * Función para verificar si el valor(parametro) es un número
	 * @param $data dato a validar
	 * @return boolean TRUE si es número, si no, FALSE
	 */
	protected function data_no_numeric($data) {
		return !is_numeric($data);
	}

	/**
	 * Función para verificar si el valor(parametro) es un número negativo
	 * @param $number número a validar
	 * @return boolean TRUE si es menor a 0(cero), si no, FALSE
	 */
	protected function data_negative_number($number) {
		return ($number<0);
	}

	/**
	 * Función para verificar si el valor(parametro) no es una fecha
	 * @param $data dato a validar
	 * @return boolean TRUE si no es fecha, si no, FALSE
	 */
	protected function data_no_date($data) {
		$data = str_replace(array(' ', '/'), '-', $data);
		$separator_date = substr_count( $data, '-');
		if ($separator_date != 2 OR strlen($data) != 10) {
			return TRUE;
		}

		list($anio, $mes, $dia) = explode('-', $data);
		return !checkdate($mes, $dia, $anio);
	}

	/**
	 * Función para CASTEAR la columna fecha
	 * @param $data dato a validar
	 * @return VOID
	 */
	protected function convert_dateformat(&$data, $cols_date) {
		$INPUT_DATE_FORMAT = trim($this->roles_validation['INPUT_DATE_FORMAT']);
		foreach ($cols_date as $col_name) {
			foreach ($data as $index => &$row) {
				$date = $row[$col_name];
				if (DateTime::createFromFormat('Y-m-d', $date) === FALSE AND $INPUT_DATE_FORMAT) {
					$fecha = DateTime::createFromFormat($INPUT_DATE_FORMAT, $date);
					if (!$fecha) {
						$message_exception = str_replace('%col_name%', $col_name, lang('excel_cell_date'));
						throw new Exception($message_exception);
					}

					$row[$col_name] = $fecha->format('Y-m-d');
				}			
			}
		}

		return TRUE;
	}

	/**
	 * Función para CASTEAR la columna fecha Hora
	 * @param $data dato a validar
	 * @return VOID
	 */
	protected function convert_datetimeformat(&$data, $cols_date) {
		$INPUT_TIMESTAMP_FORMAT = trim($this->roles_validation['INPUT_TIMESTAMP_FORMAT']);
		foreach ($cols_date as $col_name) {
			foreach ($data as &$row) {
				$timestamp = $row[$col_name];
				if (!DateTime::createFromFormat('Y-m-d H:i:s', $timestamp) AND $INPUT_TIMESTAMP_FORMAT) {
					$fecha = DateTime::createFromFormat($INPUT_TIMESTAMP_FORMAT, $timestamp);
					if (!$fecha) {
						$message_exception = str_replace('%col_name%', $col_name, lang('excel_cell_date'). "$timestamp");
						throw new Exception($message_exception);
					}

					$row[$col_name] = $fecha->format('Y-m-d H:i.s');
				} else {
					$message_exception = str_replace('%col_name%', $col_name, lang('excel_cell_date'). "$timestamp");
					throw new Exception($message_exception);
				}				
			}
		}

		return TRUE;
	}
}

/* End of file readExcel.php */
/* Location: ./application/libraries/readExcel.php */
