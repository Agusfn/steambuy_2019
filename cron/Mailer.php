<?php

class Mailer
{

	const TO_ADDR = "agusfn20@gmail.com";

	const FROM_USER = "notif-steambuy.com.ar";
	const FROM_PASSWORD = "Galapagos#235";


	private static $mail;


	private static $error_text;


	public static function sendNotification($destination, $min_price_usd, $min_price_ars, $flights)
	{
		self::$mail = new PHPMailer();

		self::$mail->Subject = "Vuelos a ".$destination." a partir de $".$min_price_ars." (".$min_price_usd." USD)";
		
		$body = "LINKS:<br/><br/>";

		foreach($flights as $flight) {

			$body .= "<br/>";

		}

		self::$mail->Body    = $body;
		self::$mail->AltBody = $body;
		self::$mail->addAddress(self::TO_ADDR, "Agustin");

		self::sendMail();

	}

	public static function errorText() {
		return self::$error_text;
	}



	private static function sendMail()
	{
		self::$mail->CharSet = 'UTF-8';
		self::$mail->isSMTP();
		self::$mail->Host = "localhost"; 
		//self::$mail->Port = 587;  
		self::$mail->SMTPAuth = true; 
		self::$mail->Username = self::FROM_USER;
		self::$mail->Password = self::FROM_PASSWORD;
		//self::$mail->SMTPSecure = 'SSL';
		self::$mail->From = self::FROM_USER;
		self::$mail->FromName = 'Cheap Flights';
		self::$mail->isHTML(true);

		if(self::$mail->send()) 
			return true;
		else 
		{
			self::$error_text = self::$mail->ErrorInfo;
			Log::notice("Mailer unsuccesful: ".self::$error_text);
			return false;	
		}
		
	}


}