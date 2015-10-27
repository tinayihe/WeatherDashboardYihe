app.controller("MainController", [
	"$scope", "$route", "Cities", "Search", //module et service, interne, externe ou personalisé
	function($scope, $route, Cities, Search) {

		// The cities followed by user and theirs weather informations.
		$scope.cities = Cities.query(); //get for liste. module de resource de Angular, cities est une liste des resources, chacun de ses éléments est aussi une resource
		
		// Indicator of showing the search message or not.
		$scope.showSearchMsg = false;

		// Name of cities which user wants to add to the list of followed cities.
		$scope.cityToSearch = {q: ''}; //JSON, q:key dans API de OWM

		// Add a city finded.
		$scope.addCity = function(city) {
			if (!city.id || !city.name) {
				alert(
					"The result from the remote API is incorret.\n" +
					"Please try another search."
				);
				$route.reload(); //actualiser
				return;
			}

			var cityInfo = {
				'city_name': city.name,
				'city_id': city.id
			}
			var cityToAdd = new Cities(cityInfo);
			cityToAdd.$save(); //post(créer nouvelle entrée) ou put(mettre à jours) méthode dans module resource
			$route.reload();
		};

		// Remove a city from the list of followed cities.
		$scope.deleteCity = function(city) {
			var sure = confirm('Do you want to delete ' + city.city_name + '?');
			if (sure) {
				city.$remove();
				$route.reload();
			}
		};
		
		// Search cities corresponding to the name of city
	    // which the user wants to follow.
		$scope.searchCity = function() {
			$scope.searchResults = Search.get($scope.cityToSearch, function() { //requête GET en AJAX, resource est facile à utiliser, sinon on peut aussi utiliser le module HTTP de Angular
				if ($scope.searchResults.count > 0) {
					$scope.searchMsg = 'Choose the city to add';
				} else {
					$scope.searchMsg = 'No results';
				}
				$scope.showSearchMsg = true;
			});
		}

		// Submit the search of city when enter is pressed
		$scope.submitSearch = function($event) {
			if ($event.keyCode === 13) {
				$scope.searchCity();
			}
		}

	}
]);
