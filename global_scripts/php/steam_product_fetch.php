<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");


/*
Funcion usando api steam storefront.
region: BR, US, AR, RU, MX, etc
*/

function ssf_getpriceinfo($producturl, $region = "ar") {

	$result["error"] = 0; // Errores. 1=mal link, 2=error en la solicitud, 3=producto inexistente/otro
	
	if(preg_match("#^(https?://)?store\.steampowered\.com/(sub|app)/([0-9]{1,10})(/.*)?$#", $producturl, $matches) && is_numeric($matches[3])) {
		$steamid = $matches[3];
		$type = $matches[2];

		$response = @file_get_contents("http://store.steampowered.com/api/".($type=="app" ? "appdetails/?appids=" : "packagedetails/?packageids=").$steamid."&cc=".$region."&l=spanish".($type=="app"?"&filters=price_overview":""));
		if($response != false) {
			$data = json_decode($response, true);
			//var_dump($data);
			if($data[$steamid]["success"] == true && isset($data[$steamid]["data"]["price".($type=="app"?"_overview":"")])) {
				$result["firstprice"] = roundfunc(intval($data[$steamid]["data"]["price".($type=="app"?"_overview":"")]["initial"]) / 100);
				$result["finalprice"] = roundfunc(intval($data[$steamid]["data"]["price".($type=="app"?"_overview":"")]["final"]) / 100);
				$result["currency"] = $data[$steamid]["data"]["price".($type=="app"?"_overview":"")]["currency"];
			} else $result["error"] = 3;
		} else $result["error"] = 2;
	} else $result["error"] = 1;
	return $result;
}




// API web scrapping
class SteamProduct 
{

	public $success;
	public $errorText;

	public $productUrl;
	public $productType; // "app" or "sub"
	public $productId; // id of app or sub
	public $pageHtml;
	public $xpath;
	
	public $name;
	public $discount;
	public $firstPrice;
	public $finalPrice;
	public $discountEndTime;


	public function __construct($productUrl) 
	{
		$this->productUrl = $productUrl;

		if($this->loadHtml() && $this->loadProductName() && $this->loadPrices()) {
			$this->success = true;
		}
		else {
			// log ?
			$this->success = false;
		}
	}
	

	/**
	 * Load the product page HTML.
	 * @return boolean [description]
	 */
	public function loadHtml()
	{
		if(!preg_match("#^(https?://)?store\.steampowered\.com/(sub|app)/([0-9]{1,10})(/.*)?$#", $this->productUrl, $matches)) {
			$this->errorText = "No se pudo cargar la página HTML del producto.";
			return false;
		}

		$this->productType = $matches[2];
		$this->productId = $matches[3];

		$htmlPage = $this->getHtmlPage("http://store.steampowered.com/".$this->productType."/".$this->productId."/?cc=ar&l=spanish");

		if(!$htmlPage) {
			$this->errorText = "No se pudo cargar la página HTML del producto.";
			return false;
		}

		$this->pageHtml = $htmlPage;

		$dom = new DOMDocument();
		@$dom->loadHTML($htmlPage);
		$this->xpath = new DOMXPath($dom);

		return true;
	}


	/**
	 * Load the product name.
	 * @return boolean [description]
	 */
	public function loadProductName() 
	{
		if($this->productType == "app") {

			$title = $this->xpath->query("//div[@class='apphub_AppName']");

			if($title->length == 1) {
				$this->name = trim($title->item(0)->textContent);
				return true;
			}
			$this->errorText = "No se encontró el nombre del producto.";
			return false;
		}
		else {
			$title = $this->xpath->query("//h2[@class='pageheader']");
			
			if($title->length == 1) {
				$this->name = trim(utf8_decode($title->item(0)->textContent));
	
				if($this->name == "Ups... ¡Perdón!") {
					$this->errorText = "El producto parece no haberse cargado correctamente.";
					return false;
				}
				return true;
			}
			$this->errorText = "No se encontró el nombre del producto.";
			return false;
		}
	}


	/**
	 * Load product prices (in ARS)
	 * @return boolean [description]
	 */
	public function loadPrices() 
	{
		// En oferta
		$prices = $this->xpath->query("(//div[@class='game_area_purchase_game'])[1]//div[@class='discount_prices']/div"); 
		
		if($prices->length == 2) {
			
			$this->discount = true;	
			$this->firstPrice = $this->trimAndRoundPrice($prices->item(0)->textContent);
			$this->finalPrice = $this->trimAndRoundPrice($prices->item(1)->textContent);

			if(preg_match("#InitDailyDealTimer\( [$]DiscountCountdown, ([0-9]{10})#", $this->pageHtml, $matches)) {
				$this->discountEndTime = date("Y-m-d H:i:s", $matches[1]);
			} 
			return true;
		} 
		else {
			// Sin oferta
			$price = $this->xpath->query("(//div[@class='game_area_purchase_game'])[1]//div[@class='game_purchase_price price']");

			if($price->length == 1) { 

				$this->discount = false;
				$this->finalPrice = $this->trimAndRoundPrice($price->item(0)->textContent);
				$this->firstPrice = $this->finalPrice;

				if($this->finalPrice == 0) {
					$this->errorText = "El precio del juego cargado es cero.";
					return false;
				}

				return true;
			} 
			else {
				$this->errorText = "No se encontró el precio del juego.";
				return false;
			}
		}
	}
	
		
	/**
	 * Sanitize fetched prices. Removes all but numbers and commas, passes to float, and rounds.
	 * @param  [type] $price [description]
	 * @return [type]        [description]
	 */
	private function trimAndRoundPrice($price)
	{
		$trimmedPrice = preg_replace("/[^0-9,]/", "", $price); // with commas as decimal separator.
		$trimmedPrice = floatval(str_replace(",", ".", $trimmedPrice)); // dot as decimal
		return round($trimmedPrice * 2, 1) / 2;
	}


	/**
	 * Obtain HTML text from page url
	 * @param  string $url
	 * @return string|false
	 */
	private function getHtmlPage($url)
	{
		$ch = curl_init();
		$timeout = 15;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13");
		curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
		curl_setopt($ch, CURLOPT_COOKIE, "birthtime=652950001;lastagecheckage=1-January-1900;mature_content=1");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
		
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
}


?>