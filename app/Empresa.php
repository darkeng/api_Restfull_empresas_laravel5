<?php

namespace app_entregas;

use Illuminate\Database\Eloquent\Model;
// Generalmente cada vez que creamos una clase tenemos que indicar el espacio de nombres
// dónde la estamos creando y suele coincidir con el nombre del directorio.
// El nombre del namespace debe comenzar por UNA LETRA MAYÚSCULA.
 
// Para más información ver contenido clase Model.php (CTRL + P en Sublime) de Eloquent para ver los atributos disponibles.
// Documentación completa de Eloquent ORM en: http://laravel.com/docs/5.0/eloquent
class Empresa extends Model
{
    // Nombre de la tabla en MySQL.
	protected $table="empresas";
 
	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('nombre','direccion','telefono', 'correo');
 
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at']; 
 
	// Definimos a continuación la relación de esta tabla con otras.
	// Ejemplos de relaciones:
	// 1 usuario tiene 1 teléfono   ->hasOne()
	// 1 teléfono pertenece a 1 usuario   ->belongsTo()
	// 1 post tiene muchos comentarios  -> hasMany()
	// 1 comentario pertenece a 1 post ->belongsTo()
	// 1 usuario puede tener muchos roles  ->belongsToMany()
	//  etc..
 
	// Relación de Espresas con Clientes:
	public function clientes()
	{	
		// 1 Empresa tiene muchos clientes
		// $this hace referencia al objeto que tengamos en ese momento de la Empresa.
		return $this->hasMany('app_entregas\Cliente');
	}
}
