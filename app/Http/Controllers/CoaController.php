<?php

namespace App\Http\Controllers;

use App\Models\coa;
use App\Http\Requests\StorecoaRequest;
use App\Http\Requests\UpdatecoaRequest;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $coa = Coa::all();
        return view('coa/view',
                        [ 
                            'coa'=>$coa,
                            'title'=>'contoh m2',
                            'nama'=>'Sarah Al Arroya'
                        ]
                    ); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorecoaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(coa $coa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(coa $coa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecoaRequest $request, coa $coa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(coa $coa)
    {
        //
    }
}
