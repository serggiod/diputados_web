angular
	.module('diputadosweb')
	.controller(
		'diputadoCtrl',
		function($scope){
			$scope.id         = null;
			$scope.fotografia = 'indefinido.jpg';
			$scope.nombre     = 'Nombre del Diputado';
			$scope.apellido   = 'Apellido del Diputado';
			$scope.mandato    = '2000-3000';
			$scope.bloque     = 'Boque';
			$scope.email      = '';
			$scope.telefono   = '0388 4239200';
			$scope.paginaweb  = '';
			$scope.facebook   = '';
			$scope.twitter    = '';
			$scope.yotube     = '';
			$scope.comisones  = {};
		}
	);