<?php

namespace app_entregas\Http\Controllers;

use Illuminate\Http\Request;

use app_entregas\Http\Requests;
use app_entregas\Http\Controllers\Controller;

// Necesita los dos nombres empresa y cliente
use app_entregas\Empresa;
use app_entregas\Cliente;

// Necesitamos la clase Response para crear la respuesta especial con la cabecera de localización en el método Store()
use Response;

// Activamos uso de caché.
use Illuminate\Support\Facades\Cache;

class EmpresaClienteController extends Controller
{
    // Configuramos en el constructor del controlador la autenticación usando el Middleware oauth2,
    // pero solamente para los métodos de crear, actualizar y borrar.
    public function __construct()
    {
        $this->middleware('oauth2',['only'=>['store','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($idEmpresa)
    {
        
        // Devolverá todos los clientees.
        //return "Mostrando los clientees del empresa con Id $idEmpresa";
        $empresa=Empresa::find($idEmpresa);
 
        if (! $empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una empresa con ese código.'])],404);
        }
 
        // Activamos la caché de los resultados.
        // Como el closure necesita acceder a la variable $empresa tenemos que pasársela con use($empresa)
        // Para acceder a los nombres no haría falta puesto que son accesibles a nivel global dentro de la clase.
        //  Cache::remember('tabla', $minutes, function()
        /*$clientesEmpr=Cache::remember('cacheClienteEmpr',2, function() use ($empresa)
        {
            // Caché válida durante 2 minutos.
            return $empresa->clientes()->get();
        });*/
 
        // Respuesta con caché:
        //return response()->json(['status'=>'ok','data'=>$clientesEmpr],200);
 
        // Respuesta sin caché:
        return response()->json(['status'=>'ok','data'=>$empresa->clientes()->get()],200);
        //return response()->json(['status'=>'ok','data'=>$empresa->clientes],200);
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($idEmpresa)
    {
        //
        return "Se muestra formulario para crear un cliente para la empresa $idEmpresa.";
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request,$idEmpresa)
    {
        /* Necesitaremos el empresa_id que lo recibimos en la ruta
         #Serie (auto incremental)
         */
 
        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('nombre') || !$request->input('apellido') || !$request->input('telefono') || !$request->input('direccion'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de insercion.'])],422);
        }
 
        // Buscamos el empresa.
        $empresa= Empresa::find($idEmpresa);
 
        // Si no existe el empresa que le hemos pasado mostramos otro código de error de no encontrado.
        if (!$empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una empresa con ese código.'])],404);
        }
 
        // Si el empresa existe entonces lo almacenamos.
        // Insertamos una fila en clientees con create pasándole todos los datos recibidos.
        $nuevoCliente=$empresa->clientes()->create($request->all());
 
        // Más información sobre respuestas en http://jsonapi.org/format/
        // Devolvemos el código HTTP 201 Created – [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
        $response = Response::make(json_encode(['data'=>$nuevoCliente]), 201)->header('Location', 'http://darkeng.my/laravel/clientes/'.$nuevoCliente->id)->header('Content-Type', 'application/json');
        return $response;
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($idEmpresa, $idCliente)
    {
        return "Se muestra el cliente $idCliente de la empresa $idEmpresa";
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($idEmpresa,$idCliente)
    {
        //
        return "Se muestra formulario para editar el cliente $idCliente de la empresa $idEmpresa";
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $idEmpresa, $idCliente)
    {
        // Comprobamos si el empresa que nos están pasando existe o no.
        $empresa=Empresa::find($idEmpresa);
 
        // Si no existe ese empresa devolvemos un error.
        if (!$empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una empresa con ese código.'])],404);
        }       
 
        // El empresa existe entonces buscamos el cliente que queremos editar asociado a ese empresa.
        $cliente = $empresa->clientes()->find($idCliente);
 
        // Si no existe ese cliente devolvemos un error.
        if (!$cliente)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un cliente con ese código asociado a la empresa.'])],404);
        }   
 
 
        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $apellido=$request->input('apellido');
        $telefono=$request->input('telefono');
        $direccion=$request->input('direccion');
 
        // Necesitamos detectar si estamos recibiendo una petición PUT o PATCH.
        // El método de la petición se sabe a través de $request->method();
        /*  nombre      apellido        telefono       direccion       Alcance */
        if ($request->method() === 'PATCH')
        {
            // Creamos una bandera para controlar si se ha modificado algún dato en el método PATCH.
            $bandera = false;
 
            // Actualización parcial de campos.
            if ($nombre)
            {
                $cliente->nombre = $nombre;
                $bandera=true;
            }
 
            if ($apellido)
            {
                $cliente->apellido = $apellido;
                $bandera=true;
            }
 
            if ($telefono)
            {
                $cliente->telefono = $telefono;
                $bandera=true;
            }
 
            if ($direccion)
            {
                $cliente->direccion = $direccion;
                $bandera=true;
            }
 
            if ($bandera)
            {
                // Almacenamos en la base de datos el registro.
                $cliente->save();
                return response()->json(['status'=>'ok','data'=>$cliente], 200);
            }
            else
            {
                // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
                // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
                return response()->json(['errors'=>array(['code'=>304,'message'=>'No se ha modificado ningún dato del cliente.'])],304);
            }
 
        }
 
        // Si el método no es PATCH entonces es PUT y tendremos que actualizar todos los datos.
        if (!$nombre || !$apellido || !$telefono || !$direccion)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan valores para completar el proceso.'])],422);
        }
 
        $cliente->nombre = $nombre;
        $cliente->apellido = $apellido;
        $cliente->telefono = $telefono;
        $cliente->direccion = $direccion;
 
        // Almacenamos en la base de datos el registro.
        $cliente->save();
 
        return response()->json(['status'=>'ok','data'=>$cliente], 200);
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($idEmpresa,$idcliente)
    {
        // Comprobamos si el empresa que nos están pasando existe o no.
        $empresa=empresa::find($idEmpresa);
 
        // Si no existe ese empresa devolvemos un error.
        if (!$empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una empresa con ese código.'])],404);
        }       
 
        // El empresa existe entonces buscamos el cliente que queremos borrar asociado a ese empresa.
        $cliente = $empresa->clientees()->find($idcliente);
 
        // Si no existe ese cliente devolvemos un error.
        if (!$cliente)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un cliente con ese código asociado a esa empresa.'])],404);
        }
 
        // Procedemos por lo tanto a eliminar el cliente.
        $cliente->delete();
 
        // Se usa el código 204 No Content – [Sin Contenido] Respuesta a una petición exitosa que no devuelve un body (como una petición DELETE)
        // Este código 204 no devuelve body así que si queremos que se vea el mensaje tendríamos que usar un código de respuesta HTTP 200.
        return response()->json(['code'=>204,'message'=>'Se ha eliminado el cliente correctamente.'],204);
    }
}
