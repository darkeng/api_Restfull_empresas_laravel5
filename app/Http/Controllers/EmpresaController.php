<?php

namespace app_entregas\Http\Controllers;

use Illuminate\Http\Request;

use app_entregas\Http\Requests;
use app_entregas\Http\Controllers\Controller;

// Necesitaremos el modelo Empresa para ciertas tareas.
use app_entregas\Empresa;

// Necesitamos la clase Response para crear la respuesta especial con la cabecera de localización en el método Store()
use Response;

// Activamos el uso de las funciones de caché.
use Illuminate\Support\Facades\Cache;

class EmpresaController extends Controller
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
    public function index()
    {
        // return "En el index de Empresa.";
        // Devolvemos un JSON con todas las Empresas.
        // return Empresa::all();
 
        // Caché se actualizará con nuevos datos cada 15 segundos.
        // cacheEmpresas es la clave con la que se almacenarán 
        // los registros obtenidos de Empresa::all()
        // El segundo parámetro son los minutos.
        $empresasPaginadas=Empresa::simplePaginate(10);
        /*$Empresas=Cache::remember('cacheEmpresas',15/60,function()
        {
            // Para la paginación en Laravel se usa "Paginator"
            // En lugar de devolver 
            // return Empresa::all();
            // devolveremos return Empresa::paginate();
            // 
            // Este método paginate() está orientado a interfaces gráficas. 
            // Paginator tiene un método llamado render() que permite construir
            // los enlaces a página siguiente, anterior, etc..
            // Para la API RESTFUL usaremos un método más sencillo llamado simplePaginate() que
            // aporta la misma funcionalidad
            return Empresa::simplePaginate(10);  // Paginamos cada 10 elementos.
 
        });*/
 
        // Para devolver un JSON con código de respuesta HTTP sin caché.
        // return response()->json(['status'=>'ok', 'data'=>Empresa::all()],200);
 
        // Devolvemos el JSON usando caché.
        // return response()->json(['status'=>'ok', 'data'=>$Empresas],200);
 
        // Con la paginación lo haremos de la siguiente forma:
        // Devolviendo también la URL a l
        return response()->json(['status'=>'ok', 'siguiente'=>$empresasPaginadas->nextPageUrl(),'anterior'=>$empresasPaginadas->previousPageUrl(),'data'=>$empresasPaginadas->items()],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return "Muestra formulario para crear un Empresa";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    // Pasamos como parámetro al método store todas las variables recibidas de tipo Request
    // utilizando inyección de dependencias (nuevo en Laravel 5)
    // Para acceder a Request necesitamos asegurarnos que está cargado use Illuminate\Http\Request;
    // Información sobre Request en: http://laravel.com/docs/5.0/requests 
    // Ejemplo de uso de Request:  $request->input('name');
    public function store(Request $request)
    {
 
        // Primero comprobaremos si estamos recibiendo todos los campos.
        if (!$request->input('nombre') || !$request->input('direccion') || !$request->input('telefono') || !$request->input('correo'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de creacion.'])],422);
        }
 
        // Insertamos una fila en Empresa con create pasándole todos los datos recibidos.
        // En $request->all() tendremos todos los campos del formulario recibidos.
        $nuevaEmpresa=Empresa::create($request->all());
 
        // Más información sobre respuestas en http://jsonapi.org/format/
        // Devolvemos el código HTTP 201 Created – [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
        $response = Response::make(json_encode(['data'=>$nuevaEmpresa]), 201)->header('Location', 'http://darkeng.my/laravel/api/v1.0/empresas/'.$nuevaEmpresa->id)->header('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // return "Se muestra Empresa con id: $id";
        // Buscamos un Empresa por el id.
        $Empresa=Empresa::find($id);
 
        // Si no existe ese Empresa devolvemos un error.
        if (!$Empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un Empresa con ese código.'.$id])],404);
        }
 
        return response()->json(['status'=>'ok','data'=>$Empresa],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return "Se muestra un formulario para editar los datos del Empresa con id $id";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Comprobamos si el Empresa que nos están pasando existe o no.
        $Empresa=Empresa::find($id);
 
        // Si no existe ese Empresa devolvemos un error.
        if (!$Empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un Empresa con ese código.'])],404);
        }       
 
        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $direccion=$request->input('direccion');
        $telefono=$request->input('telefono');
        $correo=$request->input('correo');

        // Necesitamos detectar si estamos recibiendo una petición PUT o PATCH.
        // El método de la petición se sabe a través de $request->method();
        if ($request->method() === 'PATCH')
        {
            // Creamos una bandera para controlar si se ha modificado algún dato en el método PATCH.
            $bandera = false;
 
            // Actualización parcial de campos.
            if ($nombre)
            {
                $Empresa->nombre = $nombre;
                $bandera=true;
            }
 
            if ($direccion)
            {
                $Empresa->direccion = $direccion;
                $bandera=true;
            }
 
 
            if ($telefono)
            {
                $Empresa->telefono = $telefono;
                $bandera=true;
            }

            if ($correo)
            {
                $Empresa->correo = $correo;
                $bandera=true;
            }
 
            if ($bandera)
            {
                // Almacenamos en la base de datos el registro.
                $Empresa->save();
                return response()->json(['status'=>'ok','data'=>$Empresa], 200);
            }
            else
            {
                // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
                // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
                return response()->json(['errors'=>array(['code'=>304,'message'=>'No se ha modificado ningún dato de la Empresa.'])],304);
            }
        }
 
 
        // Si el método no es PATCH entonces es PUT y tendremos que actualizar todos los datos.
        if (!$nombre || !$direccion || !$telefono || !$correo)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan valores para completar el procesamiento.'])],422);
        }
 
        $Empresa->nombre = $nombre;
        $Empresa->direccion = $direccion;
        $Empresa->telefono = $telefono;
        $Empresa->correo = $correo;
 
        // Almacenamos en la base de datos el registro.
        $Empresa->save();
        return response()->json(['status'=>'ok','data'=>$Empresa], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // Primero eliminaremos todos los clientes de un Empresa y luego el Empresa en si mismo.
        // Comprobamos si el Empresa que nos están pasando existe o no.
        $Empresa=Empresa::find($id);
 
        // Si no existe ese Empresa devolvemos un error.
        if (!$Empresa)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra una Empresa con ese código.'])],404);
        }       
 
        // El Empresa existe entonces buscamos todos los clientes asociados a ese Empresa.
        $clientes = $Empresa->clientes; // Sin paréntesis obtenemos el array de todos los clientes.
 
        // Comprobamos si tiene clientes ese Empresa.
        if (sizeof($clientes) > 0)
        {
            // Devolveremos un código 409 Conflict - [Conflicto] Cuando hay algún conflicto al procesar una petición, por ejemplo en PATCH, POST o DELETE.
            return response()->json(['code'=>409,'message'=>'Este Empresa posee clientes y no puede ser eliminado.'],409);
        }
 
        // Procedemos por lo tanto a eliminar el Empresa.
        $Empresa->delete();
 
        // Se usa el código 204 No Content – [Sin Contenido] Respuesta a una petición exitosa que no devuelve un body (como una petición DELETE)
        // Este código 204 no devuelve body así que si queremos que se vea el mensaje tendríamos que usar un código de respuesta HTTP 200.
        return response()->json(['code'=>204,'message'=>'Se ha eliminado la Empresa correctamente.'],204);
    }
}
