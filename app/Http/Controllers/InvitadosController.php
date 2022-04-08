<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invitado;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class InvitadosController extends Controller
{
    //
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Listamos todos los Invitadoos
        return Invitado::get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('nombre_invitado','usuario_fk');
        $validator = Validator::make($data, [
            'nombre_invitado' => 'required|max:50|string',
            'usuario_fk'=>'string',
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $q="SELECT
        i.id AS IdInv
      FROM invitados AS i 
      INNER JOIN users AS us
        ON i.usuario_fk = us.id
        where usuario_fk=(SELECT id FROM users WHERE email='".$request->email."');";
        $idInvitado = DB::select($q);
        $idInv =$idInvitado[0];

        //Creamos el Invitadoo en la BD
        $val = Invitado::create([
            'nombre_invitado' => $request->nombre_invitado,
            'usuario_fk'=>$idInv->IdInv,
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Invitado registrado',
            'data' => $val
        ], Response::HTTP_OK);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invitado  $Invitado
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Bucamos el Invitadoo
        $Invitado = Invitado::find($id);
        //Si el Invitadoo no existe devolvemos error no encontrado
        if (!$Invitado) {
            return response()->json([
                'message' => 'Invitado no encontrado'
            ], 404);
        }
        //Si hay Invitadoo lo devolvemos
        return $Invitado;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invitado  $Invitado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre_invitado','usuario_fk');
        $validator = Validator::make($data, [
            'nombre_invitado' => 'required|max:50|string',
            'usuario_fk'=>'required|string',
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el Invitadoo
        $Invitado = Invitado::findOrfail($id);
        //Actualizamos el Invitadoo.
        $Invitado->update([
            'nombre_invitado' => $request->nombre_invitado,
            'usuario_fk'=>$request->usuario_fk,
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Invitado actualizado',
            'data' => $Invitado
        ], Response::HTTP_CREATED);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invitado  $Invitado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el Invitadoo
        $Invitado = Invitado::findOrfail($id);
        //Eliminamos el Invitadoo
        $Invitado->delete();
        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Invitado eliminado'
        ], Response::HTTP_OK);
    }
}
