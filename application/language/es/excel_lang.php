<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

//Validación de la lectura de Excel
$lang['excel_diff_titles'] 			= 'Se encontraron diferencia en los títulos del archivo {file_name}.';
$lang['excel_titles_no_accep'] 		= 'Títulos no aceptados: {titles}.';
$lang['excel_cell_required'] 		= 'Se encontró al menos un campo vacío en la columna {col_name} del archivo {file_name}, por favor verifique los datos e intentalo nuevamente.';
$lang['excel_cell_numeric'] 		= 'Se encontró al menos un campo no númerico en la columna {col_name} del archivo {file_name}, por favor verifique los datos e intentalo nuevamente.';
$lang['excel_cell_numeric_negative']= 'Se encontró al menos un campo negativo en la columna {col_name} del archivo {file_name}, por favor verifique los datos e intentalo nuevamente.';
$lang['excel_cell_date'] 			= 'Se encontró al menos un campo que no es fecha valida en la columna {col_name} del archivo {file_name}, por favor verifique los datos e intentalo nuevamente.';
$lang['excel_cell_error_match_C'] 	= 'Se encontraron registros que no pertenece al grupo corporativo elegido, verifica los datos del layout e intenta nuevamente.';
$lang['excel_cell_error_match_D'] 	= 'Se encontraron registros que no pertenece a la empresa elegido, verifica los datos del layout e intenta nuevamente.';
$lang['excel_download_file'] 		= 'Descargar layout carga inicial.';
$lang['excel_empresas_grupos_diff'] = 'Se encontraron empresas que no pertenecen al grupo corporativo elegido, por favor verifique los datos e intentalo nuevamente';
$lang['excel_ver_configuracion'] 	= 'Ver Configuración';
$lang['excel_archivo_incidencia_nv']= 'No se pudo identificar que tipo de incidencia para el archivo {file_name}, favor de verificar el contenido.';
$lang['excel_archivo_no_valido'] 	= 'El archivo {file_name} no es válido';
$lang['excel_nomenclatura_no_definido'] = 'La nomenclatura "{nomenclatura}" no está definido en el archivo {file_name}, favor de verificar el contenido.';
$lang['excel_empty_file'] 			= 'No se encontrador datos en el archivo';
$lang['excel_data_no_valid'] 		= 'El valor {data_cell} no es válido en el archivo {file_name}, por favor verifique los datos e intentalo nuevamente.';
$lang['lang_campo_requerido_excel'] = 'La columna "%col_name%" no puede ir vacía.';
$lang['lang_campo_registrado_excel'] = 'El valor en la columna "%col_name%" ya se encuentra registrado. <br> %campo%';
$lang['lang_campos_repeat_excel'] = 'Los valores de la columna "%col_name%" están repetidos. <br> %campo%';
$lang['lang_campo_no_email_excel'] = 'El valor en la columna "%col_name%" no tiene formato de email. <br> %campo%';