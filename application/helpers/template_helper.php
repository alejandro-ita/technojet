<?php

function get_menu_top() {
	$CI =& get_instance();

	if (isset($CI->sb_menu->id_menu_active)) {
		$menuItem = [];
		foreach ($CI->userAccess as $menu) {
			if ($menu['id_menu'] == $CI->sb_menu->id_menu_active) {
				$dataview['menu_texto'] = lang($menu['texto']);
				$dataview['menu_link'] 	= base_url($menu['link']);
			}

			if ($menu['id_padre'] == $CI->sb_menu->id_menu_active) {
				$menuItem[] = $menu;
			}
		}

		$dataview['menuItem'] = $menuItem;
		return $CI->parser->parse('tpl-main/tpl-dropdown-menu.html', $dataview, TRUE);
	}
}

function show_price() {
	$CI =& get_instance();
	#MOSTRAMOS EL PRECIO DE INVENTARIO SOLO PARA EL PERFIL DE ROOT Y ADMIN
	return (int) in_array($CI->session->userdata('id_perfil'), [1,2]);
}