<?php
session_start();

define("ROOT_LEVEL", "../");

header("Content-Type: text/html; charset=UTF-8");

require_once("../global_scripts/php/client_page_preload.php");
require_once("../global_scripts/php/admlogin_functions.php");
require_once("../global_scripts/php/purchase-functions.php");




$admin = false;
if(isAdminLoggedIn())
{
	$admin = true;
}


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" itemscope itemtype="http://schema.org/Article">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>Términos y condiciones - SteamBuy</title>
        
        <meta name="description" content="Página donde se encuentran los términos y condiciones de compra.">
        <meta name="keywords" content="términos,condiciones,reglas,steambuy,compra,juego">
        
        <meta property="og:title" content="Términos y condiciones" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://steambuy.com.ar/condiciones/" />
        <meta property="og:image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg" />
        <meta property="og:site_name" content="SteamBuy" />
        <meta property="og:description" content="Página donde se encuentran los términos y condiciones de compra." />
        
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="https://steambuy.com.ar/condiciones/">
        <meta name="twitter:title" content="Términos y condiciones">
        <meta name="twitter:description" content="Página donde se encuentran los términos y condiciones de compra.">
        <meta name="twitter:image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        <meta itemprop="name" content="Términos y condiciones">
        <meta itemprop="description" content="Página donde se encuentran los términos y condiciones de compra.">
        <meta itemprop="image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        
        <link rel="shortcut icon" href="../favicon.ico?2"> 
        
        <link rel="stylesheet" href="../global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/css/main.css?2" type="text/css">
        
        <style type="text/css">
		.main_title
		{
			text-align:center;
			margin: 10px 0 20px 0;
			color: rgba(38, 116, 183, 1);
			font-size:20px;
		}
		h4
		{
			margin:15px 0 15px 15px;
		}
		
		.tosbox
		{
			margin:25px auto 35px;
			width:90%;
			font-size:15px;
		}
		</style>
        
		<script type="text/javascript" src="../global_scripts/js/jquery-1.8.3.min.js"></script>     
        <script type="text/javascript" src="../global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>       
		<script type="text/javascript" src="../resources/js/global-scripts.js?2"></script>

    </head>
    
    <body>

		<?php require_once("../global_scripts/php/header.php"); ?>
        
        <div class="wrapper">
        	
            <div class="main_content">
                
                <h3 class="main_title">Términos y condiciones del servicio</h3>
                
                <div class="tosbox">
                    <div style="font-weight:bold; text-align:center;">
                        Al usar este servicio usted acepta los siguientes términos y condiciones.<br />
                        <span style="font-size:12px;">(Actualizado el 22/08/2019)</span>
                    </div>
                    <ol>
                        <h4>Términos generales</h4>

                        <li>SteamBuy ofrece un servicio de reventa a pedido de licencias de software de videojuegos (productos) de la plataforma de Steam. Esto es, SteamBuy realiza la compra del mismo en la tienda de Steam y lo envía al comprador.</li>
                        <li>El precio de venta final de cada producto ofrecido por SteamBuy al público, es un precio en pesos argentinos, determinado por el precio final del producto en la tienda de Steam (convertido a pesos en caso que fuera en dólares) más una comisión fija determinada por SteamBuy.</li>
                        <li>Los pedidos se realizan desde el formulario de compra de juegos, en donde el comprador debe ingresar sus datos de contacto junto con la URL del producto deseado en la tienda de Steam.</li>
                        <li>Al ingresar la URL en el formulario, el sitio obtendrá y mostrará los datos del producto (nombre y precio base) e intentará realizar una cotización del precio final del mismo a partir del precio de base. <strong>El pedido será válido única y exclusivamente si el producto existe en Steam con el precio de base obtenido inicialmente</strong>, de lo contrario el pedido se cancelará, y en caso de ser abonado, se deberá realizar un reembolso o cambio de productos.</li>
                        <li>Adicionalmente SteamBuy vende productos en Stock (no de reventa) en el catálgo de productos, donde la operatoria de compra es la misma que por el formulario.</li>

                        <h4>Envío de productos</h4>

                        <li>Los envíos de los productos se realizan una vez que el pago de su pedido está acreditado, normalmente entre el mismo día de la acreditación y hasta dentro los siguientes 2 días.</li>
                        <li>Los productos son enviados en formato Regalo de Steam (Steam Gift) hacia la cuenta de Steam indicada por el comprador al realizar el pedido en el formulario de compra. Para esto, <strong>previamente se debe vincular como "amigo de steam" a la cuenta de Steam proporcionada por SteamBuy que envia el juego y la cuenta del comprador que recibirá el juego</strong>. Los productos que no puedan ser enviados de esta manera no pueden ser vendidos por SteamBuy, a excepción de la venta de productos en Stock realizada desde el catálogo de productos.</li>
                        <li>La cuenta de Steam proporcionada por el comprador para enviar el producto no debe tener una copia del mismo en su biblioteca de juegos, debido a que Steam no permite enviar un juego puntual a una cuenta que ya posee este juego. En caso que suceda esto se debe proveer de otra cuenta o cambiar el producto.</li>
                        <li>Una vez recibido un producto en formato "Steam Gift" a una cuenta de Steam, el receptor puede: agregarlo a su biblioteca de juegos o rechazarlo. Se recomienda no rechazarlo debido a que se debe hacer el envío nuevamente de ese u otro producto, y se pueden perder ofertas que pudieran haber habido.</li>

                        <h4>Ofertas</h4>

                        <li>La tienda de Steam frecuentemente realiza ofertas de tiempo limitado de sus productos, y estas se reflejan en el cotizador del formulario de compra de SteamBuy. Sin embargo, <strong>es necesario que al menos 2 horas antes de la finalización de la oferta, el pago del producto esté acreditado y que se haya hecho la vinculación como "amigos de steam" a las cuentas de SteamBuy y del comprador</strong>.</li>
                        <li>En caso que lo anterior no se cumpla, y se pierda una oferta, el comprador deberá solicitar un cambio de productos con su saldo a favor, pagando la diferencia si la hubiera.</li>

                        <h4>Reembolsos y cambios</h4>

                        <li>Si el producto comprado es en formato "Regalo de Steam", se puede realizar el reembolso de un producto únicamente si se cumplen para dicho producto las <a href="https://store.steampowered.com/steam_refunds/?l=spanish" target="_blank">condiciones de reembolso</a> de la tienda de Steam, en el apartado "REEMBOLSOS EN REGALOS". En ese caso, SteamBuy puede realizar un cambio de productos, teniendo el comprador que abonar la diferencia de precio del nuevo producto si hubiera alguna. <strong>Si las condiciones de reembolso de Steam no se cumplen, no será posible hacer el reembolso.</strong></li>
                        <li>Si el producto comprado es en formato clave de activación, no se admiten reembolsos ni cambios una vez enviado el producto.</li>


                        <h4>Privacidad</h4>
                        <li>SteamBuy almacena el nombre y apellido y el e-mail del comprador, junto con los datos del pedido. Estos datos no se comparten con ninguna otra persona o empresa.</li>
                    </ol>  
            	</div>
            
            </div><!-- End main content -->
            
        	<?php require_once("../global_scripts/php/footer.php"); ?>
        	
        </div><!-- End container -->
    </body>
    
    
</html>