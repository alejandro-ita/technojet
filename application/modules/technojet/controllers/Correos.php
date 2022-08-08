<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Correos extends SB_Controller {

	/**
	 * Envío de correos a usuarios con el template general del sistema
	 * @param  array  $settings Datos para la construcción y el envío del correo
	 * @return Boolean resultado del envío del correo
	 */
	public function send_email(array $settings) {
		$this->load->library('Mail');
		$mailData =self::prepare_mail($settings);
		
		//IMAGENES EN EL CORREO
		$mailData['imgLogo'] 	 	= FCPATH.get_var('path_img') . '/email/head.png';
		$mailData['imgHeader'] 	 	= FCPATH.get_var('path_img') . '/email/head.png';
		$mailData['imgHeaderName'] 	= 'logo-white.png';
		$mailData['imgFooter'] 	 	= FCPATH.get_var('path_img') . '/email/foot.png';
		$mailData['imgFooterName'] 	= 'somos_pae_footer.png';		 
		$mailData['log_path_email'] = config_item('log_path_email');
		$mailData['modulo'] 		= 'homeoffice';
		//debug($mailData);
		// Send email
		// return true;
		return $this->mail->send($mailData);
	}

	/**
	 * Se prepara el HTML del correo para posterior poder ser enviado
	 * @param  array  $settings Todas las configuraciones posibles del correo
	 * @return $datos del correo para su envío.
	 */
	private function prepare_mail(array $settings) {
		$view 		= isset($settings['view']) 	 ? $settings['view'] : 'tpl-main';
        $modulo 	= isset($settings['modulo']) ? $settings['modulo'] : get_var('site_name');
        $app_title 	= get_var('site_name');
        $goto 		= isset($settings['goto_url']) ? $settings['goto_url'] : base_url();
        $click_aqui = isset($settings['mail_click_aqui']) ? $settings['mail_click_aqui'] : lang('mail_click_aqui_btn');
        $imgcorreo 	= isset($settings['imgcorreo']) ? $settings['imgcorreo'] : [];
        $defaultLang= $this->session->userdata('language') ? $this->session->userdata('language') : 'mx';
        $language   = isset($settings['language']) ? $settings['language'] : $defaultLang;
        $imgDefault = [];


  //       $imgDefault = [[
		// 		'imgPath' 	=> FCPATH.get_var('path_img').'/email/correo_c.png',
		// 		'imgName' 	=> 'correo_c.png',
		// 		'imgID' 	=> 'btn-ingresar'
		// 	], [
		// 		'imgPath' 	=> FCPATH.get_var('path_img').'/email/footer/1_footlogo.png',
		// 		'imgName' 	=> '1_footlogo.png',
		// 		'imgID' 	=> 'footerlogo'
		// 	]
		// ];

		$htmlData = array(
			 'url_image'    	=> base_url(get_var('path_img'))
			,'email-body'		=> $settings['email-body']
			,'base_url' 		=> base_url()
			,'title' 			=> utf8_decode(get_var('site_name'))
			,'app_title' 		=> utf8_decode($app_title)
			,'modulo' 			=> utf8_decode($modulo)
			,'no_responder' 	=> lang('mail_no_responder')
			,'anio' 			=> date('Y')
			,'mail_click_aqui' 	=> str_replace('{custom_url}', $goto, $click_aqui)
			,'show-header' 		=> isset($settings['show-header']) ? $settings['show-header'] : TRUE
		);
		$htmlTPL = $this->parser_view("email/$view", $htmlData);
		// echo $htmlTPL;
		// exit;
	
		// Create ArrayData
		$mailData = array(
			 'body' 	 	 => utf8_decode($htmlTPL)
			,'tipo' 	 	 => 'html'
			,'asunto' 	 	 => get_var('site_name').' - '.$settings['asunto']
			,'imgHeader' 	 => isset($settings['imgHeader']) 		? $settings['imgHeader'] 	 : NULL
			,'imgHeaderName' => isset($settings['imgHeaderName']) 	? $settings['imgHeaderName'] : NULL
			,'imgFooter' 	 => isset($settings['imgFooter']) 		? $settings['imgFooter'] 	 : NULL
			,'imgFooterName' => isset($settings['imgFooterName']) 	? $settings['imgFooterName'] : NULL
			,'adjuntos' 	 => isset($settings['adjuntos']) 		? $settings['adjuntos'] 	 : NULL
			,'imgcorreo' 	 => array_merge($imgcorreo, $imgDefault)
		);

		if (isset($settings['para'])) {
			$mailData['destinatarios'] = is_array($settings['para']) ? $settings['para'] : [['email' => $settings['para'], 'nombre' => 'nombre']];
		}

		if (isset($settings['cc'])) {
			$mailData['destinatariosCC'] = is_array($settings['cc']) ? $settings['cc'] : [['email' => $settings['cc'], 'nombre' => $settings['cc']]];
		}

		if (isset($settings['cco'])) {
			$mailData['destinatariosBCC'] = is_array($settings['cco']) ? $settings['cco'] : [['email' => $settings['cco'], 'nombre' => $settings['cco']]];
		}

		if (isset($settings['ical'])) {
			$mailData['ical'] = $settings['ical'];
		}

		if (isset($settings['email_bcc_off'])) {
			$mailData['email_bcc_off'] = $settings['email_bcc_off'];
		}
		
		return $mailData;
	}
}

/* End of file Correos.php */
/* Location: ./application/controllers/Correos.php */