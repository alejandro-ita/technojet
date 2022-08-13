<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| verndor
|--------------------------------------------------------------------------
|
| Registro de las librerias JS que requiere el sistema para el correcto funcionamiento
|
*/
$vendor = [];

/***REGISTRO DEL CORE DEL TEMPLATE***/
$themeVersion='2.6.0';
$core_theme['headers']['css'][] = ['name'=>'dashlite', 'dirname'=> get_var('path_template')."/$themeVersion/css", 'fulldir'=> TRUE];
$core_theme['vendor']['js'][] = ['name'=>'bundle', 'dirname'=> get_var('path_template')."/$themeVersion/js", 'fulldir'=> TRUE];
$vendor = array_merge($vendor, ['core_theme'=>$core_theme]);

/***FULLCALENDAR***/
$fullcalendar['headers']['css'][] = ['name'=>'fullcalendar-5.11.0', 'dirname'=> get_var('path_vendor').'/fullcalendar', 'fulldir'=> TRUE];
$fullcalendar['vendor']['js'][] = ['name'=>'fullcalendar-5.11.0', 'dirname'=> get_var('path_vendor')."/fullcalendar", 'fulldir'=> TRUE];
$vendor = array_merge($vendor, ['fullcalendar'=>$fullcalendar]);

/***MOMENTJS***/
$moment['vendor']['js'][] = ['name'=>'moment.min', 'dirname'=> get_var('path_vendor').'/moment', 'fulldir'=> TRUE];
$moment['language']['js'][] = ['name'=>'moment', 'dirname'=> get_var('path_js').'/lang', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['moment'=>$moment]);

/***JS MD5***/
$md5['vendor']['js'][] = ['name'=>'jquery.md5.min', 'dirname'=> get_var('path_vendor').'/md5', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['md5'=>$md5]);

/***JQUERY VALIDATE***/
$jQValidate['language']['js'][] = ['name'=>'jq_validate', 'dirname'=> get_var('path_js').'/lang', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['jQValidate'=>$jQValidate]);

/***DATATABLE***/
$dataTables['language']['js'][] = ['name'=>'datatables', 'dirname'=> get_var('path_js').'/lang', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['dataTables'=>$dataTables]);

/***DATATABLE EXTENSIONs***/
$DTRowGroup['headers']['css'][] = ['name'=>'rowGroup.dataTables.min', 'dirname'=> get_var('path_vendor').'/datatables/extensions/rowgroup', 'fulldir'=> TRUE];
$DTRowGroup['vendor']['js'][] = ['name'=>'dataTables.rowGroup.min', 'dirname'=> get_var('path_vendor').'/datatables/extensions/rowgroup', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['DTRowGroup'=>$DTRowGroup]);

/***JS TREE BOOTSTRAP***/
$jstree['headers']['css'][] = ['name'=>'style.min', 'dirname'=> get_var('path_vendor').'/jstree-bootstrap/dist/themes/proton', 'fulldir'=> TRUE];
$jstree['vendor']['js'][] = ['name'=>'jstree.min', 'dirname'=> get_var('path_vendor').'/jstree-bootstrap/dist/', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['jstree'=>$jstree]);

/***DATE RANGE PICKER***/
$dateRangePicker['headers']['css'][] = ['name'=>'daterangepicker', 'dirname'=> get_var('path_vendor').'/date-range-picker/', 'fulldir'=> TRUE];
$dateRangePicker['vendor']['js'][] = ['name'=>'daterangepicker', 'dirname'=> get_var('path_vendor').'/date-range-picker/', 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['dateRangePicker'=>$dateRangePicker]);

/***CSS SKELETONS***/
$cssSkeletons['headers']['css'][] = ['name'=>'css-skeletons.min', 'dirname'=> get_var('path_vendor')."/css-skeletons", 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['css-skeletons'=>$cssSkeletons]);

/***REGISTRO DE LIBRERIAS JS SMART BUSSINER***/
$core_SB['headers']['css'][] = ['name'=>'preloader', 'dirname'=> get_var('path_css'), 'fulldir'=> TRUE];
$core_SB['headers']['css'][] = ['name'=>'sb', 'dirname'=> get_var('path_css'), 'fulldir'=> TRUE];
$core_SB['footer']['js'][] = ['name'=>'prototype-extends', 'dirname'=> get_var('path_js'), 'fulldir'=> TRUE];
$core_SB['footer']['js'][] = ['name'=>'system_helper', 'dirname'=> get_var('path_js'), 'fulldir'=> TRUE];
$core_SB['footer']['js'][] = ['name'=>'extends', 'dirname'=> get_var('path_js'), 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['core_SB'=>$core_SB]);

/***INICIALIZADOR DE LOS COMPONENTES***/
$components['bootstraps']['js'][] = ['name'=>'scripts', 'dirname'=> get_var('path_template')."/$themeVersion/js", 'fulldir'=> TRUE];
$components['bootstraps']['js'][] = ['name'=>'initializer', 'dirname'=> get_var('path_js'), 'fulldir'=> TRUE];
$vendor = array_merge_recursive($vendor, ['components'=>$components]);

/*echo "<pre>";
print_r($vendor);
exit();*/


$config['vendor'] = $vendor;