## Ejemplo: Api Restfull con Laravel PHP Framework

Este ejemplo se crean dos tablas en una base de datos mySql "empresas y clientes" de las cuales una empresa puede tener varios clientes, pero un cliente solo puede tener una empresa.
Para la autenticacion de clientes se utliza oauth2-laravel-server.

## Salida del comando "php artisan route:list"
+--------+----------+--------------------------------------------------+------------------------------------+----------------------------------------------------------------+------------+
| Domain | Method   | URI                                              | Name                               | Action                                                         | Middleware |
+--------+----------+--------------------------------------------------+------------------------------------+----------------------------------------------------------------+------------+
|        | GET|HEAD | api/v1.0/empresas                                | api.v1.0.empresas.index            | app_entregas\Http\Controllers\EmpresaController@index          |            |
|        | POST     | api/v1.0/empresas                                | api.v1.0.empresas.store            | app_entregas\Http\Controllers\EmpresaController@store          | oauth2     |
|        | GET|HEAD | api/v1.0/empresas/{empresas}                     | api.v1.0.empresas.show             | app_entregas\Http\Controllers\EmpresaController@show           |            |
|        | PUT      | api/v1.0/empresas/{empresas}                     | api.v1.0.empresas.update           | app_entregas\Http\Controllers\EmpresaController@update         | oauth2     |
|        | PATCH    | api/v1.0/empresas/{empresas}                     |                                    | app_entregas\Http\Controllers\EmpresaController@update         | oauth2     |
|        | DELETE   | api/v1.0/empresas/{empresas}                     | api.v1.0.empresas.destroy          | app_entregas\Http\Controllers\EmpresaController@destroy        | oauth2     |
|        | GET|HEAD | api/v1.0/clientes                                | api.v1.0.clientes.index            | app_entregas\Http\Controllers\ClienteController@index          |            |
|        | GET|HEAD | api/v1.0/clientes/{clientes}                     | api.v1.0.clientes.show             | app_entregas\Http\Controllers\ClienteController@show           |            |
|        | GET|HEAD | api/v1.0/empresas/{empresas}/clientes            | api.v1.0.empresas.clientes.index   | app_entregas\Http\Controllers\EmpresaClienteController@index   |            |
|        | POST     | api/v1.0/empresas/{empresas}/clientes            | api.v1.0.empresas.clientes.store   | app_entregas\Http\Controllers\EmpresaClienteController@store   | oauth2     |
|        | PUT      | api/v1.0/empresas/{empresas}/clientes/{clientes} | api.v1.0.empresas.clientes.update  | app_entregas\Http\Controllers\EmpresaClienteController@update  | oauth2     |
|        | PATCH    | api/v1.0/empresas/{empresas}/clientes/{clientes} |                                    | app_entregas\Http\Controllers\EmpresaClienteController@update  | oauth2     |
|        | DELETE   | api/v1.0/empresas/{empresas}/clientes/{clientes} | api.v1.0.empresas.clientes.destroy | app_entregas\Http\Controllers\EmpresaClienteController@destroy | oauth2     |
|        | POST     | oauth2/access_token                              |                                    | Closure                                                        |            |
|        | GET|HEAD | /                                                |                                    | Closure                                                        |            |
+--------+----------+--------------------------------------------------+------------------------------------+----------------------------------------------------------------+------------+
