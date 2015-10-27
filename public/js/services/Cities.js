app.factory('Cities', function($resource){
	return $resource('/api/cities/:id', {id:'@id'}); //envoyer requête HTTP selon opération
});
