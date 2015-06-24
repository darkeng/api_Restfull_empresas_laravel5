<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

// Hacemos uso del modelo User.
use App\User;
use App\Cliente;
use App\Empresa;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $this->call('EmpresaSeeder');
        $this->call('ClienteSeeder');
        // $this->call('UserTableSeeder');
        
        // Solo queremos un único usuario en la tabla, así que truncamos primero la tabla
        // Para luego rellenarla con los registros.
        User::truncate();
 
        // LLamamos al seeder de Users.
        $this->call('UserSeeder');

        Model::reguard();
    }
}
