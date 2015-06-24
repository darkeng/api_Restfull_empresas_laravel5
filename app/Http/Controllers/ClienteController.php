<?php

namespace app_entregas\Http\Controllers;

use Illuminate\Http\Request;

use app_entregas\Http\Requests;
use app_entregas\Http\Controllers\Controller;

// Necesitaremos el modelo Avion para ciertas tareas.
use app_entregas\Cliente;

// Activamos el uso de las funciones de caché.
use Illuminate\Support\Facades\Cache;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Devolverá todos los aviones.
        // return "Mostrando todos los aviones de la base de datos.";
        // return Avion::all();  No es lo más correcto por que se devolverían todos los registros. Se recomienda usar Filtros.
        // Se debería devolver un objeto con una propiedad como mínimo data y el array de resultados en esa propiedad.
        // A su vez también es necesario devolver el código HTTP de la respuesta.
        //php http://elbauldelprogramador.com/buenas-practicas-para-el-diseno-de-una-api-RESTful-pragmatica/
        // https://cloud.google.com/storage/docs/json_api/v1/status-codes
        
        // Caché se actualizará con nuevos datos cada 15 segundos.
        // cacheaviones es la clave con la que se almacenarán 
        // los registros obtenidos de Avion::all()
        // El segundo parámetro son los minutos.
        $clientesPainados=Cliente::simplePaginate(10);
        /*$clientes=Cache::remember('cacheclientes',15/60,function()
        {
            // Para la paginación en Laravel se usa "Paginator"
            // En lugar de devolver 
            // return Avion::all();
            // devolveremos return Avion::paginate();
            // 
            // Este método paginate() está orientado a interfaces gráficas. 
            // Paginator tiene un método llamado render() que permite construir
            // los enlaces a página siguiente, anterior, etc..
            // Para la API RESTFUL usaremos un método más sencillo llamado simplePaginate() que
            // aporta la misma funcionalidad
            return Cliente::simplePaginate(10);  // Paginamos cada 10 elementos.
 
        });*/

        // Respuesta sin cache
        //return response()->json(['status'=>'ok','data'=>Avion::all()], 200);

        // Devolvemos el JSON usando caché.
        // return response()->json(['status'=>'ok', 'data'=>$aviones],200);
 
        // Con la paginación lo haremos de la siguiente forma:
        // Devolviendo también la URL a l
        return response()->json(['status'=>'ok', 'siguiente'=>$clientesPainados->nextPageUrl(),'anterior'=>$clientesPainados->previousPageUrl(),'data'=>$clientesPainados->items()],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // return "Se muestra Avion con id: $id";
        // Buscamos un avion por el id.
        $cliente=Cliente::find($id);
 
        // Si no existe ese avion devolvemos un error.
        if (!$cliente)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un cliente con ese código.'.$id])],404);
        }
 
        return response()->json(['status'=>'ok','data'=>$cliente],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return "Elimina el cliente con id: $id";
    }
}
