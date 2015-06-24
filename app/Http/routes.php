<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Versionado de la ruta
Route::group(array('prefix'=>'api/v1.0'),function()
{
	// resource recibe dos parámetros principales(URI del recurso, Controlador que gestionará las peticiones)
	Route::resource('empresas','EmpresaController',['except'=>['edit','create'] ]);	// Todos los métodos menos Edit y create que mostraría un formulario de edición.
	 
	// Si queremos dar  la funcionalidad de ver todos los clientes tendremos que crear una ruta específica.
	// Pero de clientes solamente necesitamos solamente los métodos index y show.
	// Lo correcto sería hacerlo así:
	Route::resource('clientes','ClienteController',[ 'only'=>['index','show'] ]); // El resto se gestionan en EmpresaClienteController
	 
	// Como la clase principal es empresas y un cliente no se puede crear si no le indicamos una empresa, 
	// entonces necesitaremos crear lo que se conoce como  "Recurso Anidado" de empresas con clientes.
	// Definición del recurso anidado:
	Route::resource('empresas.clientes','EmpresaClienteController',[ 'except'=>['show','edit','create'] ]);
});
Route::post('oauth2/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('/', function () {
    return view('welcome');
});
