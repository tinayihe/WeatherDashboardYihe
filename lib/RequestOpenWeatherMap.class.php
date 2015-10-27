<?php

/**
 * Class: RequestOpenWeatherMap
 * A class related to send the request to OWM API
 * 
 * @author Yihe WANG <tinayihe39@gmail.com>
 */
class RequestOpenWeatherMap {

	// Indicate the type of return value is associative 
	const FORCE_ASSOC = true;

	// URL components 
	const HOST_CITIES_WEATHER = 'http://api.openweathermap.org/data/2.5/group';
	const HOST_FIND_CITY = 'http://api.openweathermap.org/data/2.5/find';
	const APP_ID = 'b13acbfc7c9c7d450fca517153482eb0';

	/**
	 * Function: requestCityIdByName
	 * Send a request to search cities corresponding to the name of city
	 * which the user wants to follow.
	 * @param string $cityName the city name to search
	 * @return json object Number and details of cities  finded
	 */
	function requestCityIdByName($cityName) {  //find API
		$url = $this->buildURL(self::HOST_FIND_CITY, array(
			'q' => $cityName,
			'type' => 'like',
			'sort' => 'population',
			'cnt' => 10
		));
		$result = $this->request($url);
		return $result;
	}

	/**
	 * Function: requestCitiesWeather
	 * Send a request to get current weathers of cities followed by user.
	 * @param array $cities the list of cities followed by user
	 * @param string $units define the unit of temprature
	 * @return json array of objects details of cities weathers
	 */
	function requestCitiesWeather($cities, $units = 'metric') { //Degré Celsius par défault
		$params = $this->buildCitiesQueryParams($cities, $units); //paramètre dans la requête
		$url = $this->buildURL(
			self::HOST_CITIES_WEATHER,
			$params
		);
		$result = $this->request($url);
		return $result;
	}

	/**
	 * Function: buildCitiesQueryParams
	 * Build a array include the parameters of request.
	 * @param array $cities the list of cities followed by user
	 * @param string $units define the unit of temprature
	 * @return array the parameters of request
	 */
	function buildCitiesQueryParams($cities, $units) {
		$cities_id = '';
		$nb_cities = count($cities);
		foreach ($cities as $ind => $city) {
			$cities_id .= $city['city_id'];
			if ($ind != $nb_cities - 1) {
				$cities_id .= ',';
			}
		}

		return array(
			'id'    => $cities_id,
			'units' => $units
		);
	}

	/**
	 * Function: buildURL
	 * Build URL to request.
	 * @param array $params the parameters of request
	 * @param string $base prefix of URL
	 * @return string The API URL to request to
	 */
	function buildURL($base, $params = array()) {
		$params['APPID'] = self::APP_ID;
		$queryParams = http_build_query($params);

		// Decode url-encoded comma (%2C) into natural comma.
		// Since OWM API requires city ids delimited by hardcoded comma.
		// This is a way of hack, but it is due to the informal format
		//   of the API (using commas).
		$queryParams = str_replace('%2C', ',', $queryParams);

		return $base .= '?' . $queryParams;
	}

	/**
	 * Function: request
	 * Request the URL for different contents.
	 * @param string $url The API URL to request to
	 * @return array|boolean The response decoded in array or false otherwise
	 */
	function request($url) {
		$curlHandle = curl_init();
		curl_setopt_array($curlHandle, array(
			CURLOPT_RETURNTRANSFER => 1, //ne pas afficher directement le résultat de la requête(défault), mais le retourner
			CURLOPT_URL            => $url //Spécifier URL à requêter
		));
		$result = curl_exec($curlHandle);
		return !$result //if else
			? false
			: json_decode($result, self::FORCE_ASSOC);
	}

}

// END /lib/RequestOpenWeatherMap.class.php
