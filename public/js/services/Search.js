app.factory('Search', function($resource){
	return $resource('/api/search/:city_name');
});
