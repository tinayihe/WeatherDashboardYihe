<?php

// Require Slim framework
require ('../../vendor/autoload.php'); //autoload de slim

// Create a Slim object $app
$app = new \Slim\Slim(); //Slim a son propre espace de nom

// Create a DB object to manipulate the database
$app->db = new DB(); //injection de dépendence, fait partie d'un attribut de app

// Create a RequestOpenWeatherMap object to manipulate the request OWM
$app->requestOWM = new RequestOpenWeatherMap();

// Get cities ant theirs weather informations
$app->get('/cities', function() use($app) { //RESTful API, à demander depuis le front-end par AJAX
	$app->response->headers->set("Content-Type","application/json"); //informer le front-end que la réponder est en json
	$cities = $app->db->get();
	//var_dump($cities);die;
	$result = $app->requestOWM->requestCitiesWeather($cities);
	foreach ($cities as $i => $city) {
		$cities[$i]['temp'] = $result['list'][$i]['main']['temp'];
		$cities[$i]['weather'] = $result['list'][$i]['weather'][0];
	}
	echo json_encode($cities);
});

// Search a city that the user want to follow its weather
$app->get('/search', function() use($app) {
	$cityName = $app->request->get('q'); //équivalent à $_(underscore)GET['q'], l'attribut request de l'app est juste un conteneur de la requête défini par slim
	$result = $app->requestOWM->requestCityIdByName($cityName);
	echo json_encode($result);
});

// Add a city that the user choose to follow its weather
$app->post('/cities', function() use($app) {
	// Try to detect if the content type of the request is json
	$posTypeJSON = strpos( //chercher la position d'une chaine de caractère dans une autre
		strtolower($app->request->getContentType()), //Peut-êter autre format
		'application/json'
	);
	$isJSON = ($posTypeJSON !== false); //0 = false
	
	if ($isJSON) {
		// The params will be sent in a json string, decode it
		$city = json_decode(
			$app->request->getBody(),//Le contenu transmis depuis Angular ne peut pas être identifié par l'objet
			RequestOpenWeatherMap::FORCE_ASSOC //request de slim donc il faut surtout décoder le corps de la requete
		);
	} else {
		// Try to retrieve the params in Slim's traditional way
		$city = array(
			'city_name' => $app->request->post('city_name'), //normalement slim peut identifier les paramètres envoyés
			'city_id'   => $app->request->post('city_id')  //via AJAX, donc on laise quand même la manière traditionnel
		);
	}
	//var_dump($city);
	$resultID = $app->db->insert($city['city_name'], $city['city_id']);

	$app->response->headers->set("Content-Type","application/json");
	if ($resultID === false) {
		echo json_encode(array('error' => 'Failed to add the city.'));
	} else {
		$city['id'] = $resultID;
		return json_encode($city);
	}
});

// Remove a city
$app->delete('/cities/:id',function($id) use($app){
	$app->response->headers->set("Content-Type","application/json");
	$app->db->delete($id);
});

$app->run();

// END /public/api/index.php
