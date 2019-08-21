<?php

	require_once("../../global_scripts/php/mysql_connection.php");
	require_once("../../global_scripts/php/purchase-functions.php");
	require_once("../../global_scripts/email/mailer.php");
	require_once "../../comprar/resources/php/order-validation-tools.php";
	
	$config = include("../../global_scripts/config.php");
	
	if(!$con) {
		echo "Error: Sin conexión";
		exit;	
	}
		

	if(isset($_POST["payment_method"]) && isset($_POST["product_name"]) && isset($_POST["product_sellingsite"]) && 
	isset($_POST["product_siteurl"]) && isset($_POST["product_discount"]) && isset($_POST["product_usdprice"]) && isset($_POST["client_name"]) && 
	isset($_POST["client_email"]) && isset($_POST["client_steam_url"]) && isset($_POST["remember_data"])) 
	{ 
		
		$payment_method = $_POST["payment_method"];
		$clientName = $_POST["client_name"];
		$clientEmail = $_POST["client_email"];
		$clientSteamProfile = $_POST["client_steam_url"];
		$gameName = $_POST["product_name"];
		$gameSellingSite = $_POST["product_sellingsite"];
		$gameSiteUrl = $_POST["product_siteurl"];
		$gameUsdPrice = floatval($_POST["product_usdprice"]);
		$gameDiscount = $_POST["product_discount"];
		
		// ****** Validación de datos ******
		
		validateInitialData($clientName, $clientEmail, $payment_method, $clientSteamProfile);
	

		// nombre del juego
		$gameName = preg_replace('/\s\s+/', ' ', $gameName);
		if(strlen($gameName) == 0){
			echo "Error: No se ha ingresado el nombre del juego."; return;			
		} else if(preg_match("/(2|two|3|three|4|four|6|six)(\s|\-)?pack/i",$gameName)) {
			echo "Error: No se permite la compra de packs múltiples.";	
		}
		
		// sitio de venta
		if($gameSellingSite != 1 && $gameSellingSite != 2) {
			echo "Error: Sitio de venta inválido."; return;	
		}
		
		// url del juego
		if($gameSellingSite == 1 && strpos($gameSiteUrl,"store.steampowered.com") === false) {
			echo "Error: La URL ingresada no es de la tienda de Steam."; return;		
		}else if($gameSellingSite == 2 && strpos($gameSiteUrl,"amazon.com") === false) {
			echo "Error: La URL ingresada no es de la tienda de Amazon."; return;		
		}
		
		// precio del juego
		if(is_numeric($gameUsdPrice) && $gameUsdPrice != "") {
			if($gameUsdPrice > 50) {
				echo "Error: El precio del juego no debe ser mayor a 50 usd."; return;	
			}
		} else {
			echo "Error: No se ha ingresado un monto válido."; return;	
		}
		
		// **** Guardar datos de comprador ****
		
		if($_POST["remember_data"] == "true") {
			remember_client_info($clientName, $clientEmail, $clientSteamProfile);
		} else if($_POST["remember_data"] == "false")  {
			forget_client_info();
		}
		
		// ****** Calcular precio ******
		$gameArsPrice = quickCalcGame($payment_method, $gameUsdPrice);
		if(!is_numeric($gameArsPrice)) {
			echo "Error: Calculo de precio, ".$gameArsPrice; return;	
		}
		
		// ****** Crear orden *******
		$purchase = new Purchase($con);
		
		if($purchase->createGameOrder($payment_method, $gameName, "", $gameSellingSite, $gameSiteUrl, $gameDiscount, $gameUsdPrice, $gameArsPrice, $clientName, 
		$clientEmail, $clientSteamProfile, $_SERVER["REMOTE_ADDR"], "", 0)) 
		{
				
			$orderInfo = $purchase->orderInfo;
			
			if($payment_method == 2) {
				$orderInfo["bank_account"] = "Caja de ahorro $ 0849/01118545/07 ";
				$orderInfo["bank_account_cbu"] = "0150849701000118545070";
				$orderInfo["bank_account_owner"] = "Rodrigo Fernandez Nuñez";
				$orderInfo["bank_account_cuil"] = "23-35983336-9";
			}
			
			$mail = new Mail;
			$mail_data = array(
			"receiver_name"=>$clientName,
			"order_id"=>$orderInfo["order_id"],
			"order_password"=>$orderInfo["order_password"],
			"product_name"=>$gameName,
			"order_ars_price"=>$gameArsPrice,
			"payment_method"=>$payment_method,
			"product_external_discount"=>$gameDiscount,
			"stock" => 0,
			"product_sellingsite"=>$gameSellingSite,
			"product_site_url"=>$gameSiteUrl,
			"order_fromcatalog"=>0);
			if($payment_method == 1) {
				$mail_data["order_purchaseticket_url"] = $orderInfo["order_purchaseticket"];	
			}
			
			$mail->prepare_email("pedido_juego_generado", $mail_data);
			$mail->add_address($clientEmail, $clientName);
			
			if(!$mail->send()) $orderInfo["mailsent"] = 0;
			else $orderInfo["mailsent"] = 1;
			
			echo json_encode($orderInfo);

		} else echo "Error: ". $purchase->error; 	
			
	}

?>