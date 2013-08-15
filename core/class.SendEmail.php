<?php
/***********************/ 
/*Autor:*/
//Daniel Romero
/*Fecha:*/
//14/05/2013
/*Objetivo:*/
/*Procesar el Envio de Correo electronico*/
/*Comentarios:*/
/*Archivo de Clase que se encarga de Enviar Correos electronicos, a traves de dos metodos, utilizando la funcion mail, o a traves de procedimiento de Base de Datos*/
/*Actualizacion:*/

/*Fecha Actualizacion*/

/**********************/
class SendEmail{
		public $remitente;
		public $destinatario;
		public $asunto;
		public $msgboby;
		public $cabeceras;
		Function EnviarByMail(){
			$this->cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$this->cabeceras  .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$this->cabeceras  .= 'To:' . $this->destinatario . "\r\n";
			$this->cabeceras  .= 'From: '. $this->remitente  . "\r\n";
			$resultado = mail($this->destinatario, $this->asunto, $this->msgboby,$this->cabeceras); 
			if($resultado == 1){
				$str_result="OK|CORREO ENVIADO";
			}
			else{
				$str_result="ERROR|NO SE PUDO ENVIAR EMAIL";
			}
			return $str_result;
		}
		
		Function EnviarByMaildb($db){
			$sql = "BEGIN produc.prc_envio_correo('".$this->remitente."','".$this->destinatario."','". $this->asunto ."','".$this->msgboby."'); END;" ;					
			//ECHO $sql;
			$stmt = $db->PrepareSP($sql);
			//$db->OutParameter($stmt, $str_result, 'str_result');
			
			if(!$db->Execute($stmt)){				
				print $db->ErrorMsg();
			}
		}
		
		Function EnviarByMailSMTP(){
			require_once('PHPMailer-master/class.phpmailer.php');
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->IsSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug  = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host       = '192.168.10.251';
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port       = 587;
			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = 'tls';
			//Whether to use SMTP authentication
			$mail->SMTPAuth   = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username   = "dromero@caricia.com";
			//Password to use for SMTP authentication
			$mail->Password   = "dromero";
			//Set who the message is to be sent from
			$mail->SetFrom($this->remitente, $this->remitente);
			//Set an alternative reply-to address
			//$mail->AddReplyTo('replyto@example.com','First Last');
			//Set who the message is to be sent to
			
			/*Enviar a varios remitentes */
			$array1=split(",",$this->destinatario);			
			foreach ($array1 as $value) {
				if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$mail->AddAddress($value);	
				}

			}
			//Copia a Mi Correo para Debug
			$mail->AddAddress('dromero@caricia.com', 'Daniel Romero');
			//Set the subject line
			$mail->Subject = $this->asunto;
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			$mail->MsgHTML($this->msgboby);
			//Replace the plain text body with one created manually
			$mail->AltBody = '';
			//Attach an image file
			//$mail->AddAttachment('images/phpmailer_mini.gif');

			//Send the message, check for errors
			if(!$mail->Send()) {
				echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				echo "Message sent!";
			}
		}
}
?>

