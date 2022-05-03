<?php

namespace App\Http\Controllers;

use App\tipoUsuario;
use App\menuPadre;
use Illuminate\Http\Request;



class menuPadreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   
    public function index()
    {
        //
        $menuPadre=menuPadre::orderBy('id','DESC')->paginate(3);
        return view('menuPadre.index',compact('menuPadre')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tipoUsuario = tipoUsuario::where( 'estado', 'A' )->get();
        return view('menuPadre.create',compact('tipoUsuario',$tipoUsuario));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
       $input=$request->all();
        $this->validate($request, [
            'nombreMenu' => 'required',
            'estado' => 'required',
            'perfilUsuario' => 'required',
        ]);

        $perfilUsuario = $request->input('perfilUsuario');
        foreach( $perfilUsuario as $idUsuario) {
         $input['perfilUsuario'] = $idUsuario;
         menuPadre::create($input);
        }
        
        return redirect()->route('menuPadre.index')->with('success','Registro creado satisfactoriamente');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
