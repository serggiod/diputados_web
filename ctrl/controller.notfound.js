angular
	.module('diputadosweb')
	.controller(
		'notFoundCtrl',
		function($scope,$http,$location){
			
			$scope.fotografia = 'indefinido.jpg';
			$scope.nombre     = 'Nombre';
			$scope.apellido   = 'Apellido';

	   	}
	);
