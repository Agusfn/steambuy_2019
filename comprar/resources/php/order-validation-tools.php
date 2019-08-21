<?php
function validateInitialData($name, $email, $paymethod, $steam_profile_url) {
	// forma pago
	if($paymethod != 1 && $paymethod != 2) {
		echo "Error: medio de pago inválido. Reintenta la operación.";
		exit;	
	}
	// nombre
	if(!preg_match("/^[a-z\sñáéíóú]*$/i", $name) || strlen($name) < 4 || strlen($name) > 30) {
		echo "Error: nombre incorrecto o caracateres inválidos. Reintenta la operación.";
		exit;
	}
	// email
	$mail_pattern = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
	if(!preg_match($mail_pattern, $email) || $email == "" || strlen($email) > 50){
		echo "Error: mail incorrecto. Reintenta la operación.";
		exit;
	}
	
	// SteamURL (si existe)
	$url_patt1 = "/^(https?:\/\/)?steamcommunity.com\/id\/[a-z0-9]{1,50}(\/.*)?$/i";
	$url_patt2 = "/^(https?:\/\/)?steamcommunity.com\/profiles\/[0-9]{13,25}(\/.*)?$/i";
	
	if($steam_profile_url != "") {
		if(!preg_match($url_patt1, $steam_profile_url) && !preg_match($url_patt2, $steam_profile_url)) {
			echo "Error: SteamURL incorrecta. Reintenta la operación.";
			exit;
		}
	}
	
}


function remember_client_info($name, $email, $steam_profile_url) {

	setcookie("client_name", $name, time() + 5184000, "/");
	setcookie("client_email", $email, time() + 5184000, "/");
	if($steam_profile_url != "") {
		setcookie("client_steam_url", $steam_profile_url, time() + 5184000, "/");
	}

}



function forget_client_info() {

	if(isset($_COOKIE["client_name"])) {
		unset($_COOKIE["client_name"]);
		setcookie("client_name", "", time() - 3600, "/"); 
	}
	if(isset($_COOKIE["client_email"])) {
		unset($_COOKIE["client_email"]);
		setcookie("client_email", "", time() - 3600, "/"); 
	}
	if(isset($_COOKIE["client_steam_url"])) {
		unset($_COOKIE["client_steam_url"]);
		setcookie("client_steam_url", "", time() - 3600, "/"); 
	}

}


?>