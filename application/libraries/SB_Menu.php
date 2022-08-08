<?php 
class SB_Menu {

	public function __construct() {
        $this->CI =& get_instance();
        if (strpos($this->CI->uri->uri_string(), 'database/almacen')!==FALSE) {
			$this->id_menu_active = 5;
		}
		
        if (strpos($this->CI->uri->uri_string(), 'database/ventas')!==FALSE) {
        	$this->id_menu_active = 11;
		}
	}

	private function get_text_menu($element) {
		return ($element['texto'] && isset($this->CI->lang->language[$element['texto']]))
			? $this->CI->lang->language[$element['texto']]
			: $element['texto'];
	}

	/**
	 * Construye el HTML del menu desktop
	 * @param  array   $menuData  [description]
	 * @param  integer $parent [description]
	 * @return [type]          [description]
	 */
	public function build_menu(&$menuData=array(), $parent=0) {
		$menu = '';
		foreach($menuData as $key => $element) {
			// debug($element);
			if($element['id_padre'] == $parent) {
				$txt  		= $this->get_text_menu($element);
				$href 		= base_url($element['link']);
				$icono 		= $element['icono'];

				$id_menu		= $element['id_menu'];
				$interactiveIN 	= '';
				$interactiveOUT = '';
				$class = ($element['link'] == $this->CI->uri->uri_string()) ? 'active' : '';

				switch (strtoupper($element['tipo'])) {
					case 'PADRE':
						$class .= ' has-sub';
						$interactiveIN = "
							<a href='#' class='nk-menu-link nk-menu-toggle'>
								<span class='nk-menu-icon'>
									<i class='{$icono}'></i>
								</span>
								<span class='nk-menu-text'> {$txt} </span>
							</a>
							<ul class='nk-menu-sub'>
						";
                		$interactiveOUT = "
                			</ul>";
						break;

					case 'SUBPADRE':
						$class .= ' has-sub';
						$interactiveIN = "
							<a href='#' class='nk-menu-link nk-menu-toggle'>
								<span class='nk-menu-icon'>
									<i class='{$icono}'></i>
								</span>
								<span class='nk-menu-text'> {$txt} </span>
							</a>
							<ul class='nk-menu-sub'>
						";
                		$interactiveOUT = "
                			</ul>";
						break;

					case 'HIJO':
						$interactiveIN  = "
						<a class='nk-menu-link' href='$href'>
							<span class='nk-menu-icon'>
								<i class='{$icono}'></i>
							</span>
							<span class='nk-menu-text'> {$txt} </span>";
						$interactiveOUT = '
						</a>';
						break;

					case 'SIMPLE':
						$interactiveIN  = "<a class='nk-menu-link' href='$href' data-key='{$id_menu}'>
							<span class='nk-menu-icon'>
								<i class='{$icono}'></i>
							</span>
							<span class='nk-menu-text'> {$txt} </span>";
						$interactiveOUT = '</a>';
						break;
				}

				$menu .= "<li class='nk-menu-item $class'>";
				$menu .= $interactiveIN;
				unset($menuData[$key]);
				$menu .= $this->build_menu($menuData, $element['id_menu']);
				$menu .= $interactiveOUT;
				$menu .= '</li>';
			}
		}

		return $menu;
	}
}