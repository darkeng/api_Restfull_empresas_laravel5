<?php

use Illuminate\Database\Seeder;
// Hace uso del modelo de Empresa.
use App\Empresa;

// Le indicamos que utilice también Faker.
// Información sobre Faker: https://github.com/fzaninotto/Faker
use Faker\Factory as Faker;

class EmpresaSeeder extends Seeder
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
 
        // Creamos un bucle para cubrir 50 empresas.
        for ($i=0; $i<50; $i++)
        {
            $ncompany=$faker->company();
            // Cuando llamamos al método create del Modelo Empresa 
            // se está creando una nueva fila en la tabla.
            Empresa::create(
                [
                    'nombre'=>$ncompany,
                    'direccion'=>$faker->address(),
                    'telefono'=>$faker->randomNumber(8), // de 8 dígitos como máximo.
                    'correo' =>str_replace(" ", "", $ncompany)."@".$faker->safeEmailDomain()
                ]
            );
        }
    }
}
