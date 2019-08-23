<?php
require_once("../../global_scripts/php/mysql_connection.php");
require_once("../../global_scripts/php/steam_product_fetch.php");


$response = [];

if(isset($_POST["steam_url"]) && preg_match("#^(https?://)?store\.steampowered\.com/(sub|app)/([0-9]{1,10})(/.*)?$#", $_POST["steam_url"])) {
	
	$product = new SteamProduct($_POST["steam_url"]);

	if($product->success) {

		
		/***** MODIFICIAR PONER ESTO EN OTRO LADO O OOO *********/
		$steamPriceReal = round($product->finalPrice * 1.21, 1);
		$finalPrice = round($steamPriceReal * 1.15);


		saveProductQuoteInDb(
			$product->productUrl,
			$product->name,
			$product->firstPrice,
			$product->finalPrice,
			$product->discount,
			$finalPrice
		);


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
		$response["error_text"] = "Ocurrió un error cargando los datos del producto, o el juego no tiene precio disponible.";
	}

}
else {
	$response["success"] = false;
	$response["error_text"] = "La URL de Steam proporcionada es incorrecta.";
}


header('Content-Type: application/json');
echo json_encode($response);




/**
 * Save quotation in table 'quotes'
 * @param  string $url              steam product url
 * @param  string $name             product name
 * @param  float $steamFirstPrice  
 * @param  float $steamFinalPrice  
 * @param  boolean $steamDiscount    
 * @param  float $quotedFinalPrice 
 * @return null                   
 */
function saveProductQuoteInDb($url, $name, $steamFirstPrice, $steamFinalPrice, $steamDiscount, $quotedFinalPrice)
{
	global $con;

	$sql = "INSERT INTO `quotes` (`id`, `date`, `product_url`, `product_name`, `current_steam_firstprice_ars`, `current_steam_finalprice_ars`, `current_steam_discount`, `quoted_final_price_ars`) VALUES 
	(NULL, NOW(), '".mysqli_real_escape_string($con, $url)."', '".mysqli_real_escape_string($con, $name)."', ".$steamFirstPrice.", ".$steamFinalPrice.", ".($steamDiscount ? 1 : 0).", ".$quotedFinalPrice.");";

	mysqli_query($con, $sql);
}