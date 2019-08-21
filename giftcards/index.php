<?php
session_start();

define("ROOT_LEVEL", "../");

header("Content-Type: text/html; charset=UTF-8");

require_once("../global_scripts/php/client_page_preload.php");
require_once("../global_scripts/php/admlogin_functions.php");
require_once("../global_scripts/php/purchase-functions.php");
require_once "resources/php/giftcard-pg-functions.php";


$admin = false;
if(isAdminLoggedIn())
{
	$admin = true;
}



$sql = "SELECT * FROM `products_giftcards` WHERE `stock` > 0 ORDER BY `usd_ammount` ASC";
$query = mysqli_query($con, $sql);


$giftcard_ammount = array(1=>0, 2=>0, 3=>0, 4=>0);
$giftcard_catalog = array(1=>"", 2=>"", 3=>"", 4=>"");

while($gcardData = mysqli_fetch_assoc($query)) {

	$giftcard_ammount[$gcardData["type"]] += 1;
	$giftcard_catalog[$gcardData["type"]] .= giftcard_catalog_product($gcardData);
	
}




?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" itemscope itemtype="http://schema.org/Article">
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>Tarjetas de regalo - SteamBuy</title>
        
        <meta name="description" content="Catálogo de tarjetas de regalo">
        <meta name="keywords" content="steam,playstation,xbox,gift,card,tarjeta,regalo,saldo,wallet">
        
        <meta property="og:title" content="Tarjetas de regalo - SteamBuy" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://steambuy.com.ar" />
        <meta property="og:image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg" />
        <meta property="og:site_name" content="SteamBuy" />
        <meta property="og:description" content="Catálogo de tarjetas de regalo" />
        
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="http://steambuy.com.ar">
        <meta name="twitter:title" content="Tarjetas de regalo - SteamBuy">
        <meta name="twitter:description" content="Catálogo de tarjetas de regalo">
        <meta name="twitter:image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg">
        
        <meta itemprop="name" content="Tarjetas de regalo - SteamBuy">
        <meta itemprop="description" content="Catálogo de tarjetas de regalo">
        <meta itemprop="image" content="http://steambuy.com.ar/global_design/img/logo-complete-meta.jpg"-->
        
        
        <link rel="shortcut icon" href="../favicon.ico?2"> 
     
        <link rel="stylesheet" href="../global_design/font-awesome-4.1.0/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/bootstrap-3.1.1/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="../global_design/css/main.css?2.01" type="text/css">
        <link rel="stylesheet" href="resources/css/giftcards-page.css" type="text/css">

		<script type="text/javascript" src="../global_scripts/js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="../global_design/bootstrap-3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../resources/js/global-scripts.js"></script>

    </head>
    
    <body>
    
		<?php require_once("../global_scripts/php/header.php"); ?>
        
        <div class="wrapper">
        	
            <div class="main_content">


                <div class="clearfix tab-container">
                
					<!-- Nav tabs -->
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                    	<?php
						if($giftcard_ammount[1] > 0) {
							echo "<li role='presentation' class='active'><a href='#steamwallet' aria-controls='steamwallet' role='tab' data-toggle='tab'>Steam Wallet cards</a></li>";
						}
						if($giftcard_ammount[2] > 0) {
							echo "<li role='presentation'><a href='#playstation' aria-controls='playstation' role='tab' data-toggle='tab'>Playstation Store</a></li>";
						}
						if($giftcard_ammount[3] > 0) {
							echo "<li role='presentation'><a href='#xbox' aria-controls='xbox' role='tab' data-toggle='tab'>Xbox</a></li>";
						}
						if($giftcard_ammount[4] > 0) {
							echo "<li role='presentation'><a href='#ncoin' aria-controls='ncoin' role='tab' data-toggle='tab'>NCSoft NCoin</a></li>";
						}
						?>
                    </ul>
                    
                      <!-- Tab panes -->
                    <div class="tab-content"> 
                        <?php
						if($giftcard_ammount[1] > 0) {
							echo "<div role='tabpanel' class='tab-pane active' id='steamwallet'>".$giftcard_catalog[1]."</div>";
						}
						if($giftcard_ammount[2] > 0) {
							echo "<div role='tabpanel' class='tab-pane' id='playstation'>".$giftcard_catalog[2]."</div>";
						}
						if($giftcard_ammount[3] > 0) {
							echo "<div role='tabpanel' class='tab-pane' id='xbox'>".$giftcard_catalog[3]."</div>";
						}
						if($giftcard_ammount[4] > 0) {
							echo "<div role='tabpanel' class='tab-pane' id='ncoin'>".$giftcard_catalog[4]."</div>";
						}
						?>
                    </div>
                
                </div>



            </div><!-- End main content -->
            
        	<?php require_once("../global_scripts/php/footer.php"); ?>
        	
        </div><!-- End container -->
    </body>
    
    
</html>