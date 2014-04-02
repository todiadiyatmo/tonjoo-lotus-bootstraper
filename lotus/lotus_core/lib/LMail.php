<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LMail{

	function sendMail($args){

		$sender = $args['sender'];
		$recipient = $args['recipient'];
		$body = $args['body'];
		$subject = $args['subject'];

		require L_BASEPATH.'/core/lib/vendor/class.phpmailer.php';
		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->IsSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug  = 2;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host       = "node-a.tonjoo.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port       = 465;
		//Whether to use SMTP authentication
		$mail->SMTPAuth   = true;
		//Username to use for SMTP authentication
		$mail->Username   = "todiadiyatmo@tonjoo.com";
		//Password to use for SMTP authentication
		$mail->Password   = "waterloo1815";
		//Set who the message is to be sent from
		$mail->SetFrom('todiadiyatmo@tonjoo.com', 'Todi Adiyatmo Wijoyo');
		//Set an alternative reply-to address
		$mail->AddReplyTo('todiadiyatmo@tonjoo.com','Todi Adiyatmo Wijoyo');
		//Set who the message is to be sent to
		$mail->AddAddress('todiadiyatmo@gmail.com', 'John Doe');
		//Set the subject line
		$mail->Subject = 'PHPMailer SMTP test';
		//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
		$mail->MsgHTML("asdasdasd");
		//Replace the plain text body with one created manually
		$mail->AltBody = 'This is a plain-text message body';
		//Send the message, check for errors
		if(!$mail->Send()) {
		  echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  echo "Message sent!";
		}

	}

}