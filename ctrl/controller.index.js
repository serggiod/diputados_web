angular.module('diputadosweb')
.controller(
	'indexCtrl',
	function($scope,$http,$location){
		// Funcion: Inicializar
		$scope.init = function(){
			$('#cargando').hide();
			$['#overlay'].hide();
		};
		$scope.init();
   	}
);
