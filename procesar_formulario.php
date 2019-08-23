<?php
session_start();

define("ROOT_LEVEL", "");

header("Content-Type: text/html; charset=UTF-8");

require_once("global_scripts/php/client_page_preload.php");
require_once("global_scripts/php/admlogin_functions.php");
require_once("global_scripts/php/purchase-functions.php");
require_once("global_scripts/email/mailer.php");
require_once("global_scripts/php/steam_product_fetch.php");
require_once("comprar/resources/php/order-validation-tools.php");
$config = include("global_scripts/config.php");


$admin = false;
if(isAdminLoggedIn())
{
	$admin = true;
}




if(!isset($_POST["buyer_name"], $_POST["buyer_email"], $_POST["game_url"], $_POST["buyer_account_url"])) {
	echo "Error: No se proporcionaron los datos necesarios.";
	exit;
}

$buyerName = $_POST["buyer_name"];
$buyerEmail = $_POST["buyer_email"];
$gameUrl = $_POST["game_url"];
$buyerSteamAccUrl = $_POST["buyer_account_url"];


validateInitialData($buyerName, $buyerEmail, 1, $buyerSteamAccUrl);


$product = new SteamProduct($gameUrl);



if($product->success) {

	/***** PUT IN ANOTHER PLACEEE **********/
	$steamPriceReal = round($product->finalPrice * 1.21, 1);
	$finalPriceArs = round($steamPriceReal * 1.15);
	

	$purchase = new Purchase($con);


	$orderSuccess = $purchase->createGameOrder(1, $product->name, "", 1, $gameUrl, $product->discount ? 1 : 0, 0, $product->finalPrice, $finalPriceArs, $buyerName, 
		$buyerEmail, $buyerSteamAccUrl, $_SERVER["REMOTE_ADDR"], "", 0);


	if($orderSuccess) {

		$mail = new Mail();
		$mail_data = array(
			"receiver_name" => $buyerName,
			"order_id" => $purchase->orderInfo["order_id"],
			"order_password" => $purchase->orderInfo["order_password"],
			"order_purchaseticket_url" => $purchase->orderInfo["order_purchaseticket"],
			"product_name" => $product->name,
			"order_ars_price" => $finalPriceArs,
			"payment_method" => 1,
			"product_external_discount" => $product->discount,
			"stock" => 0,
			"product_sellingsite" => 1,
			"product_site_url" => $gameUrl,
			"order_fromcatalog" => 0			
		);

		$mail->prepare_email("pedido_juego_generado", $mail_data);
		$mail->add_address($buyerEmail, $buyerName);
		
		if($mail->send())
			$mailSent = true;
		else
			$mailSent = false;
	}



}


if($product->success && $orderSuccess) {
	header("Location: pedido_generado.php?id=".$purchase->orderInfo["order_id"]."&clave=".$purchase->orderInfo["order_password"]."&mail_sent=".($mailSent ? 1 : 0));
	exit;
}
else {
	?>

	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	    
	    <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	        
	        <meta name="robots" content="noindex, nofollow" />
	        
	        <title>Error generando pedido - SteamBuy</title>
	        
	        
	        <link rel="shortcut icon" href="favicon.ico">
	        
	        <link rel="stylesheet" href="global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
	        <link rel="stylesheet" href="global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
	        <link rel="stylesheet" href="global_design/css/main.css" type="text/css">

			<script type="text/javascript" src="global_scripts/js/jquery-1.8.3.min.js"></script>
	        <script type="text/javascript" src="global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="resources/js/global-scripts.js"></script>
	    </head>
	    
	    <body>

			<?php require_once("global_scripts/php/header.php"); ?>
	        
	        <div class="wrapper">
	        	
	            <div class="main_content">
	            
		            <?php
		            if(!$product->success) {
		            	?>
		            	<div class="alert alert-danger" style="margin: 40px 0">Ocurrió un error intentanto obtener la información del juego de Steam, <a href="/">intenta realizar el pedido nuevamente</a>.</div>
		            	<?php
		            }
		            else if(!$orderSuccess) {
		            	?>
		            	<div class="alert alert-danger" style="margin: 40px 0">Ocurrió un error intentanto generar el pedido, <a href="/">intenta generarlo nuevamente</a>.</div>
		            	<?php	
		            }
		            ?>

				</div>
	            
	            <?php require_once("global_scripts/php/footer.php"); ?>
	            
			</div>
		</body>
	</html>

	<?php
}
?>


