<?php
/*
Script para obtener los precios del producto, en paso previo a generar pedido en formulario de compra.
*/


$result = array("success" => false);


if(!isset($_POST["price_usd"]) || !is_numeric($_POST["price_usd"])) {
	echo json_encode($result);
	exit;	
}

require_once("../../global_scripts/php/mysql_connection.php");
require_once("../../global_scripts/php/purchase-functions.php");


$result["prices"] = array(quickCalcGame(1, $_POST["price_usd"]), quickCalcGame(2, $_POST["price_usd"]));
$result["success"] = true;

echo json_encode($result);




?>