<?php
/*
data: receiver_name, order_id, product_name, expiration_type
si expiration_type = 2: inform_status, offer_endtime
	si inform_status = 2: reject_reason
	si inform_status = 3: order_informed_date
*/

if(!isset($data)) return false;

if($data["expiration_type"] == 1) { // expirado 5 días

	$subject = "Tu pedido por el juego ".$data["product_name"]." ha expirado";
	echo "Estimado/a ".$data["receiver_name"].", tu pedido ID <strong>".$data["order_id"]."</strong> que realizaste por el juego <strong>".$data["product_name"]."</strong> 
	ha expirado automáticamente debido a que no se registró el pago pasados los días de vencimiento, por lo cual el pedido y la boleta vencieron.<br/>
	<br/>
	Si ya abonaste el pedido, <a href='mailto:contacto@steambuy.com.ar'>contáctanos</a> para gestionar, si es posible, la compra del mismo producto, de lo contrario un cambio de producto o un 
	reembolso.<br/>
	<br/>
	Un saludo.<br/>
	<br/>
	El equipo de SteamBuy";
	
} else if($data["expiration_type"] == 2) { // expirado fin oferta externa lim.
	
	$subject = "La oferta por el juego ".$data["product_name"]." ha finalizado y tu pedido expiró";
	echo "Estimado/a ".$data["receiver_name"].", tu pedido ID <strong>".$data["order_id"]."</strong> por el juego <strong>".$data["product_name"]."</strong> 
	expiró debido a que su oferta limitada finalizó y no se pudo tener el pago acreditado a tiempo.<br/>
	<br/>
	Envianos un mensaje a 
	<a href='mailto:contacto@steambuy.com.ar'>nuestro correo</a> para para solicitar un cambio de productos y/o abono de la diferencia, o solicitar un reembolso.<br/>
	<br/>
	Un saludo.<br/>
	<br/>
	El equipo de SteamBuy";
	
} else if($data["expiration_type"] == 3) { // expiro por cualquiera de los 2 motivos, sin mucha informacion
	
	$subject = "Tu pedido por el juego ".$data["product_name"]." ha expirado";
	echo "Estimado/a ".$data["receiver_name"].", el pedido ID <strong>".$data["order_id"]."</strong> que realizaste por el juego <strong>".$data["product_name"]."</strong> 
	ha expirado debido a que no se registró el pago luego de la fecha de vencimiento, o porque su oferta limitada finalizó.<br/>
	<br/>
	Si ya abonaste el pedido, <a href='mailto:contacto@steambuy.com.ar'>contáctanos</a> para pedir el producto nuevamente, pedir un reembolso o pedir un cambio de producto. Si no abonaste 
	el pedido ignora este mensaje.<br/>
	<br/>
	Un saludo,<br/>
	El equipo de SteamBuy";	
	
}
?>