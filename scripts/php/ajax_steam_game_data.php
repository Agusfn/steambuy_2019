<?php
require_once("../../global_scripts/php/steam_product_fetch.php");


$response = [];

if(isset($_POST["steam_url"]) && preg_match("#^(https?://)?store\.steampowered\.com/(sub|app)/([0-9]{1,10})(/.*)?$#", $_POST["steam_url"])) {
	
	$product = new SteamProduct($_POST["steam_url"]);

	if($product->success) {

		
		/***** MODIFICIAR PONER ESTO EN OTRO LADO O OOO *********/
		$steamPriceReal = round($product->finalPrice * 1.21, 1);
		$finalPrice = round($steamPriceReal * 1.15);

		$response = [
			"success" => true,
			"data" => [
				"product_name" => $product->name,
				"product_discount" => $product->discount,
				"product_steamprice" => $steamPriceReal,
				"product_finalprice" => $finalPrice
			]
		];

	}
	else {
		$response["success"] = false;
		$response["error_text"] = "Ocurrió un error cargando los datos del producto, por favor recarga la página o intentá más tarde.";
	}

}
else {
	$response["success"] = false;
	$response["error_text"] = "La URL de Steam proporcionada es incorrecta.";
}


header('Content-Type: application/json');
echo json_encode($response);