<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail extends PHPMailer {

	public $email_onoff;
	public $email_address, $email_name;
	public $email_bcc_onoff, $email_bcc;
	public $success, $resultado, $data;
	public $body, $tipo, $asunto, $adjuntos, $destinatarios, $destinatariosCc, $destinatariosBcc, $imagenes;
	private $ci;

	function __construct() {
        $this->email_onoff		= get_var('email_onoff') ? TRUE : FALSE; 
        $this->success 			= FALSE;
        $noCuenta 				= get_var('email_cuenta', 1);
        $this->Host             = get_var("email_{$noCuenta}_host");
        $this->email_address   	= get_var("email_{$noCuenta}_address");
        $this->Username			= get_var("email_{$noCuenta}_user", FALSE);
        $this->Password			= get_var("email_{$noCuenta}_pass", FALSE);
        $this->Port				= get_var("email_{$noCuenta}_port", FALSE);
        $this->SMTPSecure		= get_var("email_{$noCuenta}_stmp_secure", FALSE);
        $this->SMTPAuth			= get_var("email_{$noCuenta}_stmp_auth", FALSE);
        $this->email_name		= get_var('email_name');
        $this->email_bcc_onoff	= get_var('email_bcc_onoff') ? TRUE : FALSE;
        $this->email_bcc		= get_var('email_bcc');
        $this->email_debug		= get_var('email_debug', 0);
    }

	function send($data=array()) {
        $mail = new PHPMailer;
        /**
         * ALERTA!!!!!
         * ALERTA!!!!!
         * ALERTA!!!!!
         * YA TE HAKIE!!!
         * COMENTAR ESTA LINEA EN PRODUCCION
         * @var array
         */
        $mail->SMTPOptions = array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		));

		if($this->email_onoff) {
			// Variables recibidas
			$this->body 				= $data['body'];
			$this->tipo 				= ($data['tipo']=='html') ? TRUE : FALSE;
			$this->asunto 				= (isset($data['asunto'])) ? utf8_decode($data['asunto']) : $this->email_name;
			$this->adjuntos 			= (isset($data['adjuntos'])) ? $data['adjuntos'] : FALSE;
			$this->destinatarios 		= (isset($data['destinatarios'])) ? $data['destinatarios'] : FALSE;
			$this->destinatariosCc  	= (isset($data['destinatariosCC'])) ? $data['destinatariosCC'] : FALSE;
			$this->destinatariosBcc 	= (isset($data['destinatariosBCC'])) ? $data['destinatariosBCC'] : FALSE;
			$this->imagenes 			= (isset($data['imagenes'])) ? $data['imagenes'] : FALSE;
			// Setup
			$mail->isSMTP();	//Establece uso de SMTP
			$mail->SMTPDebug 		= $this->email_debug; //Enable SMTP debugging :  0=>off; 1=>client msg; 2=>server & client msg
			// $mail->Debugoutput 		= 'html';
			if ($this->email_debug) {
				$date = date('Ymd-His');
				$filepath= $data['log_path_email'];
				file_exists($filepath) OR mkdir($filepath, 0755, TRUE);
				$filelog = "$filepath/emaillog_{$date}.".config_item('log_file_extension');
				$log = fopen($filelog, 'w' );		        
				$mail->Debugoutput = function($str) use ($filelog) {
				   error_log($str, 3, $filelog);
				};		        
				fclose($log);
			}

			// $mail->isSMTP();	//Establece uso de SMTP
			$mail->Host 			= $this->Host;
			$mail->Port 			= $this->Port;
			$mail->SMTPSecure 		= $this->SMTPSecure;
			$mail->SMTPAuth 		= $this->SMTPAuth;
			$mail->Username 		= $this->Username;
			$mail->Password 		= $this->Password;
			$mail->email_address	= $this->email_address;
			$mail->email_name 		= $this->email_name;
			$mail->email_bcc_onoff 	= ($this->email_bcc_onoff) ? TRUE : FALSE;
			$mail->email_bcc 		= $this->email_bcc;
				//print_debug($mail);		
			//Emisor Data
			$mail->setFrom($this->email_address, $this->email_name);
			//Direccion de respuesta
			$mail->addReplyTo($this->email_address, $this->email_name);
			//Receptor Data
			if(is_array($this->destinatarios)){
				foreach($this->destinatarios as $destinatario) {
					$mail->addAddress($destinatario['email'], utf8_decode($destinatario['nombre']));
				}				
			}
			// CC

			if(is_array($this->destinatariosCc)){
				foreach($this->destinatariosCc as $destinatarioCc){
					$mail->addCC($destinatarioCc['email'], utf8_decode($destinatarioCc['nombre']));
				}
			}
			// BCC
			if(is_array($this->destinatariosBcc)) {
				foreach($this->destinatariosBcc as $destinatarioBcc) {
					if($destinatarioBcc['email'] != '')  
						$mail->addBCC($destinatarioBcc['email'], utf8_decode($destinatarioBcc['nombre']));
				}
			}

			if(isset($data['email_bcc_off'])) {

			}
			else {
				// Copia oculta - Acuses
				if($this->email_bcc_onoff){			
					$mail->addBCC($this->email_bcc, $this->email_bcc);
				}
			}
				
			//Asunto
			$mail->Subject = $this->asunto;
			// Imagenes			
			if(is_array($this->imagenes)>0){
				foreach($this->imagenes as $imagen){
					$mail->AddEmbeddedImage(trim($imagen['ruta'],'/').'/'.$imagen['file'], $imagen['alias'],$imagen['file'], $imagen['encode'], $imagen['mime']);
				}
			}
			
			$mail->Body = $this->body;
			$mail->IsHTML($this->tipo);

			if(isset($data['ical'])) {
				// $mail->CharSet 		= 'UTF-8';
				// $mail->ContentType = 'text/html';
				$mail->Ical = $data['ical'];
			}
			else {
				if (isset($data['imgHeader']) && isset($data['imgHeaderName'])) {
	    			$mail->AddEmbeddedImage($data['imgHeader'], 'imgHeader', $data['imgHeaderName']);
				}

				if (isset($data['imgFooter']) && isset($data['imgFooterName'])) {
	    			$mail->AddEmbeddedImage($data['imgFooter'], 'imgFooter', $data['imgFooterName']);
				}

				if (isset($data['imgcorreo'])) {
					foreach ($data['imgcorreo'] as $img) {
						$mail->AddEmbeddedImage($img['imgPath'], $img['imgID'], $img['imgName']);
					}
				}
			}
				

			//Texto plano alternativo al HTML
			$mail->AltBody = 'Su correo no soporta HTML, por favor, contacte a su administrador de correo.';

			//Adjunto
			if(is_array($this->adjuntos)){
				foreach($this->adjuntos as $adjunto){
					$mail->addAttachment($adjunto);
				}
			}

			// Envío de correo e imprime mensajes
			if (!$mail->send()) {
				log_message('error', $mail->ErrorInfo);
			    $respuesta = array('success' => FALSE, 'error' => $mail->ErrorInfo, 'msg' =>  $mail->ErrorInfo);
			} else {
			    $respuesta = array('success' => TRUE, 'msg' => lang("mail_send_succes"));
			}
		}else{ 
			// $this->success = TRUE; 
			log_message('info', lang("mail_outings_off"));
			$respuesta = array('success' => FALSE, 'msg' => lang("mail_outings_off"));
		}
		return $respuesta;
	}
}