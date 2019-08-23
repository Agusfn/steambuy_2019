<?php
session_start();

define("ROOT_LEVEL", "");

header("Content-Type: text/html; charset=UTF-8");

require_once("global_scripts/php/client_page_preload.php");
require_once("global_scripts/php/admlogin_functions.php");
require_once("global_scripts/php/purchase-functions.php");
require_once("resources/php/catalog_functions.php");


$admin = false;
if(isAdminLoggedIn())
{
	$admin = true;
}


// si hay un evento de ofertas de steam, esto lo que hace es agregar un expositor de juegos en la página ppal
$steam_sales_event = false;
$steam_sales_featured_items = 9;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" itemscope itemtype="http://schema.org/Article">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>Tienda de SteamBuy</title>
        
        <meta name="description" content="SteamBuy es una tienda donde encontrarás una gran variedad de juegos digitales para PC con medios de pago accesibles.">
        <meta name="keywords" content="juegos,comprar,tarjeta,crédito,steam,amazon,humblebundle,bundlestars,rapipago,pago fácil,ripsa,counter strike,oferta,descuento,PayPal">
        
        <meta property="og:title" content="Tienda de SteamBuy" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://steambuy.com.ar" />
        <meta property="og:image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg" />
        <meta property="og:site_name" content="SteamBuy" />
        <meta property="og:description" content="SteamBuy es una tienda donde encontrarás una gran variedad de juegos digitales para PC con medios de pago accesibles." />
        
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="http://steambuy.com.ar">
        <meta name="twitter:title" content="Tienda de SteamBuy">
        <meta name="twitter:description" content="SteamBuy es una tienda donde encontrarás una gran variedad de juegos digitales para PC con medios de pago accesibles.">
        <meta name="twitter:image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        <meta itemprop="name" content="Tienda de SteamBuy">
        <meta itemprop="description" content="SteamBuy es una tienda donde encontrarás una gran variedad de juegos digitales para PC con medios de pago accesibles.">
        <meta itemprop="image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        
        <link rel="shortcut icon" href="favicon.ico?2"> 
     
        <link rel="stylesheet" href="global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="global_design/css/main.css?2.01" type="text/css">
        <link rel="stylesheet" href="design/css/main_page.css?3" type="text/css">

		<script type="text/javascript" src="global_scripts/js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="resources/js/global-scripts.js"></script>
		<script type="text/javascript" src="resources/js/main-page.js"></script>
		<script type="text/javascript" src="resources/js/price-inpt-fnc.js"></script>

    </head>
    
    <body>
        

		<?php require_once("global_scripts/php/header.php"); ?>
        
        <div class="wrapper">
        	
            <div class="main_content">


                <div class="panel panel-default" style="margin-bottom: 40px">
                    <div class="panel-body" style="padding: 35px 40px">
                    
                        <form action="procesar_formulario.php" method="POST" id="buy_game_form">
                            <h3 style="margin-bottom: 25px">Formulario de compra de juegos de Steam</h3>


                            <h1 style="margin-bottom: 10px; font-size: 15px">Compra juegos de Steam por cupón de pago en pesos argentinos (Rapipago, Pago Fácil, etc).</h1>
                            <h5 style="margin-bottom: 30px;">Una vez abonado el cupón, te enviaremos el juego como Steam Gift.</h5>

                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Nombre y apellido</label>
                                        <input type="text" class="form-control" name="buyer_name">
                                        <span class="help-block" style="display: none;"></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>E-mail contacto</label>
                                        <input type="text" class="form-control" name="buyer_email">
                                        <span class="help-block" style="display: none;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>URL de juego en Steam <i class="fa fa-spinner fa-spin fa-fw" id="url_loading_spinner" style="display: none;"></i></label>
                                        <input type="text" class="form-control" name="game_url" placeholder="Ej: https://store.steampowered.com/app/220/HalfLife_2/">
                                        <span class="help-block" style="display: none;"></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>URL cuenta Steam que recibirá el juego <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="top" title="La cuenta NO debe tener el juego actualmente. El juego NO es transferible."></span></label>
                                        <input type="text" class="form-control" name="buyer_account_url" placeholder="Ej: https://steamcommunity.com/id/gabelogannewell">
                                        <span class="help-block" style="display: none;"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-6">
                                    <h4 style="margin-bottom: 10px">
                                        Juego: <span id="game_name"></span> <span class="insert-url-label">Ingresar URL del juego</span>
                                    </h4>
                                    <h5 style="margin-bottom: 10px">
                                        <span style="text-decoration: underline dotted" data-toggle="tooltip" data-placement="top" title="Precio pagando en Steam con tarjeta crédito (inc. IVA 21% gobierno)">Precio en Steam:</span> <span id="game_steam_real_price"></span> <span class="insert-url-label">Ingresar URL del juego</span>
                                    </h5>
                                    <h4 style="margin-bottom: 10px">
                                        Precio final: <span id="game_final_ars_price" style="color: #2772bf"></span> <span class="insert-url-label">Ingresar URL del juego</span>
                                    </h4>
                                    <a href="soporte/preguntas-frecuentes/#como-funcionan-las-ofertas" target="_blank"><span class="label label-info" id="limited_time_offer_warning" style="display: none;">Juego en oferta de tiempo limitado, se debe pagar antes de que termine.</span></a>
                                </div>

                                <div class="col-xs-6" style="padding-top: 40px">

                                    <div style="text-align: right;">
                                        <label><input type="checkbox" id="terms_checkbox"> Acepto los <a href="condiciones" target="_blank">términos y condiciones de compra</a>.</label>
                                        <button type="button" class="btn btn-primary" id="buy_form_submit">Generar pedido y boleta de pago&nbsp;&nbsp;<span class="glyphicon glyphicon-barcode"></span></button>
                                    </div>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>




				<div class="catalog-panel" style="margin-bottom:30px;">
                	
                    <div class="cp-top">
                    	<div class="cp-title">LIQUIDACIÓN STOCK<a href="juegos/"><div class="cp-viewmore">Ver todo</div></a></div>
                    </div>
                    
                    <div class="cp-content">

                        <div id="carousel-relevant" class="carousel slide" data-ride="carousel" data-interval="10000">
                            <div class="carousel-inner" role="listbox">

                                <?php
        						$filas = 5;
        						$paginas = 3;
        						$prod_por_pag = $filas*4; // 4 columnas
        						$max_productos = $prod_por_pag * $paginas;

        						$sql = "SELECT ".$needed_product_data." FROM `products` WHERE ".$basic_product_filter." AND `product_has_limited_units` = 1 AND `product_limited_units` > 0 AND (`product_sellingsite` = 1 OR `product_sellingsite` = 2 OR `product_sellingsite` = 5) ORDER BY `product_rating` DESC LIMIT ".$max_productos;

        						$res = mysqli_query($con, $sql);
        						$result_ammount = mysqli_num_rows($res);
        						
                                $displayed = 0;
                                while($pData = mysqli_fetch_assoc($res)) 
                                {
        							if($displayed >= $max_productos) break;
        							
        							$displayed++;
        							$displayedProducts[] = $pData["product_id"];
        							if(is_int(($displayed-1)/$prod_por_pag)) {
        								echo "<div class='item".($displayed==1?" active":"")."'>";	
        							}
        							
        							display_catalog_product($pData);
        							
        							if(is_int($displayed/$prod_por_pag)) {
        								echo "</div>";	
        							} else if(!is_int($displayed/$prod_por_pag) && $displayed == $result_ammount) { // Si es el último elemento de la consulta y no el último de la página, se rellena y se cierra el .item
        								$items_restantes = $prod_por_pag - ($displayed % $prod_por_pag);
        								for($i=0;$i<$items_restantes;$i++) {
        									display_catalog_product(false, "sm");
        								}
        								echo "</div>";
        							}
                                }
                                ?>

                            </div>
                        </div>

                    </div>

                    <div class="cp-bottom">
                        <span class="cp-carousel-pagination">0/0</span>
                        <span class="cp-carousel-pag-controls">
                            <a href="#carousel-relevant" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a><a href="#carousel-relevant" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
                        </span>
                    </div>

                </div>

                <?php /*
                <div class="clearfix">

                    <div class="left-column">

                        <div class="catalog-panel" style="margin-top:25px;">
                            <div class="cp-top">
                                <div class="cp-title">Otros productos</div>
                            </div>
                            <div class="cp-content">
                                <?php
                                $filas = 3;
                                $cant_productos = $filas * 3; // 3 columnas
                                
                                $sql = "SELECT ".$needed_product_data." FROM products WHERE ".$basic_product_filter." AND ((`product_sellingsite` = 3 OR `product_sellingsite` = 4) OR (`product_sellingsite` = 2 AND `product_limited_units` = 0)) LIMIT ".($cant_productos);
                                
                                $query = mysqli_query($con, $sql);
                                $i = 0;
                                while($pData = mysqli_fetch_assoc($query)) {
                                    
                                    if($i <$cant_productos// && !in_array($pData["product_id"],$displayedProducts)//) {
                                        $i++;
                                        //$displayedProducts[] = $pData["product_id"];
                                        display_catalog_product($pData, "sm");                                  
                                    }
                                    
                                }
                                ?> 
                            </div>
                        </div>

                    </div>


                    <div class="right-column">
                        
                        <?php
                        $sql = "SELECT * FROM `products_giftcards` WHERE `stock` > 0 ORDER BY `relevance` DESC LIMIT 3";
                        $query = mysqli_query($con, $sql);
                        if(mysqli_num_rows($query) > 0) {
                            ?>
                            <div class="catalog-panel" style="margin-bottom:30px;">
                                <div class="cp-top-short">
                                    <div class="cp-title">Gift cards populares<a href="giftcards/"><div class="cp-viewmore">Ver todas</div></a></div>
                                </div>
                                <div class="cp-content" style="height:237px;border-bottom:1px solid #AAA;">
                                    <?php
                                    while($gcardData = mysqli_fetch_assoc($query)) {
                                        ?>
                                        <a href="comprar/pago.php?type=2&p_id=<?php echo $gcardData["id"]; ?>" style="text-decoration:none !important;"><div class="cpl-product">
                                            <div style="float:left;">
                                                <img src="resources/css/img/giftcards/<?php
                                                if($gcardData["type"] == 1) echo "steam";
                                                else if($gcardData["type"] == 2) echo "playstation";
                                                else if($gcardData["type"] == 3) echo "xbox";
                                                else if($gcardData["type"] == 4) echo "ncoin";
                                                ?>.png" class="cpl-gftcrd-img">
                                                <div class="cpl-gftcrd-ammount"><span><?php echo $gcardData["usd_ammount"]; ?></span> USD</div>
                                            </div>
                                            <div class="cpl-gftcrd-name">
                                                <div><?php echo $gcardData["name"]; ?></div>
                                            </div>
                                            <div class="cpl-gftcrd-price">$<?php echo quickCalcGame(1, $gcardData["selling_price_usd"]); ?> <span>ARS</span></div>
                                        </div></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        } 
                        ?>
                        
                        <!--a style="text-decoration:none !important;" href="javascript:void(0);" data-toggle="modal" data-target="#game_form_modal"><div class="game-form-box" style="margin-bottom:20px">
                            Formulario de compra de juegos de Amazon
                        </div></a-->
                        

    
                    </div>

                </div>
                */
               ?>
						
            
            </div><!-- End main content -->
            
        	<?php require_once("global_scripts/php/footer.php"); ?>
        	
        </div><!-- End container -->
    </body>
    
    
</html>