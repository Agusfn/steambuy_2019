<?php
session_start();

define("ROOT_LEVEL", "");

header("Content-Type: text/html; charset=UTF-8");

require_once("global_scripts/php/client_page_preload.php");
require_once("global_scripts/php/admlogin_functions.php");
$config = include("global_scripts/config.php");


$admin = false;
if(isAdminLoggedIn())
{
	$admin = true;
}


if(isset($_GET["id"], $_GET["clave"])) {
	$orderId = $_GET["id"];
	$orderPassword = $_GET["clave"];
}
else {
	header("Location: /");
	exit;
}


if(isset($_GET["mail_sent"]) && $_GET["mail_sent"] == 0)
	$mailError = true;
else
	$mailError = false;



$sql = "SELECT * FROM `orders` WHERE `order_id`='".mysqli_real_escape_string($con, $orderId)."' AND BINARY `order_password`='".mysqli_real_escape_string($con, $orderPassword)."' AND `order_status`=1";
$query = mysqli_query($con, $sql);

if(mysqli_num_rows($query) == 1) {
	$orderData = mysqli_fetch_assoc($query);
}
else {
	header("Location: /");
	exit;
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <meta name="robots" content="noindex, nofollow" />
        
        <title>Pedido generado - SteamBuy</title>
        
        
        <link rel="shortcut icon" href="favicon.ico">
        
        <link rel="stylesheet" href="global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="global_design/css/main.css" type="text/css">
        <link rel="stylesheet" href="comprar/resources/css/shared-steps.css" type="text/css">
		<link rel="stylesheet" href="comprar/resources/css/step3.css" type="text/css">


		<script type="text/javascript" src="global_scripts/js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="resources/js/global-scripts.js"></script>
    </head>
    
    <body>

		<?php require_once("global_scripts/php/header.php"); ?>
        
        <div class="wrapper">
        	
            <div class="main_content">
            
				<div class="purchase_instructions">
                    <h4 class="pi_title">El pedido se ha generado</h4>
                    <div class="alert alert-info" style="font-size: 14px;margin-top:30px; text-align:justify">
                    	Guarda estos datos de tu pedido en caso de soporte: ID de pedido: <strong><?php echo $orderData["order_id"]; ?></strong>, clave de pedido: <strong><?php echo $orderData["order_password"]; ?></strong>.
                    </div>

					<div class="pi_instructions">Se ha generado tu pedido de <strong>$<?php echo $orderData["product_arsprice"]; ?> pesos argentinos</strong> por el juego <strong><?php echo $orderData["product_name"]; ?></strong>, el siguiente paso es imprimir y abonar el cupón de pago en cualquier sucursal de <strong>Rapipago</strong>,
                    <strong>Pago Fácil</strong>, <strong>Ripsa</strong>, <strong>Cobroexpress</strong>, <strong>Bapropagos</strong>, u otras cadenas de pago especficadas en la boleta o cupón de pago.<br></div>
                    
                    <div style="text-align:center; margin:25px 0;">
                    	<a href="<?php echo $orderData["order_purchaseticket"]; ?>" target="_blank" class="btn btn-primary btn-lg">Ver cupón de pago&nbsp;&nbsp;<span class="glyphicon glyphicon-barcode"></span></a>
                        <br/><a href="<?php 
						$split = explode("?id=",$orderData["order_purchaseticket"]);
						echo "https://www.cuentadigital.com/ticket.php?id=".substr($split[1], 4, 8);
						 ?>" target="_blank">Ver en formato ticket</a>
                    </div>  
                        
                    <div class="pi_instructions">Una vez abonado, el pago <strong>se acreditará instantáneamente</strong> para la mayoría de medios de pago, o tomará <strong>entre 12 y 48 horas en acreditarse</strong> para el resto (ver tiempos de acreditación en boleta).<br/>
                    <br/>
                    El producto será enviado en formato <strong>regalo de Steam</strong> durante el día en que se acredita el pago, o hasta 2 días después de acreditado. Podés ver el estado de tu pago en el <a href="https://www.cuentadigital.com/area.php?name=Search&query=<?php echo $split[1]; ?>" target="_blank">siguiente enlace</a>, que también se te ha enviado por e-mail.</div> 

					<div class='alert alert-info' style='margin-top: 15px;padding-bottom: 15px;'>Este juego se envía como regalo de Steam, para que podamos enviarlo necesitamos que agregues a nuestra cuenta de Steam como amigo. Envíanos una solicitud de amistad con la cuenta que quieres recibir el juego:
					<a href='http://steamcommunity.com/id/agusfn/' target='_blank'><span class='label label-primary'>http://steamcommunity.com/id/agusfn/</span></a></div>

                    <?php
					if($orderData["product_limited_discount"] == 1) 
					{
						?>
						<div class="alert alert-warning pi_offerwarning">
							Este juego posee una oferta limitada de reventa de Steam. Es necesario que al menos 2hs antes de que la oferta finalice el pago se encuentre <strong>acreditado y tener nuestra cuenta de Steam agregada a amigos</strong>. De lo contrario deberás elegir un cambio de pedido o un reembolso. 
                        </div>
						<?php	
					}

					if($mailError) {
						?>
                        <div class="alert alert-danger" style="font-size: 14px;margin-top:30px; text-align:justify">Ha ocurrido un error intentando enviar el e-mail con los datos de pedido, tomá nota del <strong>ID</strong> y <strong>clave de pedido</strong> de esta página ya que <strong>no se pudieron enviar por e-mail</strong>, disculpa las molestias.</div>
                    	<?php	
					}
					?>
                                                    
                    <div class="pi_return"><a href="../">Volver a la página principal</a></div>
				</div>

			</div>
            
            <?php require_once("global_scripts/php/footer.php"); ?>
            
		</div>
	</body>
</html>

