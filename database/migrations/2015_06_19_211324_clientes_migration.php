<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientesMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('telefono');
            $table->string('correo');
            $table->string('direccion');
 
            // Añadimos la clave foránea con Empresas. empresa_id
            // Acordarse de añadir al array protected $fillable del fichero de modelo "cliente.php" la nueva columna:
            // protected $fillable = array('nombre','apellido','telefono','correo', 'direccion', empresa_id');
            $table->integer('empresa_id')->unsigned();
 
            // Indicamos cual es la clave foránea de esta tabla:
            $table->foreign('empresa_id')->references('id')->on('empresas');
 
            // Para que también cree automáticamente los campos timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clientes');
    }
}
