angular.module('diputadosweb')
.controller(
	'notFoundCtrl',
	function($scope,$http,$location){
		// Funcion: Inicializar
		$scope.init = function(){
			console.log('Hola Mundo');
			$('#cargando').hide();
			$['#overlay'].hide();
		};
		$scope.init();
   	}
);
