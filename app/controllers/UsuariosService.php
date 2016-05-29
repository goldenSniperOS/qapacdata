<?php

/**
* 
*/
class UsuariosService
{
	public function sumate($user_id,$id_comunidad){
		Header::allowAccess();
		$exists = UsuarioComunidad::where('objectID',$user_id)->where('id_comunidad',$id_comunidad)->get();
		if(empty($exists)){
			UsuarioComunidad::create([
				'objectID' => $user_id,
				'id_comunidad' => $id_comunidad
			]);
			Response::json(true);
		}else{
			Response::json(false);
		}
		
	}

	public function getComunidadesDe($user_id){
		Header::allowAccess();
		$comunidades = UsuarioComunidad::join('comunidad','comunidad.id','=','id_comunidad')->where('objectID',$user_id)->get();
		Response::json($comunidades);
	}

	
}