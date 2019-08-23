<?php
/*
$data: receiver_name, order_id, order_password, product_name, order_ars_price, payment_method, product_external_discount, product_sellingsite, product_site_url, order_fromcatalog

si payment_method=1: order_purchaseticket_url
si order_fromcatalog = 1 y product_external_discount = 1: product_external_offer_endtime
*/

if(!isset($data)) return false;


$subject = "Se ha generado tu pedido por el juego ".$data["product_name"];


echo "Estimado/a ".$data["receiver_name"].", se ha generado tu pedido por el juego <strong>".$data["product_name"]."</strong> por <strong>&#36;".$data["order_ars_price"]." 
pesos argentinos</strong>. El ID del pedido es <strong>".$data["order_id"]."</strong> y la clave es <strong>".$data["order_password"]."</strong>.<br/>

El siguiente paso para recibir el juego es imprimir y abonar en cualquier sucursal de pago la boleta de pago que puedes encontrar en el siguiente link: <br/>
<a href='".$data["order_purchaseticket_url"]."' target='_blank'>".$data["order_purchaseticket_url"]."</a>.<br/><br/>
Una vez abonado, el pago se acreditará instantáneamente en alguos casos, o en otros tomará entre 12 y 48 horas en acreditarse. El juego será enviado entre el día en que se acredita el pago y hasta 2 días después.<br/><br/>";

if($data["product_sellingsite"] == 1 && $data["stock"] == 0) {
	echo "<strong>Para recibir el juego deberás agregarnos a nuestra <a href='http://steamcommunity.com/id/steambuyarg/'>cuenta de Steam</a> a la lista de amigos o aceptar la solicitud que enviaremos a la cuenta proporcionada.</strong><br/><br/>";	
}

if($data["product_external_discount"] == 1) {

	echo "Este juego posee una oferta limitada de reventa de Steam. <strong>Es necesario que el pago se encuentre acreditado al menos 2 horas antes de que la oferta finalice, y la cuenta de Steam nuestra agregada a la lista de amigos de la cuenta que recibe el juego</strong>. De lo contrario deberás elegir un cambio de pedido o un reembolso. ";

} else {
	
	echo "<strong>Recuerda que este pedido se cancelará automáticamente en la fecha de vencimiento del pago si no se recibe el mismo.</strong><br/>";
	
}
echo "Ante cualquier duda revisa la página de <a href='http://steambuy.com.ar/soporte/' target='_blank'>soporte</a> o <a href='mailto:contacto@steambuy.com.ar'>contáctanos</a>.<br/>
<br/>
Un saludo,<br/>
El equipo de SteamBuy";





?>