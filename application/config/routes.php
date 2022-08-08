<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'technojet/login/index';
$route['login'] 			 = 'technojet/login/index';
$route['logout'] 			 = 'technojet/login/logout';
$route['login/auth'] 	 	 = 'technojet/login/auth';
$route['reset-password/(:any)'] = 'technojet/login/reset_password/$1';
$route['process_change_password'] = 'technojet/login/process_change_password';
$route['404_override'] 		 = 'errors/error404';
$route['error-ie'] 			 = 'errors/error_ie';
$route['translate_uri_dashes'] = FALSE;

$route['dashboard'] 				= 'technojet/dashboard';
$route['dashboard/tareas'] 			= 'technojet/tareas/task';

#ADMINISTRACION
$route['administracion/registro-actividades'] 	= 'administracion/registro_actividades';
$route['sistemas/reporte'] 						= 'administracion/reporte_sistemas';
$route['configuracion/cuenta'] 					= 'technojet/administracion/cuenta';
$route['database/almacen/vales-entrada'] 		= 'database/almacen/vales_entrada';
$route['database/almacen/vales-salida'] 		= 'database/almacen/vales_salida';

#ALMACEN
$route['almacen/requisicion-material'] 			= 'almacen/requisicion_material';


$route['ventas/cotizaciones'] 			= 'ventas/ventas/cotizaciones';
$route['ventas/pedidos-internos/factura'] 			= 'ventas/ventas/factura';
$route['ventas/pedidos-internos/mostrador'] 			= 'ventas/ventas/mostrador';