<?php

use Illuminate\Database\Seeder;
// Hace uso del modelo de Empresa.
use App\Empresa;
// Hace uso del modelo de Cliente.
use App\Cliente;

// Le indicamos que utilice también Faker.
// Información sobre Faker: https://github.com/fzaninotto/Faker
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creamos una instancia de Faker
        $faker = Faker::create('es_ES');
 
        // Para cubrir los clientes tenemos que tener en cuenta que empresas tenemos.
        // Para que la clave foránea no nos de problemas.
        // Averiguamos cuantas empresas hay en la tabla.
        $cuantas= Empresa::all()->count();
 
        // Creamos un bucle para cubrir 200 clientes:
        for ($i=0; $i<200; $i++)
        {
            // Cuando llamamos al método create del Modelo Cliente 
            // se está creando una nueva fila en la tabla.
            $namec=$faker->firstName();
            $apell=$faker->lastName();
            Cliente::create(
                [
                 'nombre'=>$namec,
                 'apellido'=>$apell,
                 'telefono'=>$faker->randomNumber(8),
                 'correo'=>$namec.'.'.$apell.'@'.$faker->freeEmailDomain(),  // nombre.apellido@dominio.algo
                 'direccion'=>$faker->address(),
                 'empresa_id'=>$faker->numberBetween(1,$cuantas)
                ]
            );
        }
    }
}
