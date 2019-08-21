<?php


class TurismoCitySearch
{


	const API_URL = "https://api.turismocity.com/graphql-cdn";


	/**
	 * Código departureIata
	 * @var string
	 */
	private $origin;

	/**
	 * Código arrivalIata
	 * @var string
	 */
	private $destination;

	/**
	 * Fecha inicio búsqueda (YYYY-MM-DD)
	 * @var string
	 */
	private $search_date_start;

	/**
	 * Fecha fin búsqueda (YYYY-MM-DD)
	 * @var string
	 */
	private $search_date_end;

	/**
	 * Precio máximo en USD
	 * @var int
	 */
	private $max_price_usd;

	/**
	 * Cant mín de días
	 * @var int
	 */
	private $min_stay_duration;

	/**
	 * Cant máx de días
	 * @var int
	 */
	private $max_stay_duration;

	/**
	 * Mensaje de error al realizar la búsqueda.
	 * @var string
	 */
	private $error_text;


	/**
	 * Codigo de respuesta http de la ultima solicitud
	 * @var int
	 */
	private $http_resp_code;



	public function setOrigin($origin) {
		$this->origin = $origin;
	}

	public function setDestination($destination) {
		$this->destination = $destination;
	}

	public function setDateStart($date) {
		$this->search_date_start = $date;
	}

	public function setDateEnd($date) {
		$this->search_date_end = $date;
	}

	public function setMaxPrice($price) {
		$this->max_price_usd = $price;
	}

	public function setMinStayDuration($days) {
		$this->min_stay_duration = $days;
	}

	public function setMaxStayDuration($days) {
		$this->max_stay_duration = $days;
	}


	public function errorText() {
		return $this->error_text;
	}


	public function getFlights()
	{

		if($this->origin == null || $this->destination == null) {
			$this->error_text = "Asigna origen y destino.";
			return false;
		}

		$response = $this->makeGetRequest(self::API_URL.$this->generateUrlParams());

		if($response === false)
			return false;



		return $response["data"]["flights"];

	}





	/**
	 * Generar parámetros url GET para la búsqueda a partir de los datos de búsqueda.
	 * @return null
	 */
	private function generateUrlParams() 
	{
		$query_param = 'query=query($iso:+String!,+$arrivalIata:+String!,+$departureIata:+String,+$month:+Int,+$year:+Int,+$dateStart:+String,+$dateEnd:+String,+$stops:+Boolean,+$maxStayDuration:+Int,+$minStayDuration:+Int,+$minPrice:+Int,+$maxPrice:+Int,+$onlyParentIatas:+Boolean,+$oneWay:+Boolean)+{+flights:+cheapflights(+iso:+$iso+arrivalIata:+$arrivalIata+departureIata:+$departureIata+month:+$month+year:+$year+dateStart:+$dateStart+dateEnd:+$dateEnd+directFlights:+$stops+maxStayDuration:+$maxStayDuration+minStayDuration:+$minStayDuration,+minPrice:+$minPrice,+maxPrice:+$maxPrice,+onlyParentIatas:+$onlyParentIatas+limit:+0,+oneWay:+$oneWay+)+{+id+arrival+departure+dias+beststops+inbound+outbound+minutesoutbound+minutesinbound+price+airline+airlinename+}+pricesLimits:+cheapFlightsPricesLimits(+iso:+$iso+arrivalIata:+$arrivalIata+departureIata:+$departureIata+oneWay:+$oneWay+)+{+minPrice+maxPrice+}+monthsBestPrices:+cheapFlightsMonthsFeed(+iso:+$iso,+arrivalIata:+$arrivalIata+departureIata:+$departureIata+oneWay:+$oneWay+directFlights:+$stops+maxStayDuration:+$maxStayDuration+minStayDuration:+$minStayDuration+)+{+id+minprice+month+year+}+}';


		$variables = [
			"iso" => "AR",
			"arrivalIata" => $this->destination,
			"departureIata" => $this->origin,
			"stops" => false, // false = con escalas
			"onlyParentIatas" => true,
			"anyMulti" => false,
			"oneWay" => false,
			"type" => "ROUNDTRIP"
		];

		if($this->search_date_start != null)
			$variables["dateStart"] = $this->search_date_start;
		if($this->search_date_end != null)
			$variables["dateEnd"] = $this->search_date_end;

		if($this->max_stay_duration != null)
			$variables["maxStayDuration"] = $this->max_stay_duration;
		if($this->min_stay_duration != null)
			$variables["minStayDuration"] = $this->min_stay_duration;

		if($this->max_price_usd != null)
			$variables["maxPrice"] = $this->max_price_usd;



		$variables_param = "variables=".json_encode($variables);


		return "?".$query_param."&".$variables_param;

	}


	/**
	 * Realiza solicitud HTTP GET
	 * @param  string $url
	 * @return array|false
	 */
	private function makeGetRequest($url)
	{

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');


		$response = curl_exec($ch); // devuelve datos o FALSE

		if($response !== false) // success
		{
			$this->http_resp_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if($this->http_resp_code != 200) 
			{
				$this->error_text = "Http response ".$this->http_resp_code;
				Log::notice("HTTP Response ".$this->http_resp_code.". Params: ".explode("variables=",$url)[1]."\r\nResponse:\r\n".$response);
				return false;
			} 
			else
				Log::info("HTTP Response ".$this->http_resp_code.". Params: ".explode("variables=",$url)[1]);

			return json_decode($response, true);

		}
		else // failed
		{
			$this->error_text = curl_error($ch);
			Log::notice("Error realizando solicitud http get. Texto: ".$this->error_text);
			curl_close($ch);
			return false;
		}

	}



}