var app = angular.module('weatherDashboard', ["ngResource", "ngRoute"]);
app.config(function ($routeProvider) {
	$routeProvider //routeur front-end
		.when('/', {
			controller: 'MainController',
			templateUrl: 'views/home.html'
		})
		.when('/settings', {
			controller: 'MainController',
			templateUrl: 'views/settings.html'
		})
		.otherwise({
			redirectTo: '/'
		});
});
