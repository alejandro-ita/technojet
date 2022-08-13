<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SB_Controller extends MX_Controller
{

    public function __construct()
    {
        #BANDERA QUE SE OCUPARA PARA LAS RESPUESTAS DE LAS API REST[SB_Rest.php]
        $_POST['isWeb'] = TRUE;
        parent::__construct();

        /** Validamos la autenticacion del usuario **/
        authentication_validate();

        self::encryption_initialize();
        !isset($_POST['dataEncription']) or $this->decripterData();

        $isLogged = $this->session->userdata('isLogged');
        if (get_var('log_access') && $isLogged) {
            LogTxt($this->session->get_userdata());
        }
    }

    private function encryption_initialize()
    {
        $this->encryption->initialize(
            array(
                'cipher'   => 'aes-256', 'mode'     => 'cbc', 'key'      => bin2hex(get_var('custom_key_IS'))
            )
        );
    }

    /**
     * Descencriptamos los datos y lo pasamos al $_POST
     * @return Void String
     */
    protected function decripterData($token = NULL, $return = FALSE)
    {
        $token = $token ? $token : $this->input->post('dataEncription');

        if (!$token) return;

        $JSONString = $this->encryption->decrypt($token);
        $data = json_decode($JSONString, TRUE);
        unset($_POST['dataEncription']);

        if (!is_array($data)) return FALSE;
        if ($return) return $data;

        foreach ($data as $key => $value) {
            $_POST[$key] = $value;
        }

        return TRUE;
    }

    /**
     * Unifica las vistas header & footer con las vistas parseadas
     * de la seccion seleccionada
     * @param string $view
     * @param array $data
     * @param array $includes Incluir archivos al sistema JS|CSS ...
     * @param string $ext
     * @return void
     */
    public function load_view($view = '', $data = [], $includes = [], $folder = 'tpl-main', $ext = '.html')
    {
        $ext = $ext != '.html' ? '' : $ext;

        #OBTENEMOS EL CORE DEL TEMPLATE
        if (!$includes or ($includes and !isset($includes['headers']))) {
            $vendor = get_includes_vendor();
            $includes = array_merge_recursive($vendor, $includes);
        }
        $dataPage = array_merge(['include-modulo' => '', 'include-language' => ''], $this->prepare_includes_vendor($includes));

        //PARSER PARTES DE LA PAGINA
        $URL_ACCESS  = self::get_url_user_access();
        $dataMenu['menu-left']          = $this->get_user_menu();
        $dataPage['page-left-sidebar']  = $this->parser_view("$folder/page-left-sidebar-view", $dataMenu);
        $dataPage['page-footer']    = $this->parser_view("$folder/page-footer-view", $URL_ACCESS);
        $dataPage['meta-view']      = $this->parser_view("$folder/meta-view", $URL_ACCESS);
        $dataPage['page-preloader'] = $this->parser_view("tpl-main/page-preloader");
        $dataPage['page-header']    = $this->parser_view("$folder/page-header-view");
        $dataPage['page-content']   = '';

        //PARSER CONTENIDO DE LA PAGINA
        if ($view) {
            $dataview                   = array_merge($data, $URL_ACCESS);
            $dataview['URL_ACCESS']     = json_encode($URL_ACCESS);
            #$dataview['SYSTEM_LANG']    = json_encode(get_system_lang());
            $pageContent['content']     = $this->parser_view($view, $dataview);
            $dataPage['page-content']   = $this->parser_view("$folder/page-content-view", $pageContent);
        }

        $dataPage['system-config']  = json_encode(get_var(FALSE, [], 'config'), JSON_HEX_APOS);
        $this->parser_view("$folder/main-view", $dataPage, FALSE);
    }

    /**
     * parseamos la vista HTML y retorna el resultado
     * @param string $view
     * @param array $data
     * @param boolean $autoload
     * @param array $includes Incluir archivos al sistema JS|CSS ...
     * @param string $ext
     * @return void
     */
    public function parser_view($view, $data = [], $return = TRUE, $includes = [], $ext = '.html')
    {
        $ext      = ($ext != '.html') ? '' : $ext;
        $includes = $this->load_scripts($includes);
        // self::load_Lang_Files($data);

        $data['js']     = $includes['js'];
        $data['css']    = $includes['css'];
        $template = $this->parser->parse($view . $ext, $data, TRUE);

        if ($return) return $template;

        echo $template;
    }

    protected function prepare_includes_vendor(array $includes = [])
    {
        $response = [];
        foreach ($includes as $seccion => $files) {
            $includesFiles = $this->load_scripts($files);
            $css    = isset($includesFiles['css']) ? $includesFiles['css'] : '';
            $js     = isset($includesFiles['js']) ? $includesFiles['js'] : '';
            $response["include-{$seccion}"] = "$css \n $js";
        }

        return $response;
    }

    /**
     * Carga archivos js & css en el header
     * @param array $data
     * @return array
     */
    protected function load_scripts(array $data)
    {
        $MODULO         = config_item('modulo');
        $CMODULO        = strtr(strtolower(sanitizar_string($MODULO)), array(' ' => '_'));
        $JS_PATH_MODULO = rtrim(get_var("path_js_$CMODULO", get_var('path_js')), '/');
        $CSS_PATH_MODULO = rtrim(get_var("path_css_$CMODULO", get_var('path_css')), '/');
        $version        = get_var('version') . '-' . rand();
        $js             = '';
        $css            = '';

        //CARGA DE ARCHIVO JS
        if (isset($data['js']) and is_array($data['js'])) {
            foreach ($data['js'] as $fileData) {
                $filename = isset($fileData['name']) ? trim($fileData['name'], '/') : '';

                if (isset($fileData['fulldir']) and $fileData['fulldir']) {
                    $filePath = rtrim(base_url(rtrim($fileData['dirname'], '/')), '/');
                } else {
                    $filePath = isset($fileData['dirname']) ? rtrim($fileData['dirname'], '/') : '';
                    $filePath = rtrim(base_url("$JS_PATH_MODULO/$filePath"), '/');
                }

                $js .= "<script type='text/javascript' src='$filePath/$filename.js?v=$version'></script>\n";
            }
        }

        //CARGA DE ARCHIVO CSS
        if (isset($data['css']) and is_array($data['css'])) {
            foreach ($data['css'] as $fileData) {
                $filename = isset($fileData['name']) ? trim($fileData['name'], '/') : '';

                if (isset($fileData['fulldir']) and $fileData['fulldir']) {
                    $filePath = rtrim(base_url(rtrim($fileData['dirname'], '/')), '/');
                } else {
                    $filePath = isset($fileData['dirname']) ? rtrim($fileData['dirname'], '/') : '';
                    $filePath = rtrim(base_url("$CSS_PATH_MODULO/$filePath"), '/');
                }

                $js .= "<link rel='stylesheet' type='text/css' href='$filePath/$filename.css?v=$version'/>\n";
            }
        }

        return ['js' => $js, 'css' => $css];
    }

    protected function get_url_user_access()
    {
        $response = array();

        if (!$this->userAccess) {
            #$sqlWhere = ['id_global_key'  => $this->session->userdata('id_global_key')];
            #$URLS = $this->db_menu->get_menu($sqlWhere);
            $this->userAccess = [];
        }

        $URLS = array_column($this->userAccess, 'link', 'id_menu');
        foreach ($URLS as $id_menu => $link)
            $response['menu' . $id_menu] = $link;

        return $response;
    }

    /**
     * Configuración para la construcción del menú de acuerdo al perfil del usuario
     * @param  string $ids_menu [description]
     * @return [type]            [description]
     */
    private function get_user_menu()
    {
        $this->load->library('SB_Menu');
        $dataMenu = [];
        foreach ($this->userAccess as $menu) {
            if ($menu['tipo'] == 'URL') continue;
            $dataMenu[] = $menu;
        }

        return $this->sb_menu->build_menu($dataMenu);
    }

    protected function parserFile()
    {
        if (isset($_FILES['file'])) {
            $file_post  = $_FILES['file'];
            $file_keys  = array_keys($file_post);

            foreach ($_FILES['file']['name'] as $i => $value) {
                foreach ($file_keys as $key) {
                    $files[$i][$key] = $file_post[$key][$i];
                }
            }

            unset($_FILES['file']);
            $_FILES = $files;
        }
    }
}

/* End of file SB_Controller.php */
/* Location: ./application/core/SB_Controller.php */