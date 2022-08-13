<?php if (!defined( 'BASEPATH')) exit('No direct script access allowed'); 
class Load_language {

    public $lang_files = array(
             'db'
            ,'mail'
            ,'menu'
            ,'vales'
            ,'error'
            ,'login'
            ,'excel'
            ,'upload'
            ,'library'
            ,'general'
            ,'almacen'
            ,'usuarios'
            ,'validate'
            ,'reportes'
            ,'catalogos'
    );

    function initialize() {
        $CI         =& get_instance();
        $lang_folder= config_item('language');
        $CI->config->set_item('language', $lang_folder);
        
        #SE REALIZA LA CARGA POR ARCHIVO PARA PODER ESPECIFICAR LA CARGAR DE LOS CONTENIDOS DEL ARCHIVO EN LA VISTA
        $CI->lang->filesContent = array();
        foreach ($this->lang_files as $file) {
            $lang = $CI->lang->load($file, $lang_folder, TRUE);
            $CI->lang->fileContent[$file] = $lang; #ESPECIFICAMOS LOS ARCHIVOS A CARGAR EN LA VISTA DESDE ELON

            $CI->lang->is_loaded[$file] = $lang_folder;
            $CI->lang->language = array_merge($CI->lang->language, $lang);
        }
    }
}