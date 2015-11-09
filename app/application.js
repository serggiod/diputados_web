angular.module('diputadosweb',['ngRoute']);

/* RUTAS */
angular.module('diputadosweb').config(function($routeProvider){
    $routeProvider

    // Pantalla de logueo.
    .when('/:id',{
        templateUrl:'view/index.html',
        controller:'indexCtrl'
    })
    .when('/notFound',{
        templateUrl:'views/view.notfound.html',
        controller:'notFoundCtrl'
    })
    .otherwise({redirectTo:'/notFound'});
});

/*
angular.module('diputadosweb').config(function($routeProvider){
    $routeProvider

    // Pantalla de logueo.
    .when('/',{
        templateUrl:'view/view.login.html',
        controller:'loginCtrl'
    })
    .when('/login',{
        templateUrl:'view/view.login.html',
        controller:'loginCtrl'
    })

    // Menu archivos.
    .when('/logout',{
        templateUrl:'view/view.logout.html',
        controller:'logoutCtrl'
    })
    .when('/password',{
        templateUrl:'view/view.password.html',
        controller:'passwordCtrl'
    })

    // Menú Herramientas.
    .when('/tv',{
        templateUrl:'view/view.tv_msg.html',
        controller:'TvMsgCtrl'
    })
    .when('/mensajes/:tvId',{
        templateUrl:'view/view.mensajes.html',
        controller:'mensajesCtrl'
    })
    .when('/audios/:tvId',{
        templateUrl:'view/view.audios.html',
        controller:'audiosCtrl'
    })
    .when('/lanzar/:tvId',{
        templateUrl:'view/view.lanzar.html',
        controller:'lanzarCtrl'
    })
    .when('/usuarios',{
        templateUrl:'view/view.usuarios.html',
        controller:'usuariosCtrl'
    })
    .when('/imagenes',{
        templateUrl:'view/view.imagenes.html',
        controller:'imagenesCtrl'
    })
    .when('/fuentes',{
        templateUrl:'view/view.fuentes.html',
        controller:'fuentesCtrl'
    })
    .when('/mp3',{
        templateUrl:'view/view.mp3.html',
        controller:'mp3Ctrl'
    })

    // Menú ayuda.
    .when('/acerca',{
        templateUrl:'view/view.acerca.html',
        controller:'acercaCtrl'
    })
    .when('/manual',{
        templateUrl:'view/view.manual.html',
        controller:'manualCtrl'
    })
    .otherwise({redirectTo:'/login'});
});
*/