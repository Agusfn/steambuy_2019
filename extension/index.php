<?php
session_start();

define("ROOT_LEVEL", "../");

header("Content-Type: text/html; charset=UTF-8");

require_once("../global_scripts/php/client_page_preload.php");
require_once("../global_scripts/php/admlogin_functions.php");
require_once("../global_scripts/php/purchase-functions.php");
require_once("../resources/php/catalog_functions.php");


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
        
        <title>Extensión para el navegador - SteamBuy</title>
        
        <meta name="description" content="Extensión para comprar juegos en Steam con medios de pago en efectivo para Argentina.">
        <meta name="keywords" content="juegos,comprar,tarjeta,crédito,steam,amazon,humblebundle,bundlestars,rapipago,pago fácil,ripsa,counter strike,oferta,descuento,PayPal">
        
        <meta property="og:title" content="Extensión para el navegador - SteamBuy" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://steambuy.com.ar" />
        <meta property="og:image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg" />
        <meta property="og:site_name" content="SteamBuy" />
        <meta property="og:description" content="Extensión para comprar juegos en Steam con medios de pago en efectivo para Argentina." />
        
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="https://steambuy.com.ar">
        <meta name="twitter:title" content="Extensión para el navegador - SteamBuy">
        <meta name="twitter:description" content="Extensión para comprar juegos en Steam con medios de pago en efectivo para Argentina.">
        <meta name="twitter:image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        <meta itemprop="name" content="Extensión para el navegador - SteamBuy">
        <meta itemprop="description" content="Extensión para comprar juegos en Steam con medios de pago en efectivo para Argentina.">
        <meta itemprop="image" content="https://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        
        <link rel="shortcut icon" href="../favicon.ico?2"> 
     
        <link rel="stylesheet" href="../global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/css/main.css?2.02" type="text/css">

        <style type="text/css">
            
            .ext-panel {
                padding: 20px;
                background-color: #444;
                color: #EEE;
            }

            .ext-panel a {
                color: #EEE;
            }


        </style>


		<script type="text/javascript" src="../global_scripts/js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="../global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../resources/js/global-scripts.js"></script>


    </head>
    
    <body>
        

		<?php require_once("../global_scripts/php/header.php"); ?>
        
        <div class="wrapper">
        	
            <div class="main_content">
		  
                <div style="margin: 10px 30px">
                    <h3>Extensión SteamBuy para el navegador</h3>


                    <div class="ext-panel" style="margin-top: 40px">
                        <h4><a href="https://addons.mozilla.org/es/firefox/addon/extension-steambuy/" target="_blank"><i class="fa fa-firefox" aria-hidden="true"></i> Descargar para Mozilla Firefox</a></h4>
                    </div>

                    <div class="ext-panel" style="margin-top: 20px">
                        <h4><a href="sb_extension_0.0.2.crx"><i class="fa fa-opera" aria-hidden="true"></i> Descargar para Opera</a></h4>
                        <p style="margin-top: 10px">Se debe arrastrar el archivo .crx descargado al navegador, y confirmar la instalación.</p>
                    </div>

                    <p style="margin-top: 40px">Explorá la tienda de Steam desde tu navegador y cotizá los precios de los juegos en efectivo (Rapipago, Pago Fácil, Ripsa, Cobroexpress, etc), con la opción de compra por medio del servicio de SteamBuy.</p>

                    <p>Además, agrega un indicador del precio final en pesos en caso de comprar el juego directamente a Steam con tarjeta de crédito, sumando el 21% de IVA impuesto recientemente para compras con tarjeta.</p>


                    <div style="width: 850px;margin: 30px auto">
                        <img src="ext.jpg" style="width: 100%" alt="Cuadro de compra del producto">
                    </div>

                    <p style="margin: 20px 0">Hacer click en el botón de compra dirige al formulario de compra de juegos con la URL cargada:</p>

                    <div style="width: 750px;margin: 20px auto">
                        <img src="ext2.jpg" style="width: 100%" alt="Cuadro de compra del producto">
                    </div>



                    <p style="margin-top: 30px">Muy pronto disponible en Google Chrome</p>

                </div>





            </div><!-- End main content -->
            
        	<?php require_once("../global_scripts/php/footer.php"); ?>
        	
        </div><!-- End container -->
    </body>
    
    
</html>