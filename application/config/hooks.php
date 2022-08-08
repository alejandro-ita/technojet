<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

#AUTORIZACION DE ACCESO A URL
$hook['pre_controller'][] = array(
     'class'    => 'User_access_validate'
    ,'function' => 'check_authorized_sites'
    ,'filename' => 'User_access_validate.php'
    ,'filepath' => 'hooks'
);

#CARGA DEL LENGUAJE DEL SISTEMA
$hook['pre_controller'][] = array(
     'class'    => 'Load_language'
    ,'function' => 'initialize'
    ,'filename' => 'Load_language.php'
    ,'filepath' => 'hooks'
);

#VALIDACIÓN DE PETICIONES AJAX
$hook['post_controller_constructor'][] = array(
     'class'    => 'User_access_validate'
    ,'function' => 'check_illegal_ajax'
    ,'filename' => 'User_access_validate.php'
    ,'filepath' => 'hooks'
);

#LIMPIA LOS ARCHIVOS TEMPORALES EN LA CARPETA assets/tmp
$hook['post_controller_constructor'][] = array(
     'class'    => 'cleaners'
    ,'function' => 'clean_all'
    ,'filename' => 'garbage_collector.php'
    ,'filepath' => 'hooks'
);