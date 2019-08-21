<?php
require_once("PHPMailer/PHPMailerAutoload.php");
require_once("Log.php");
require_once("Mailer.php");
require_once("TurismoCitySearch.php");


/* Criterio de obtención de listas y notificación */

const FROM = "AEP"; // ezeiza o aeroparque.

const USD_TO_ARS = 39;

/* Destinos
 * Se hace una solicitud y revisión por cada uno. 
 * Cualquier vuelo que cumpla estas condiciones se notifica por e-mail instantáneamente.
 */
$destinations = [

	["code" => "MIA", "ref_name" => "Miami", "max_price_usd" => 600],
	/*["code" => "NYC", "max_price_usd" => 400], // nueva york
	["code" => "SFB", "max_price_usd" => 400], // orlando
	["code" => "CUN", "max_price_usd" => 300], // cancun
	["code" => "BRC", "max_price_usd" => 85, "date_start" => "2019-07-20", "date_end" => "2019-08-20"] // bariloche*/

];



foreach($destinations as $destination)
{
	$search = new TurismoCitySearch();
	$search->setOrigin(FROM);
	$search->setDestination($destination["code"]);

	if(isset($destination["date_start"]))
		$search->setDateStart($destination["date_start"]);

	if(isset($destination["date_end"]))
		$search->setDateEnd($destination["date_end"]);

	if(isset($destination["max_price_usd"]))
		$search->setMaxPrice($destination["max_price_usd"]);

	if(isset($destination["min_stay_duration"]))
		$search->setMinStayDuration($destination["min_stay_duration"]);	

	if(isset($destination["max_stay_duration"]))
		$search->setMaxStayDuration($destination["min_stay_duration"]);


	$flights = $search->getFlights();

	if($flights === false)
		continue;


	if(sizeof($flights) > 0)
	{
		
		$min_price_usd = null;
		foreach($flights as $flight)
		{
			if($min_price_usd === null)
				$min_price_usd = $flight["price"];
			else
			{
				if($flight["price"] < $min_price_usd)
					$min_price_usd = $flight["price"];
			}
		}

		Mailer::sendNotification($destination["ref_name"], $min_price_usd, $min_price_usd*USD_TO_ARS, $flights);

	}


}

