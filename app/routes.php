<?php


//Comunidades

Route::get('save-comunidades','ComunidadesService@saveComunidades');
Route::get('total-comunidades','ComunidadesService@totalComunidades');
Route::get('detalle-comunidad/{id}','ComunidadesService@getDetailComunidad');

//Usuarios
Route::get('sumate/{userid}/{idcomunidad}','UsuariosService@sumate');
Route::get('getcomunidades/{user_id}','UsuariosService@getComunidadesDe');