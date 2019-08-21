<?php

function giftcard_catalog_product($gcardData) {
	ob_start();
	?>
	<a href="../comprar/pago.php?type=2&p_id=<?php echo $gcardData["id"]; ?>"><div class="catalog-element clearfix">
		<img src="../resources/css/img/giftcards/<?php
        if($gcardData["type"] == 1) echo "steam";
		else if($gcardData["type"] == 2) echo "playstation";
		else if($gcardData["type"] == 3) echo "xbox";
		else if($gcardData["type"] == 4) echo "ncoin";
		?>.png" class="giftcard-img">
		<div class="giftcard-ammt"><?php echo $gcardData["usd_ammount"] ?> <span style="font-size:14px">USD</span></div>
		<div class="giftcard-name"><?php echo $gcardData["name"] ?></div>
		<div class="giftcard-price">$<?php echo quickCalcGame(1, $gcardData["selling_price_usd"]); ?> <span style="font-size:14px">ARS</span></div>
	</div></a>
    <?php
	$html = ob_get_clean();
	return $html;
	
}




?>